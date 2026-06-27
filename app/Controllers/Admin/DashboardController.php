<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $db = \Config\Database::connect();

        // Core stats
        $totalUsers = $db->table('users')
            ->where('role !=', 'admin')
            ->where('deleted_at IS NULL')
            ->countAllResults();

        $activeConnections = $db->table('connections')
            ->where('status', 'accepted')
            ->countAllResults();

        $checkinsToday = $db->table('check_ins')
            ->where('created_at >=', date('Y-m-d 00:00:00'))
            ->countAllResults();

        // Overdue users: accepted connections where the connected user's last check-in
        // is older than alert_threshold_days
        $thresholdRow = $db->table('site_settings')
            ->where('setting_key', 'alert_threshold_days')
            ->get()->getRow();
        $thresholdDays = $thresholdRow ? (int)$thresholdRow->setting_value : 2;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$thresholdDays} days"));

        // Find users who are connected (accepted) and whose last check-in is older than threshold
        $overdueUsers = $db->query("
            SELECT DISTINCT u.id
            FROM users u
            INNER JOIN connections c ON (c.user_id = u.id OR c.connected_to = u.id)
            WHERE c.status = 'accepted'
              AND u.role != 'admin'
              AND u.deleted_at IS NULL
              AND (
                u.id NOT IN (SELECT user_id FROM check_ins WHERE created_at >= ?)
              )
              AND u.id IN (SELECT user_id FROM check_ins)
        ", [$cutoff])->getResultArray();
        $overdueCount = count($overdueUsers);

        $stats = [
            'total_users'        => $totalUsers,
            'active_connections' => $activeConnections,
            'checkins_today'     => $checkinsToday,
            'overdue_users'      => $overdueCount,
        ];

        // Recent check-ins (last 10 with user names)
        $recentCheckIns = $db->query("
            SELECT ci.*, u.name as user_name, u.user_code
            FROM check_ins ci
            LEFT JOIN users u ON u.id = ci.user_id
            ORDER BY ci.created_at DESC
            LIMIT 10
        ")->getResult();

        // Recent connections (last 10)
        $recentConnections = $db->query("
            SELECT c.*,
                   u1.name as user_name, u1.user_code as user_code,
                   u2.name as connected_name, u2.user_code as connected_code
            FROM connections c
            LEFT JOIN users u1 ON u1.id = c.user_id
            LEFT JOIN users u2 ON u2.id = c.connected_to
            ORDER BY c.created_at DESC
            LIMIT 10
        ")->getResult();

        return view('admin/dashboard/index', [
            'activeMenu'        => 'dashboard',
            'stats'             => $stats,
            'recentCheckIns'    => $recentCheckIns,
            'recentConnections' => $recentConnections,
        ]);
    }
}
