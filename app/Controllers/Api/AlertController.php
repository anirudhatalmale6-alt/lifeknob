<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\AlertModel;
use App\Models\NotificationModel;

class AlertController extends ResourceController
{
    protected $format = 'json';

    public function getAlerts()
    {
        $groupId = $this->request->getGet('group_id');

        if (!$groupId) {
            return $this->failValidationErrors('group_id required');
        }

        $alertModel = new AlertModel();
        $alerts = $alertModel->getRecentAlerts($groupId, 50);

        return $this->respond([
            'status' => 'success',
            'data'   => $alerts,
        ]);
    }

    public function getActive()
    {
        $groupId = $this->request->getGet('group_id');

        if (!$groupId) {
            return $this->failValidationErrors('group_id required');
        }

        $alertModel = new AlertModel();
        $alerts = $alertModel->getActiveAlerts($groupId);

        return $this->respond([
            'status' => 'success',
            'data'   => $alerts,
        ]);
    }

    public function resolve()
    {
        $alertId    = $this->request->getPost('alert_id');
        $resolvedBy = $this->request->getPost('user_id');

        if (!$alertId || !$resolvedBy) {
            return $this->failValidationErrors('alert_id and user_id required');
        }

        $alertModel = new AlertModel();
        $alertModel->resolveAlert($alertId, $resolvedBy);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Alert resolved',
        ]);
    }

    public function getNotifications()
    {
        $userId = $this->request->getGet('user_id');
        $limit  = (int)($this->request->getGet('limit') ?? 50);

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $notifModel = new NotificationModel();
        $notifications = $notifModel->getForUser($userId, $limit);

        return $this->respond([
            'status' => 'success',
            'data'   => $notifications,
        ]);
    }

    public function markRead()
    {
        $notificationId = $this->request->getPost('notification_id');

        if (!$notificationId) {
            return $this->failValidationErrors('notification_id required');
        }

        $notifModel = new NotificationModel();
        $notifModel->markAsRead($notificationId);

        return $this->respond(['status' => 'success']);
    }

    public function markAllRead()
    {
        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $notifModel = new NotificationModel();
        $notifModel->markAllRead($userId);

        return $this->respond(['status' => 'success']);
    }
}
