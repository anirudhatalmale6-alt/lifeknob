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
            $value = $this->input($field);
            if ($value !== null) {
                $data[$field] = $value;
            }
        }

        $userModel = new UserModel();
        $userUpdates = [];
        $sosNumber = $this->input('sos_number');
        if ($sosNumber !== null) {
            $userUpdates['sos_number'] = $sosNumber;
        }
        $quietHoursEnabled = $this->input('quiet_hours_enabled');
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
                'avatar'            => $user->avatar ?? null,
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
            $value = $this->input($field);
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

    public function uploadAvatar()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $file = $this->request->getFile('avatar');
        if (!$file || !$file->isValid()) {
            return $this->failValidationErrors('No valid image uploaded');
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return $this->failValidationErrors('Image must be under 2MB');
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->failValidationErrors('Only JPG, PNG, and WebP images are allowed');
        }

        $newName = 'avatar_' . $userId . '_' . time() . '.' . $file->getExtension();
        $uploadPath = FCPATH . 'uploads/avatars/';

        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if ($user && !empty($user->avatar)) {
            $oldFile = $uploadPath . basename($user->avatar);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $file->move($uploadPath, $newName);
        $avatarUrl = '/uploads/avatars/' . $newName;
        $userModel->update($userId, ['avatar' => $avatarUrl]);

        return $this->respond([
            'status'     => 'success',
            'message'    => 'Avatar updated',
            'avatar_url' => $avatarUrl,
        ]);
    }
}
