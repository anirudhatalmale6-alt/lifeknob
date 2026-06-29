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
        $sosName = $this->input('sos_name');
        if ($sosName !== null) {
            $userUpdates['sos_name'] = $sosName;
        }
        $ambulanceNumber = $this->input('ambulance_number');
        if ($ambulanceNumber !== null) {
            $userUpdates['ambulance_number'] = $ambulanceNumber;
        }
        $plan = $this->input('plan');
        if ($plan !== null) {
            $userUpdates['plan'] = $plan;
        }
        $maxConn = $this->input('max_connections');
        if ($maxConn !== null) {
            $userUpdates['max_connections'] = (int) $maxConn;
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
        $maxConnections = $user->max_connections ?? ($plan === 'paid' ? 5 : 1);

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
                'sos_name'          => $user->sos_name ?? null,
                'ambulance_number'  => $user->ambulance_number ?? null,
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
        $fields = ['name', 'email', 'phone', 'timezone'];

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

    public function getSiteSettings()
    {
        $db = db_connect();
        $rows = $db->table("site_settings")->get()->getResultArray();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row["setting_key"]] = $row["setting_value"];
        }
        return $this->respond([
            "status" => "success",
            "data" => $settings,
        ]);
    }

    public function languages()
    {
        $db = db_connect();
        $languages = $db->table('languages')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->get()->getResultArray();

        $result = [];
        foreach ($languages as $lang) {
            $result[] = [
                'code' => $lang['code'],
                'name' => $lang['name'],
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function translations($langCode = 'en')
    {
        $db = db_connect();

        $lang = $db->table('languages')
            ->where('code', $langCode)
            ->where('is_active', 1)
            ->get()->getRow();

        if (!$lang) {
            return $this->failNotFound('Language not found');
        }

        $rows = $db->table('translations')
            ->where('lang_code', $langCode)
            ->get()->getResultArray();

        $strings = [];
        foreach ($rows as $row) {
            $strings[$row['string_key']] = $row['string_value'];
        }

        if ($langCode !== 'en') {
            $enRows = $db->table('translations')
                ->where('lang_code', 'en')
                ->get()->getResultArray();
            foreach ($enRows as $row) {
                if (!isset($strings[$row['string_key']])) {
                    $strings[$row['string_key']] = $row['string_value'];
                }
            }
        }

        return $this->respond([
            'status' => 'success',
            'lang_code' => $langCode,
            'lang_name' => $lang->name,
            'data' => $strings,
        ]);
    }

    public function logos()
    {
        $db = db_connect();
        $rows = $db->table('app_logos')->get()->getResultArray();
        $result = [];
        foreach ($rows as $r) {
            if ($r['file_path']) {
                $result[$r['logo_key']] = $r['file_path'];
            }
        }
        return $this->respond(['status' => 'success', 'data' => $result]);
    }

    public function legalPage($type = null, $langCode = 'en')
    {
        $db = db_connect();

        $page = $db->table('legal_pages')
            ->where('page_type', $type)
            ->where('lang_code', $langCode)
            ->get()->getRowArray();

        if (!$page) {
            $page = $db->table('legal_pages')
                ->where('page_type', $type)
                ->where('lang_code', 'en')
                ->get()->getRowArray();
        }

        if (!$page) {
            return $this->failNotFound('Page not found');
        }

        return $this->respond([
            'status' => 'success',
            'data' => [
                'title' => $page['title'],
                'content' => $page['content'],
                'lang_code' => $page['lang_code'],
            ],
        ]);
    }
}
