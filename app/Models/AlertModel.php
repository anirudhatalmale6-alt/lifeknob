<?php

namespace App\Models;

use CodeIgniter\Model;

class AlertModel extends Model
{
    protected $table            = 'alerts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'group_id',
        'elder_id',
        'type',
        'message',
        'is_resolved',
        'resolved_by',
        'resolved_at',
        'created_at',
    ];

    /**
     * Get all unresolved alerts for a group.
     */
    public function getActiveAlerts(int $groupId): array
    {
        return $this->where('group_id', $groupId)
            ->where('is_resolved', 0)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get all alerts for a specific elder.
     */
    public function getAlertsForElder(int $elderId): array
    {
        return $this->where('elder_id', $elderId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Mark an alert as resolved.
     */
    public function resolveAlert(int $alertId, int $resolvedBy): bool
    {
        return $this->update($alertId, [
            'is_resolved' => 1,
            'resolved_by' => $resolvedBy,
            'resolved_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get recent alerts for a group with a limit.
     */
    public function getRecentAlerts(int $groupId, int $limit = 20): array
    {
        return $this->where('group_id', $groupId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
