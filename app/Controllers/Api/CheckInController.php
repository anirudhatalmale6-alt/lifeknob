<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CheckInModel;
use App\Models\AlertModel;
use App\Models\FamilyMemberModel;
use App\Models\UserModel;
use App\Services\NotificationService;

class CheckInController extends ResourceController
{
    protected $format = 'json';

    public function create()
    {
        $userId = $this->request->getPost('user_id');
        $type   = $this->request->getPost('type');

        if (!$userId || !in_array($type, ['ok', 'help', 'emergency'])) {
            return $this->failValidationErrors('user_id and valid type (ok/help/emergency) required');
        }

        $checkInModel = new CheckInModel();

        $data = [
            'user_id'    => $userId,
            'type'       => $type,
            'latitude'   => $this->request->getPost('latitude'),
            'longitude'  => $this->request->getPost('longitude'),
            'note'       => $this->request->getPost('note'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $checkInId = $checkInModel->insert($data);
        if (!$checkInId) {
            return $this->failServerError('Failed to save check-in');
        }

        $userModel = new UserModel();
        $userModel->updateLastSeen($userId);

        if ($type === 'ok') {
            $this->notifyFamilyOk($userId);
        } elseif ($type === 'help') {
            $this->createAlert($userId, 'help', 'is requesting help!');
        } elseif ($type === 'emergency') {
            $this->createAlert($userId, 'emergency', 'has triggered an EMERGENCY alert!');
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => $this->getResponseMessage($type),
            'data'    => ['check_in_id' => $checkInId],
        ]);
    }

    public function history()
    {
        $userId = $this->request->getGet('user_id');
        $limit  = (int)($this->request->getGet('limit') ?? 50);

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $checkInModel = new CheckInModel();
        $history = $checkInModel->getCheckInHistory($userId, $limit);

        return $this->respond([
            'status' => 'success',
            'data'   => $history,
        ]);
    }

    public function stats()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $checkInModel = new CheckInModel();
        $stats = $checkInModel->getCheckInStats($userId);

        return $this->respond([
            'status' => 'success',
            'data'   => $stats,
        ]);
    }

    public function latestForGroup()
    {
        $groupId = $this->request->getGet('group_id');

        if (!$groupId) {
            return $this->failValidationErrors('group_id required');
        }

        $familyMemberModel = new FamilyMemberModel();
        $elders = $familyMemberModel->getEldersInGroup($groupId);

        $checkInModel = new CheckInModel();
        $result = [];

        foreach ($elders as $elder) {
            $latest = $checkInModel->getLatestCheckIn($elder->user_id);
            $result[] = [
                'user_id'        => $elder->user_id,
                'name'           => $elder->name ?? 'Unknown',
                'latest_checkin' => $latest,
                'is_overdue'     => $this->isOverdue($elder->user_id, $latest),
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    private function notifyFamilyOk(int $elderId): void
    {
        $userModel = new UserModel();
        $elder = $userModel->find($elderId);
        if (!$elder) return;

        $familyMemberModel = new FamilyMemberModel();
        $notificationService = new NotificationService();

        $groups = model('FamilyGroupModel')->getGroupsForUser($elderId);
        foreach ($groups as $group) {
            $familyMembers = $familyMemberModel->getFamilyInGroup($group->id);
            foreach ($familyMembers as $member) {
                $notificationService->send(
                    $member->user_id,
                    'info',
                    $elder->name . ' checked in',
                    $elder->name . ' pressed "I\'m OK" just now.',
                    ['type' => 'ok_checkin', 'elder_id' => $elderId]
                );
            }
        }
    }

    private function createAlert(int $elderId, string $type, string $messageSuffix): void
    {
        $userModel = new UserModel();
        $elder = $userModel->find($elderId);
        if (!$elder) return;

        $alertModel = new AlertModel();
        $familyMemberModel = new FamilyMemberModel();
        $notificationService = new NotificationService();

        $groups = model('FamilyGroupModel')->getGroupsForUser($elderId);
        foreach ($groups as $group) {
            $alertModel->insert([
                'group_id'   => $group->id,
                'elder_id'   => $elderId,
                'type'       => $type === 'help' ? 'help' : 'emergency',
                'message'    => $elder->name . ' ' . $messageSuffix,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $familyMembers = $familyMemberModel->getFamilyInGroup($group->id);
            foreach ($familyMembers as $member) {
                $priority = $type === 'emergency' ? 'EMERGENCY' : 'URGENT';
                $notificationService->send(
                    $member->user_id,
                    'alert',
                    "[$priority] " . $elder->name,
                    $elder->name . ' ' . $messageSuffix,
                    ['type' => $type, 'elder_id' => $elderId, 'group_id' => $group->id]
                );
            }
        }
    }

    private function getResponseMessage(string $type): string
    {
        return match ($type) {
            'ok'        => 'Check-in recorded. Your family has been notified.',
            'help'      => 'Help request sent to your family.',
            'emergency' => 'Emergency alert sent. Help is on the way.',
        };
    }

    private function isOverdue(int $userId, ?object $latestCheckIn): bool
    {
        if (!$latestCheckIn) return true;

        $settingModel = new \App\Models\CheckInSettingModel();
        $settings = $settingModel->getSettingsForUser($userId);
        $frequencySeconds = ($settings->frequency_hours ?? 24) * 3600;

        $lastTime = strtotime($latestCheckIn->created_at);
        return (time() - $lastTime) > $frequencySeconds;
    }
}
