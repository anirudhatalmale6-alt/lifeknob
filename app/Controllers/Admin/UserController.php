<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CheckInModel;
use App\Models\CheckInSettingModel;
use App\Models\FamilyGroupModel;

class UserController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $userModel = new UserModel();
        $roleFilter = $this->request->getGet('role');
        $search = $this->request->getGet('q');

        $builder = $userModel->where('role !=', 'admin');

        if ($roleFilter && in_array($roleFilter, ['elder', 'family'])) {
            $builder->where('role', $roleFilter);
        }

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->groupEnd();
        }

        $users = $builder->orderBy('created_at', 'DESC')->paginate(25);

        return view('admin/users/index', [
            'activeMenu' => 'users',
            'users'      => $users,
            'pager'      => $userModel->pager,
            'roleFilter' => $roleFilter,
            'search'     => $search,
        ]);
    }

    public function view($id)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        $checkIns = [];
        $settings = null;

        if ($user->role === 'elder') {
            $checkInModel = new CheckInModel();
            $checkIns = $checkInModel->getCheckInHistory($id, 20);

            $settingModel = new CheckInSettingModel();
            $settings = $settingModel->getSettingsForUser($id);
        }

        $groupModel = new FamilyGroupModel();
        $groups = $groupModel->getGroupsForUser($id);

        return view('admin/users/view', [
            'activeMenu' => 'users',
            'user'       => $user,
            'checkIns'   => $checkIns,
            'settings'   => $settings,
            'groups'     => $groups,
        ]);
    }

    public function toggle($id)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if ($user) {
            $userModel->update($id, ['is_active' => $user->is_active ? 0 : 1]);
        }

        return redirect()->to("/admin/users/{$id}")->with('success', 'User status updated');
    }
}
