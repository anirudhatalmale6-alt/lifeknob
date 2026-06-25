<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CheckInModel;
use App\Models\CheckInSettingModel;

class WebCheckInController extends BaseController
{
    public function index()
    {
        $token = $this->request->getGet('token');
        $user = null;
        $lastCheckIn = null;
        $settings = null;

        if ($token) {
            $decoded = $this->decodeToken($token);
            if ($decoded) {
                $userModel = new UserModel();
                $user = $userModel->find($decoded['user_id']);

                if ($user) {
                    $checkInModel = new CheckInModel();
                    $lastCheckIn = $checkInModel->getLatestCheckIn($user->id);

                    $settingModel = new CheckInSettingModel();
                    $settings = $settingModel->getSettingsForUser($user->id);
                }
            }
        }

        return view('web_checkin', [
            'user'        => $user,
            'token'       => $token,
            'lastCheckIn' => $lastCheckIn,
            'settings'    => $settings,
        ]);
    }

    public function submit()
    {
        $token = $this->request->getPost('token');
        $type  = $this->request->getPost('type');

        if (!$token || !in_array($type, ['ok', 'help', 'emergency'])) {
            return redirect()->to('/checkin/web')->with('error', 'Invalid request');
        }

        $decoded = $this->decodeToken($token);
        if (!$decoded) {
            return redirect()->to('/checkin/web')->with('error', 'Invalid or expired token');
        }

        $userModel = new UserModel();
        $user = $userModel->find($decoded['user_id']);
        if (!$user) {
            return redirect()->to('/checkin/web')->with('error', 'User not found');
        }

        $checkInModel = new CheckInModel();
        $checkInModel->insert([
            'user_id'    => $user->id,
            'type'       => $type,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $userModel->updateLastSeen($user->id);

        $apiCheckIn = new \App\Controllers\Api\CheckInController();
        if ($type === 'help') {
            $this->createAlertForWeb($user->id, 'help', 'is requesting help (via web)!');
        } elseif ($type === 'emergency') {
            $this->createAlertForWeb($user->id, 'emergency', 'has triggered an EMERGENCY alert (via web)!');
        } else {
            $this->notifyFamilyOkWeb($user->id);
        }

        $messages = [
            'ok'        => 'Check-in recorded! Your family can see you are OK.',
            'help'      => 'Help request sent to your family!',
            'emergency' => 'Emergency alert sent! Help is on the way.',
        ];

        return redirect()->to('/checkin/web?token=' . $token)->with('success', $messages[$type]);
    }

    private function notifyFamilyOkWeb(int $elderId): void
    {
        $userModel = new UserModel();
        $elder = $userModel->find($elderId);
        if (!$elder) return;

        $notificationService = new \App\Services\NotificationService();
        $groups = model('FamilyGroupModel')->getGroupsForUser($elderId);

        foreach ($groups as $group) {
            $members = model('FamilyMemberModel')->getFamilyInGroup($group->id);
            foreach ($members as $member) {
                $notificationService->send(
                    $member->user_id, 'info',
                    $elder->name . ' checked in (web)',
                    $elder->name . ' pressed "I\'m OK" from the website.',
                    ['type' => 'ok_checkin', 'elder_id' => $elderId]
                );
            }
        }
    }

    private function createAlertForWeb(int $elderId, string $type, string $messageSuffix): void
    {
        $userModel = new UserModel();
        $elder = $userModel->find($elderId);
        if (!$elder) return;

        $alertModel = model('AlertModel');
        $notificationService = new \App\Services\NotificationService();
        $groups = model('FamilyGroupModel')->getGroupsForUser($elderId);

        foreach ($groups as $group) {
            $alertModel->insert([
                'group_id'   => $group->id,
                'elder_id'   => $elderId,
                'type'       => $type === 'help' ? 'help' : 'emergency',
                'message'    => $elder->name . ' ' . $messageSuffix,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $members = model('FamilyMemberModel')->getFamilyInGroup($group->id);
            foreach ($members as $member) {
                $priority = $type === 'emergency' ? 'EMERGENCY' : 'URGENT';
                $notificationService->send(
                    $member->user_id, 'alert',
                    "[$priority] " . $elder->name,
                    $elder->name . ' ' . $messageSuffix,
                    ['type' => $type, 'elder_id' => $elderId, 'group_id' => $group->id]
                );
            }
        }
    }

    private function decodeToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        $secret = getenv('JWT_SECRET') ?: 'lifeknob_secret_key_change_in_production';
        $signature = hash_hmac('sha256', "{$parts[0]}.{$parts[1]}", $secret);

        if ($signature !== $parts[2]) return null;

        $payload = json_decode(base64_decode($parts[1]), true);
        if (!$payload || ($payload['expires_at'] ?? 0) < time()) return null;

        return $payload;
    }
}
