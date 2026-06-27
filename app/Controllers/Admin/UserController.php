<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CheckInModel;

class UserController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $userModel = new UserModel();
        $planFilter = $this->request->getGet('plan');
        $search = $this->request->getGet('q');

        $builder = $userModel->where('role !=', 'admin');

        if ($planFilter && in_array($planFilter, ['free', 'paid'])) {
            $builder->where('plan', $planFilter);
        }

        if ($search) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
                ->orLike('user_code', $search)
                ->groupEnd();
        }

        $users = $builder->orderBy('created_at', 'DESC')->paginate(25);

        // Get connection counts and last check-in for each user
        $db = \Config\Database::connect();
        foreach ($users as &$user) {
            $user->connection_count = $db->table('connections')
                ->groupStart()
                    ->where('user_id', $user->id)
                    ->orWhere('connected_to', $user->id)
                ->groupEnd()
                ->where('status', 'accepted')
                ->countAllResults();

            $lastCheckin = $db->table('check_ins')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'DESC')
                ->limit(1)
                ->get()->getRow();
            $user->last_checkin_at = $lastCheckin ? $lastCheckin->created_at : null;
        }

        return view('admin/users/index', [
            'activeMenu'  => 'users',
            'users'       => $users,
            'pager'       => $userModel->pager,
            'planFilter'  => $planFilter,
            'search'      => $search,
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

        $db = \Config\Database::connect();

        // Get connections for this user
        $connections = $db->query("
            SELECT c.*,
                   CASE WHEN c.user_id = ? THEN u2.id ELSE u1.id END as other_id,
                   CASE WHEN c.user_id = ? THEN u2.name ELSE u1.name END as other_name,
                   CASE WHEN c.user_id = ? THEN u2.user_code ELSE u1.user_code END as other_code,
                   CASE WHEN c.user_id = ? THEN u2.email ELSE u1.email END as other_email,
                   (SELECT ci.created_at FROM check_ins ci
                    WHERE ci.user_id = CASE WHEN c.user_id = ? THEN c.connected_to ELSE c.user_id END
                    ORDER BY ci.created_at DESC LIMIT 1) as other_last_checkin
            FROM connections c
            LEFT JOIN users u1 ON u1.id = c.user_id
            LEFT JOIN users u2 ON u2.id = c.connected_to
            WHERE c.user_id = ? OR c.connected_to = ?
            ORDER BY c.created_at DESC
        ", [$id, $id, $id, $id, $id, $id, $id])->getResult();

        // Get check-in history (last 20)
        $checkInModel = new CheckInModel();
        $checkIns = $checkInModel->getCheckInHistory($id, 20);

        return view('admin/users/view', [
            'activeMenu'  => 'users',
            'user'        => $user,
            'connections'  => $connections,
            'checkIns'    => $checkIns,
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
