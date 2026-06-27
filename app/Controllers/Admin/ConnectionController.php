<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ConnectionController extends BaseController
{
    public function index()
    {
        if (!session()->get('is_admin')) {
            return redirect()->to('/admin/login');
        }

        $db = \Config\Database::connect();
        $statusFilter = $this->request->getGet('status');
        $search = $this->request->getGet('q');

        $where = '';
        $params = [];

        if ($statusFilter && in_array($statusFilter, ['pending', 'accepted', 'rejected', 'inactive'])) {
            $where .= ' AND c.status = ?';
            $params[] = $statusFilter;
        }

        if ($search) {
            $like = '%' . $db->escapeLikeString($search) . '%';
            $where .= ' AND (u1.name LIKE ? OR u1.user_code LIKE ? OR u2.name LIKE ? OR u2.user_code LIKE ?)';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $connections = $db->query("
            SELECT c.*,
                   u1.name as user_name, u1.user_code as user_code,
                   u2.name as connected_name, u2.user_code as connected_code
            FROM connections c
            LEFT JOIN users u1 ON u1.id = c.user_id
            LEFT JOIN users u2 ON u2.id = c.connected_to
            WHERE 1=1 {$where}
            ORDER BY c.created_at DESC
            LIMIT 100
        ", $params)->getResult();

        return view('admin/connections/index', [
            'activeMenu'   => 'connections',
            'connections'  => $connections,
            'statusFilter' => $statusFilter,
            'search'       => $search,
        ]);
    }
}
