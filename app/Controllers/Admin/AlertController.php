<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AlertModel;

class AlertController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $alertModel = new AlertModel();
        $typeFilter = $this->request->getGet('type');
        $statusFilter = $this->request->getGet('status');

        $builder = $alertModel
            ->select('alerts.*, u.name as elder_name, g.name as group_name, r.name as resolved_by_name')
            ->join('users u', 'u.id = alerts.elder_id', 'left')
            ->join('family_groups g', 'g.id = alerts.group_id', 'left')
            ->join('users r', 'r.id = alerts.resolved_by', 'left');

        if ($typeFilter && in_array($typeFilter, ['missed_checkin', 'help', 'emergency'])) {
            $builder->where('alerts.type', $typeFilter);
        }

        if ($statusFilter === 'active') {
            $builder->where('alerts.is_resolved', 0);
        } elseif ($statusFilter === 'resolved') {
            $builder->where('alerts.is_resolved', 1);
        }

        $alerts = $builder->orderBy('alerts.created_at', 'DESC')->paginate(25);

        return view('admin/alerts/index', [
            'activeMenu'   => 'alerts',
            'alerts'       => $alerts,
            'pager'        => $alertModel->pager,
            'typeFilter'   => $typeFilter,
            'statusFilter' => $statusFilter,
        ]);
    }
}
