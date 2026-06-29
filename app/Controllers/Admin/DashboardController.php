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

        $thresholdRow = $db->table('site_settings')
            ->where('setting_key', 'alert_threshold_days')
            ->get()->getRow();
        $thresholdDays = $thresholdRow ? (int)$thresholdRow->setting_value : 2;
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$thresholdDays} days"));

        $overdueUsers = $db->query("
            SELECT DISTINCT u.id
            FROM users u
            INNER JOIN connections c ON (c.user_id = u.id OR c.connected_to = u.id)
            WHERE c.status = 'accepted'
              AND u.role != 'admin'
              AND u.deleted_at IS NULL
              AND u.id NOT IN (SELECT user_id FROM check_ins WHERE created_at >= ?)
              AND u.id IN (SELECT user_id FROM check_ins)
        ", [$cutoff])->getResultArray();
        $overdueCount = count($overdueUsers);

        $stats = [
            'total_users'        => $totalUsers,
            'active_connections' => $activeConnections,
            'checkins_today'     => $checkinsToday,
            'overdue_users'      => $overdueCount,
        ];

        // Check-in activity last 7 days
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = $db->table('check_ins')
                ->where('created_at >=', $date . ' 00:00:00')
                ->where('created_at <=', $date . ' 23:59:59')
                ->countAllResults();
            $chartData[] = ['date' => date('D', strtotime($date)), 'count' => $count];
        }

        // Plan distribution
        $freePlan = $db->table('users')->where('role !=', 'admin')->where('deleted_at IS NULL')
            ->where('plan', 'free')->countAllResults();
        $paidPlan = $db->table('users')->where('role !=', 'admin')->where('deleted_at IS NULL')
            ->where('plan', 'paid')->countAllResults();

        // Connection status breakdown
        $connPending = $db->table('connections')->where('status', 'pending')->countAllResults();
        $connAccepted = $db->table('connections')->where('status', 'accepted')->countAllResults();
        $connInactive = $db->table('connections')->where('status', 'inactive')->countAllResults();

        // New users last 7 days
        $newUsersData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $count = $db->table('users')
                ->where('role !=', 'admin')
                ->where('deleted_at IS NULL')
                ->where('created_at >=', $date . ' 00:00:00')
                ->where('created_at <=', $date . ' 23:59:59')
                ->countAllResults();
            $newUsersData[] = $count;
        }

        // Recent check-ins
        $recentCheckIns = $db->query("
            SELECT ci.*, u.name as user_name, u.user_code
            FROM check_ins ci
            LEFT JOIN users u ON u.id = ci.user_id
            ORDER BY ci.created_at DESC
            LIMIT 10
        ")->getResult();

        // Recent connections
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
            'chartData'         => $chartData,
            'newUsersData'      => $newUsersData,
            'planData'          => ['free' => $freePlan, 'paid' => $paidPlan],
            'connStatusData'    => ['pending' => $connPending, 'accepted' => $connAccepted, 'inactive' => $connInactive],
            'recentCheckIns'    => $recentCheckIns,
            'recentConnections' => $recentConnections,
        ]);
    }
}
