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
        'created_at',
    ];

    /**
     * Get all people this user is connected TO (i.e. whose codes they entered).
     * Joins users table for name, email, user_code, last_seen_at.
     */
    public function getConnectionsForUser(int $userId): array
    {
        return $this->select('connections.id as connection_id, connections.created_at as connected_since, users.id as user_id, users.name, users.email, users.user_code, users.last_seen_at')
            ->join('users', 'users.id = connections.connected_to')
            ->where('connections.user_id', $userId)
            ->findAll();
    }

    /**
     * Get all people who connected TO this user (i.e. entered this user's code).
     * Joins users table for name, email, user_code, last_seen_at.
     */
    public function getConnectedToMe(int $userId): array
    {
        return $this->select('connections.id as connection_id, connections.created_at as connected_since, users.id as user_id, users.name, users.email, users.user_code, users.last_seen_at')
            ->join('users', 'users.id = connections.user_id')
            ->where('connections.connected_to', $userId)
            ->findAll();
    }

    /**
     * Create a connection between two users.
     */
    public function connect(int $userId, int $connectedTo): int|false
    {
        return $this->insert([
            'user_id'      => $userId,
            'connected_to' => $connectedTo,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Remove a connection.
     */
    public function disconnect(int $userId, int $connectedTo): bool
    {
        return $this->where('user_id', $userId)
            ->where('connected_to', $connectedTo)
            ->delete();
    }

    /**
     * Count how many active outgoing connections a user has.
     */
    public function getConnectionCount(int $userId): int
    {
        return $this->where('user_id', $userId)
            ->countAllResults();
    }

    /**
     * Check if the user has a free connection slot.
     * Free users: max 1 connection. Paid users: max 5.
     */
    public function canConnect(int $userId): bool
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return false;
        }

        $maxConnections = ($user->plan ?? 'free') === 'paid' ? 5 : 1;
        $currentCount = $this->getConnectionCount($userId);

        return $currentCount < $maxConnections;
    }

    /**
     * Check if a free user can change/disconnect (3-day cooldown).
     * Paid users can always switch.
     */
    public function canChangeConnection(int $userId): bool
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user) {
            return false;
        }

        // Paid users have no cooldown
        if (($user->plan ?? 'free') === 'paid') {
            return true;
        }

        // Free users: check 3-day cooldown from last_code_change
        if (empty($user->last_code_change)) {
            return true;
        }

        $lastChange = strtotime($user->last_code_change);
        $cooldownEnd = $lastChange + (3 * 24 * 3600); // 3 days

        return time() >= $cooldownEnd;
    }

    /**
     * Get the remaining cooldown time in seconds (0 if no cooldown).
     */
    public function getCooldownRemaining(int $userId): int
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user || ($user->plan ?? 'free') === 'paid' || empty($user->last_code_change)) {
            return 0;
        }

        $cooldownEnd = strtotime($user->last_code_change) + (3 * 24 * 3600);
        $remaining = $cooldownEnd - time();

        return max(0, $remaining);
    }

    /**
     * Check if a specific connection already exists.
     */
    public function connectionExists(int $userId, int $connectedTo): bool
    {
        return $this->where('user_id', $userId)
            ->where('connected_to', $connectedTo)
            ->countAllResults() > 0;
    }

    /**
     * Get all user IDs that are mutually visible to a given user.
     * This includes: people I connected to + people who connected to me.
     */
    public function getAllConnectedUserIds(int $userId): array
    {
        // People I connected to
        $outgoing = $this->where('user_id', $userId)->findAll();
        $outIds = array_map(fn($c) => (int) $c->connected_to, $outgoing);

        // People who connected to me
        $incoming = $this->where('connected_to', $userId)->findAll();
        $inIds = array_map(fn($c) => (int) $c->user_id, $incoming);

        return array_unique(array_merge($outIds, $inIds));
    }
}
