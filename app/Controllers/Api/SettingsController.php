<?php

namespace App\Controllers\Api;

use App\Models\CheckInSettingModel;
use App\Models\UserModel;
use App\Models\ConnectionModel;

class SettingsController extends ApiBaseController
{
    public function getSettings()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $settingModel = new CheckInSettingModel();
        $settings = $settingModel->getSettingsForUser($userId);

        return $this->respond([
            'status' => 'success',
            'data'   => $settings,
        ]);
    }

    public function updateSettings()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $settingModel = new CheckInSettingModel();
        $existing = $settingModel->where('user_id', $userId)->first();

        $data = [];
        $fields = ['frequency_hours', 'reminder_minutes', 'alert_delay_minutes', 'quiet_hours_start', 'quiet_hours_end', 'is_active'];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            if ($value !== null) {
                $data[$field] = $value;
            }
        }

        // Also handle SOS number and quiet hours toggle on the user record
        $userModel = new UserModel();
        $userUpdates = [];
        $sosNumber = $this->request->getPost('sos_number');
        if ($sosNumber !== null) {
            $userUpdates['sos_number'] = $sosNumber;
        }
        $quietHoursEnabled = $this->request->getPost('quiet_hours_enabled');
        if ($quietHoursEnabled !== null) {
            $userUpdates['quiet_hours_enabled'] = $quietHoursEnabled ? 1 : 0;
        }
        if (!empty($userUpdates)) {
            $userModel->update($userId, $userUpdates);
        }

        if (!empty($data)) {
            if ($existing) {
                $settingModel->update($existing->id, $data);
            } else {
                $data['user_id'] = $userId;
                $settingModel->insert($data);
            }
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Settings updated',
        ]);
    }

    public function getProfile()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $db = \Config\Database::connect();
        $usedConnections = $db->table('connections')
            ->where('user_id', $userId)
            ->countAllResults();

        $plan = $user->plan ?? 'free';
        $maxConnections = $plan === 'paid' ? 5 : 1;

        return $this->respond([
            'status' => 'success',
            'user'   => [
                'id'                => (int) $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'phone'             => $user->phone ?? '',
                'user_code'         => $user->user_code,
                'plan'              => $plan,
                'max_connections'   => $maxConnections,
                'used_connections'  => $usedConnections,
                'sos_number'        => $user->sos_number ?? null,
                'quiet_hours_enabled' => ($user->quiet_hours_enabled ?? 0) == 1,
            ],
        ]);
    }

    public function updateProfile()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $userModel = new UserModel();
        $data = [];
        $fields = ['name', 'phone', 'timezone'];

        foreach ($fields as $field) {
            $value = $this->request->getPost($field);
            if ($value !== null) {
                $data[$field] = $value;
            }
        }

        if (empty($data)) {
            return $this->failValidationErrors('No fields to update');
        }

        $userModel->update($userId, $data);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Profile updated',
        ]);
    }
}
