<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CheckInModel;
use App\Models\AlertModel;
use App\Models\FamilyGroupModel;

class DashboardController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $userModel    = new UserModel();
        $checkInModel = new CheckInModel();
        $alertModel   = new AlertModel();
        $groupModel   = new FamilyGroupModel();

        $db = \Config\Database::connect();
        $stats = [
            'total_users'    => $db->table('users')->where('role !=', 'admin')->where('deleted_at IS NULL')->countAllResults(),
            'active_elders'  => $db->table('users')->where('role', 'elder')->where('is_active', 1)->where('deleted_at IS NULL')->countAllResults(),
            'family_members' => $db->table('users')->where('role', 'family')->where('deleted_at IS NULL')->countAllResults(),
            'active_alerts'  => $db->table('alerts')->where('is_resolved', 0)->countAllResults(),
            'total_groups'   => $db->table('family_groups')->countAllResults(),
            'checkins_today' => $db->table('check_ins')->where('check_ins.created_at >=', date('Y-m-d 00:00:00'))->countAllResults(),
        ];

        $recentAlerts = $alertModel
            ->select('alerts.*, u.name as elder_name, g.name as group_name')
            ->join('users u', 'u.id = alerts.elder_id', 'left')
            ->join('family_groups g', 'g.id = alerts.group_id', 'left')
            ->orderBy('alerts.created_at', 'DESC')
            ->limit(10)
            ->find();

        $recentCheckIns = $checkInModel
            ->select('check_ins.*, u.name as user_name')
            ->join('users u', 'u.id = check_ins.user_id', 'left')
            ->orderBy('check_ins.created_at', 'DESC')
            ->limit(10)
            ->find();

        return view('admin/dashboard/index', [
            'activeMenu'      => 'dashboard',
            'stats'           => $stats,
            'recentAlerts'    => $recentAlerts,
            'recentCheckIns'  => $recentCheckIns,
        ]);
    }
}
