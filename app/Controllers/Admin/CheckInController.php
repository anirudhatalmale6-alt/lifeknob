<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CheckInModel;

class CheckInController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $checkInModel = new CheckInModel();
        $typeFilter = $this->request->getGet('type');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $builder = $checkInModel
            ->select('check_ins.*, u.name as user_name, u.role as user_role')
            ->join('users u', 'u.id = check_ins.user_id', 'left');

        if ($typeFilter && in_array($typeFilter, ['ok', 'help', 'emergency'])) {
            $builder->where('check_ins.type', $typeFilter);
        }

        if ($dateFrom) {
            $builder->where('check_ins.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('check_ins.created_at <=', $dateTo . ' 23:59:59');
        }

        $checkIns = $builder->orderBy('check_ins.created_at', 'DESC')->paginate(50);

        return view('admin/checkins/index', [
            'activeMenu'  => 'checkins',
            'checkIns'    => $checkIns,
            'pager'       => $checkInModel->pager,
            'typeFilter'  => $typeFilter,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
        ]);
    }
}
