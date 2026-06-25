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

        $settings = [
            'default_frequency_hours'     => getenv('DEFAULT_FREQUENCY_HOURS') ?: 24,
            'default_reminder_minutes'    => getenv('DEFAULT_REMINDER_MINUTES') ?: 30,
            'default_alert_delay_minutes' => getenv('DEFAULT_ALERT_DELAY_MINUTES') ?: 60,
            'firebase_configured'         => file_exists(WRITEPATH . 'firebase/service-account.json'),
            'cron_token'                  => getenv('CRON_TOKEN') ?: 'lifeknob2026cronkey',
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

        return redirect()->to('/admin/settings')->with('success', 'Settings saved');
    }
}
