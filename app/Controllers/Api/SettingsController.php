<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\CheckInSettingModel;
use App\Models\UserModel;

class SettingsController extends ResourceController
{
    protected $format = 'json';

    public function getSettings()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
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
        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
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

        if (empty($data)) {
            return $this->failValidationErrors('No settings to update');
        }

        if ($existing) {
            $settingModel->update($existing->id, $data);
        } else {
            $data['user_id'] = $userId;
            $settingModel->insert($data);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Settings updated',
        ]);
    }

    public function getProfile()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->failNotFound('User not found');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'phone'    => $user->phone,
                'role'     => $user->role,
                'timezone' => $user->timezone,
                'avatar'   => $user->avatar,
            ],
        ]);
    }

    public function updateProfile()
    {
        $userId = $this->request->getPost('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
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
