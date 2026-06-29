<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class SettingsController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $db = db_connect();
        $siteSettings = [];
        $rows = $db->table('site_settings')->get()->getResultArray();
        foreach ($rows as $row) {
            $siteSettings[$row['setting_key']] = $row['setting_value'];
        }

        $settings = [
            'alert_threshold_days'        => $siteSettings['alert_threshold_days'] ?? '2',
            'reminder_enabled'            => $siteSettings['reminder_enabled'] ?? '1',
            'alert_email_enabled'         => $siteSettings['alert_email_enabled'] ?? '1',
            'firebase_configured'         => file_exists(WRITEPATH . 'firebase/service-account.json'),
            'cron_token'                  => getenv('CRON_TOKEN') ?: 'lifeknob2026cronkey',
            'ads_enabled'                 => $siteSettings['ads_enabled'] ?? '1',
            'adsense_banner_code'         => $siteSettings['adsense_banner_code'] ?? '',
            'adsense_bumper_code'         => $siteSettings['adsense_bumper_code'] ?? '',
            'bumper_delay_seconds'        => $siteSettings['bumper_delay_seconds'] ?? '30',
            'banner_ad_image'             => $siteSettings['banner_ad_image'] ?? '',
            'banner_ad_url'               => $siteSettings['banner_ad_url'] ?? '',
            'bumper_ad_image'             => $siteSettings['bumper_ad_image'] ?? '',
            'bumper_ad_url'               => $siteSettings['bumper_ad_url'] ?? '',
        ];

        return view('admin/settings/index', [
            'activeMenu' => 'settings',
            'settings'   => $settings,
        ]);
    }

    public function save()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $db = db_connect();
        $textFields = ['alert_threshold_days', 'adsense_banner_code', 'adsense_bumper_code', 'bumper_delay_seconds', 'banner_ad_url', 'bumper_ad_url'];
        $checkboxFields = ['reminder_enabled', 'alert_email_enabled', 'ads_enabled'];

        foreach ($textFields as $key) {
            $value = $this->request->getPost($key);
            if ($value !== null) {
                $db->table('site_settings')->replace([
                    'setting_key' => $key,
                    'setting_value' => $value,
                ]);
            }
        }
        foreach ($checkboxFields as $key) {
            $value = $this->request->getPost($key) ? '1' : '0';
            $db->table('site_settings')->replace([
                'setting_key' => $key,
                'setting_value' => $value,
            ]);
        }

        $uploadPath = FCPATH . 'uploads/ads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Banner ad image upload
        if ($this->request->getPost('remove_banner_image')) {
            $this->_removeAdImage($db, 'banner_ad_image', $uploadPath);
        }
        $bannerFile = $this->request->getFile('banner_ad_file');
        if ($bannerFile && $bannerFile->isValid() && !$bannerFile->hasMoved()) {
            $this->_removeAdImage($db, 'banner_ad_image', $uploadPath);
            $newName = 'banner_' . time() . '.' . $bannerFile->getExtension();
            $bannerFile->move($uploadPath, $newName);
            $db->table('site_settings')->replace([
                'setting_key' => 'banner_ad_image',
                'setting_value' => '/uploads/ads/' . $newName,
            ]);
        }

        // Bumper ad image upload
        if ($this->request->getPost('remove_bumper_image')) {
            $this->_removeAdImage($db, 'bumper_ad_image', $uploadPath);
        }
        $bumperFile = $this->request->getFile('bumper_ad_file');
        if ($bumperFile && $bumperFile->isValid() && !$bumperFile->hasMoved()) {
            $this->_removeAdImage($db, 'bumper_ad_image', $uploadPath);
            $newName = 'bumper_' . time() . '.' . $bumperFile->getExtension();
            $bumperFile->move($uploadPath, $newName);
            $db->table('site_settings')->replace([
                'setting_key' => 'bumper_ad_image',
                'setting_value' => '/uploads/ads/' . $newName,
            ]);
        }

        return redirect()->to('/admin/settings')->with('success', 'Settings saved');
    }

    private function _removeAdImage($db, string $key, string $uploadPath): void
    {
        $row = $db->table('site_settings')->where('setting_key', $key)->get()->getRowArray();
        if ($row && !empty($row['setting_value'])) {
            $oldFile = $uploadPath . basename($row['setting_value']);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $db->table('site_settings')->replace([
                'setting_key' => $key,
                'setting_value' => '',
            ]);
        }
    }
}
