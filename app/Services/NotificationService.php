<?php

namespace App\Services;

use App\Models\NotificationModel;
use App\Models\UserModel;

class NotificationService
{
    public function send(int $userId, string $type, string $title, string $body, array $extraData = []): void
    {
        $notifModel = new NotificationModel();
        $notifModel->insert([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'data'       => !empty($extraData) ? json_encode($extraData) : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->sendPush($userId, $title, $body, $extraData);
    }

    public function sendToGroup(int $groupId, string $type, string $title, string $body, array $extraData = []): void
    {
        $memberModel = model('FamilyMemberModel');
        $members = $memberModel->getMembersOfGroup($groupId);

        foreach ($members as $member) {
            $this->send($member->user_id, $type, $title, $body, $extraData);
        }
    }

    private function sendPush(int $userId, string $title, string $body, array $data = []): void
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user || empty($user->firebase_token)) {
            return;
        }

        $serviceAccountPath = WRITEPATH . 'firebase/service-account.json';
        if (!file_exists($serviceAccountPath)) {
            log_message('warning', '[LifeKnob] Firebase service account not found');
            return;
        }

        try {
            $accessToken = $this->getFirebaseAccessToken($serviceAccountPath);
            if (!$accessToken) return;

            $sa = json_decode(file_get_contents($serviceAccountPath), true);
            $projectId = $sa['project_id'] ?? '';

            $message = [
                'message' => [
                    'token' => $user->firebase_token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => array_map('strval', $data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => $data['type'] === 'emergency' ? 'emergency' : 'default',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            $ch = curl_init("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $accessToken,
                    'Content-Type: application/json',
                ],
                CURLOPT_POSTFIELDS     => json_encode($message),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 10,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                log_message('error', "[LifeKnob] FCM push failed (HTTP $httpCode): $response");
            }
        } catch (\Exception $e) {
            log_message('error', '[LifeKnob] Push notification error: ' . $e->getMessage());
        }
    }

    private function getFirebaseAccessToken(string $serviceAccountPath): ?string
    {
        $sa = json_decode(file_get_contents($serviceAccountPath), true);
        if (!$sa) return null;

        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = base64_encode(json_encode([
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => $sa['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "$header.$claims";
        $signature = '';
        openssl_sign($signingInput, $signature, $sa['private_key'], 'SHA256');
        $jwt = $signingInput . '.' . base64_encode($signature);

        $ch = curl_init($sa['token_uri']);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['access_token'] ?? null;
    }
}
