<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnectionModel extends Model
{
    protected $table            = 'connections';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_id',
        'connected_to',
        'status',
        'created_at',
    ];

    public function getConnectionsForUser(int $userId): array
    {
        return $this->select('connections.id as connection_id, connections.status, connections.created_at as connected_since, users.id as user_id, users.name, users.email, users.user_code, users.last_seen_at, users.avatar')
            ->join('users', 'users.id = connections.connected_to')
            ->where('connections.user_id', $userId)
            ->findAll();
    }

    public function getConnectedToMe(int $userId): array
    {
        return $this->select('connections.id as connection_id, connections.status, connections.created_at as connected_since, users.id as user_id, users.name, users.email, users.user_code, users.last_seen_at, users.avatar')
            ->join('users', 'users.id = connections.user_id')
            ->where('connections.connected_to', $userId)
            ->findAll();
    }

    public function getPendingRequests(int $userId): array
    {
        return $this->select('connections.id as connection_id, connections.created_at as requested_at, users.id as user_id, users.name, users.user_code, users.avatar')
            ->join('users', 'users.id = connections.user_id')
            ->where('connections.connected_to', $userId)
            ->where('connections.status', 'pending')
            ->findAll();
    }

    public function connect(int $userId, int $connectedTo): int|false
    {
        return $this->insert([
            'user_id'      => $userId,
            'connected_to' => $connectedTo,
            'status'       => 'pending',
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function acceptConnection(int $connectionId, int $userId): bool
    {
        return $this->where('id', $connectionId)
            ->where('connected_to', $userId)
            ->where('status', 'pending')
            ->set(['status' => 'accepted'])
            ->update();
    }

    public function rejectConnection(int $connectionId, int $userId): bool
    {
        return $this->where('id', $connectionId)
            ->where('connected_to', $userId)
            ->where('status', 'pending')
            ->delete();
    }

    public function disconnect(int $userId, int $connectedTo): bool
    {
        return $this->where('user_id', $userId)
            ->where('connected_to', $connectedTo)
            ->delete();
    }

    public function getConnectionCount(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->whereIn('status', ['pending', 'accepted'])
            ->countAllResults();
    }

    public function canConnect(int $userId): bool
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) return false;

        $maxConnections = $user->max_connections ?? (($user->plan ?? 'free') === 'paid' ? 5 : 1);
        $currentCount = $this->getConnectionCount($userId);

        return $currentCount < $maxConnections;
    }

    public function canChangeConnection(int $userId): bool
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) return false;
        if (($user->plan ?? 'free') === 'paid') return true;
        if (empty($user->last_code_change)) return true;

        $cooldownEnd = strtotime($user->last_code_change) + (3 * 24 * 3600);
        return time() >= $cooldownEnd;
    }

    public function getCooldownRemaining(int $userId): int
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user || ($user->plan ?? 'free') === 'paid' || empty($user->last_code_change)) return 0;

        $cooldownEnd = strtotime($user->last_code_change) + (3 * 24 * 3600);
        return max(0, $cooldownEnd - time());
    }

    public function connectionExists(int $userId, int $connectedTo): bool
    {
        return $this->where('user_id', $userId)
            ->where('connected_to', $connectedTo)
            ->countAllResults() > 0;
    }

    public function getAllConnectedUserIds(int $userId): array
    {
        $outgoing = $this->where('user_id', $userId)->where('status', 'accepted')->findAll();
        $outIds = array_map(fn($c) => (int) $c->connected_to, $outgoing);

        $incoming = $this->where('connected_to', $userId)->where('status', 'accepted')->findAll();
        $inIds = array_map(fn($c) => (int) $c->user_id, $incoming);

        return array_unique(array_merge($outIds, $inIds));
    }
}
