<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AlertController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $db = \Config\Database::connect();

        // Get alert threshold from site_settings
        $thresholdRow = $db->table('site_settings')
            ->where('setting_key', 'alert_threshold_days')
            ->get()->getRow();
        $thresholdDays = $thresholdRow ? (int)$thresholdRow->setting_value : 2;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$thresholdDays} days"));

        // Find overdue users: users with accepted connections whose last check-in is before cutoff
        // They must have at least one check-in (otherwise they're new, not overdue)
        $overdueUsers = $db->query("
            SELECT u.id, u.name, u.user_code, u.email, u.phone, u.is_active,
                   MAX(ci.created_at) as last_checkin_at
            FROM users u
            INNER JOIN check_ins ci ON ci.user_id = u.id
            INNER JOIN connections c ON (c.user_id = u.id OR c.connected_to = u.id) AND c.status = 'accepted'
            WHERE u.role != 'admin'
              AND u.deleted_at IS NULL
            GROUP BY u.id, u.name, u.user_code, u.email, u.phone, u.is_active
            HAVING MAX(ci.created_at) < ?
            ORDER BY last_checkin_at ASC
        ", [$cutoff])->getResult();

        // For each overdue user, calculate days overdue and get who's connected to them
        foreach ($overdueUsers as &$user) {
            $lastCheckin = strtotime($user->last_checkin_at);
            $now = time();
            $user->days_overdue = floor(($now - $lastCheckin) / 86400);

            // Get connected users
            $connectedUsers = $db->query("
                SELECT CASE WHEN c.user_id = ? THEN u2.name ELSE u1.name END as name,
                       CASE WHEN c.user_id = ? THEN u2.user_code ELSE u1.user_code END as code
                FROM connections c
                LEFT JOIN users u1 ON u1.id = c.user_id
                LEFT JOIN users u2 ON u2.id = c.connected_to
                WHERE (c.user_id = ? OR c.connected_to = ?) AND c.status = 'accepted'
            ", [$user->id, $user->id, $user->id, $user->id])->getResult();

            $user->connected_to = $connectedUsers;
        }

        return view('admin/alerts/index', [
            'activeMenu'    => 'overdue',
            'overdueUsers'  => $overdueUsers,
            'thresholdDays' => $thresholdDays,
        ]);
    }
}
