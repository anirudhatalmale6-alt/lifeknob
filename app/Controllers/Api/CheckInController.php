<?php

namespace App\Controllers\Api;

use App\Models\CheckInModel;
use App\Models\AlertModel;
use App\Models\ConnectionModel;
use App\Models\FamilyMemberModel;
use App\Models\UserModel;
use App\Services\NotificationService;

class CheckInController extends ApiBaseController
{
    public function create()
    {
        $userId = $this->getUserId();
        $type   = $this->input('type');

        if (!$userId || !in_array($type, ['ok', 'help', 'emergency'])) {
            return $this->failValidationErrors('Valid type (ok/help/emergency) required');
        }

        $checkInModel = new CheckInModel();

        $data = [
            'user_id'    => $userId,
            'type'       => $type,
            'latitude'   => $this->input('latitude'),
            'longitude'  => $this->input('longitude'),
            'note'       => $this->input('note'),
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
        $userId = $this->getUserId();
        $limit  = (int)($this->request->getGet('limit') ?? 50);

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $connectionModel = new ConnectionModel();
        $connectedIds = $connectionModel->getAllConnectedUserIds($userId);
        $allUserIds = array_merge([$userId], $connectedIds);

        $db = \Config\Database::connect();
        $history = $db->table('check_ins')
            ->select('check_ins.*, users.name as user_name')
            ->join('users', 'users.id = check_ins.user_id', 'left')
            ->whereIn('check_ins.user_id', $allUserIds)
            ->orderBy('check_ins.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data'   => $history,
        ]);
    }

    public function stats()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
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

    public function latestForConnections()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $connectionModel = new ConnectionModel();
        $checkInModel = new CheckInModel();
        $userModel = new UserModel();

        $connectedIds = $connectionModel->getAllConnectedUserIds($userId);

        $result = [];
        foreach ($connectedIds as $connUserId) {
            $connUser = $userModel->find($connUserId);
            if (!$connUser) continue;

            $latest = $checkInModel->getLatestCheckIn($connUserId);
            $result[] = [
                'user_id'        => $connUser->id,
                'name'           => $connUser->name,
                'user_code'      => $connUser->user_code,
                'latest_checkin' => $latest ? [
                    'type'       => $latest->type,
                    'created_at' => $latest->created_at,
                    'note'       => $latest->note ?? null,
                ] : null,
                'is_overdue'     => $this->isOverdue($connUserId, $latest),
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

        $notificationService = new NotificationService();

        $familyMemberModel = new FamilyMemberModel();
        $groups = model('FamilyGroupModel')->getGroupsForUser($elderId);
        $notifiedUserIds = [];
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
                $notifiedUserIds[] = (int) $member->user_id;
            }
        }

        $connectionModel = new ConnectionModel();
        $connectedUserIds = $connectionModel->getAllConnectedUserIds($elderId);
        foreach ($connectedUserIds as $connUserId) {
            if (in_array($connUserId, $notifiedUserIds)) continue;
            $notificationService->send(
                $connUserId,
                'info',
                $elder->name . ' checked in',
                $elder->name . ' pressed "I\'m OK" just now.',
                ['type' => 'ok_checkin', 'elder_id' => $elderId]
            );
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
        $priority = $type === 'emergency' ? 'EMERGENCY' : 'URGENT';

        $notifiedUserIds = [];
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
                $notificationService->send(
                    $member->user_id,
                    'alert',
                    "[$priority] " . $elder->name,
                    $elder->name . ' ' . $messageSuffix,
                    ['type' => $type, 'elder_id' => $elderId, 'group_id' => $group->id]
                );
                $notifiedUserIds[] = (int) $member->user_id;
            }
        }

        $connectionModel = new ConnectionModel();
        $connectedUserIds = $connectionModel->getAllConnectedUserIds($elderId);
        foreach ($connectedUserIds as $connUserId) {
            if (in_array($connUserId, $notifiedUserIds)) continue;
            $notificationService->send(
                $connUserId,
                'alert',
                "[$priority] " . $elder->name,
                $elder->name . ' ' . $messageSuffix,
                ['type' => $type, 'elder_id' => $elderId]
            );
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
