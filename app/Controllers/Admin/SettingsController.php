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
        $textFields = ['alert_threshold_days', 'adsense_banner_code', 'adsense_bumper_code', 'bumper_delay_seconds'];
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

        return redirect()->to('/admin/settings')->with('success', 'Settings saved');
    }
}
