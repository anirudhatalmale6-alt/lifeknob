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

        $stats = [
            'total_users'    => $userModel->where('role !=', 'admin')->countAllResults(false),
            'active_elders'  => $userModel->where('role', 'elder')->where('is_active', 1)->countAllResults(false),
            'family_members' => $userModel->where('role', 'family')->countAllResults(false),
            'active_alerts'  => $alertModel->where('is_resolved', 0)->countAllResults(false),
            'total_groups'   => $groupModel->countAllResults(false),
            'checkins_today' => $checkInModel->where('created_at >=', date('Y-m-d 00:00:00'))->countAllResults(false),
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
