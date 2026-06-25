<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FamilyGroupModel;
use App\Models\FamilyMemberModel;
use App\Models\AlertModel;

class GroupController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $groupModel = new FamilyGroupModel();

        $groups = $groupModel
            ->select('family_groups.*, u.name as created_by_name,
                      (SELECT COUNT(*) FROM family_members WHERE group_id = family_groups.id) as member_count,
                      (SELECT COUNT(*) FROM family_members WHERE group_id = family_groups.id AND role = "elder") as elder_count')
            ->join('users u', 'u.id = family_groups.created_by', 'left')
            ->orderBy('family_groups.created_at', 'DESC')
            ->paginate(25);

        return view('admin/groups/index', [
            'activeMenu' => 'groups',
            'groups'     => $groups,
            'pager'      => $groupModel->pager,
        ]);
    }

    public function view($id)
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $groupModel = new FamilyGroupModel();
        $group = $groupModel->find($id);

        if (!$group) {
            return redirect()->to('/admin/groups')->with('error', 'Group not found');
        }

        $memberModel = new FamilyMemberModel();
        $members = $memberModel->getMembersOfGroup($id);

        $alertModel = new AlertModel();
        $alerts = $alertModel
            ->select('alerts.*, u.name as elder_name')
            ->join('users u', 'u.id = alerts.elder_id', 'left')
            ->where('alerts.group_id', $id)
            ->orderBy('alerts.created_at', 'DESC')
            ->limit(20)
            ->find();

        return view('admin/groups/view', [
            'activeMenu' => 'groups',
            'group'      => $group,
            'members'    => $members,
            'alerts'     => $alerts,
        ]);
    }
}
