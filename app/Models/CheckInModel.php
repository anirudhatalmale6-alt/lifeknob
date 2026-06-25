<?php

namespace App\Models;

use CodeIgniter\Model;

class CheckInModel extends Model
{
    protected $table            = 'check_ins';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'user_id',
        'type',
        'latitude',
        'longitude',
        'note',
        'created_at',
    ];

    /**
     * Get the most recent check-in for a user.
     */
    public function getLatestCheckIn(int $userId): ?object
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Get check-in history for a user with a limit.
     */
    public function getCheckInHistory(int $userId, int $limit = 50): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get all check-ins for a user on a specific date (Y-m-d).
     */
    public function getCheckInsForDate(int $userId, string $date): array
    {
        return $this->where('user_id', $userId)
            ->where('DATE(created_at)', $date)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    /**
     * Get check-in stats: total count, current streak, and last type.
     */
    public function getCheckInStats(int $userId): object
    {
        $total = $this->where('user_id', $userId)->countAllResults(false);

        $latest = $this->getLatestCheckIn($userId);
        $lastType = $latest ? $latest->type : null;

        // Calculate streak: consecutive days with at least one check-in
        $streak = 0;
        $date = date('Y-m-d');

        while (true) {
            $count = $this->where('user_id', $userId)
                ->where('DATE(created_at)', $date)
                ->countAllResults(false);

            if ($count === 0) {
                // Allow today to have no check-in yet (streak still valid from yesterday)
                if ($date === date('Y-m-d') && $streak === 0) {
                    $date = date('Y-m-d', strtotime($date . ' -1 day'));
                    continue;
                }
                break;
            }

            $streak++;
            $date = date('Y-m-d', strtotime($date . ' -1 day'));
        }

        return (object) [
            'total'     => $total,
            'streak'    => $streak,
            'last_type' => $lastType,
        ];
    }

    /**
     * Check if a user has checked in within the given number of hours.
     */
    public function hasCheckedInWithin(int $userId, int $hours): bool
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));

        return $this->where('user_id', $userId)
            ->where('created_at >=', $cutoff)
            ->countAllResults() > 0;
    }
}
