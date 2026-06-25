<?php

namespace App\Models;

use CodeIgniter\Model;

class CheckInSettingModel extends Model
{
    protected $table            = 'check_in_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'user_id',
        'frequency_hours',
        'reminder_minutes',
        'alert_delay_minutes',
        'quiet_hours_start',
        'quiet_hours_end',
        'is_active',
    ];

    /**
     * Get settings for a user, creating defaults if none exist.
     */
    public function getSettingsForUser(int $userId): object
    {
        $settings = $this->where('user_id', $userId)->first();

        if ($settings === null) {
            $this->insert([
                'user_id'              => $userId,
                'frequency_hours'      => 12,
                'reminder_minutes'     => 30,
                'alert_delay_minutes'  => 60,
                'quiet_hours_start'    => '22:00',
                'quiet_hours_end'      => '07:00',
                'is_active'            => 1,
            ]);

            $settings = $this->find($this->getInsertID());
        }

        return $settings;
    }

    /**
     * Get all active elder settings with user data (for cron jobs).
     */
    public function getActiveElderSettings(): array
    {
        return $this->select('check_in_settings.*, users.name, users.email, users.firebase_token, users.timezone, users.last_seen_at')
            ->join('users', 'users.id = check_in_settings.user_id')
            ->where('check_in_settings.is_active', 1)
            ->where('users.role', 'elder')
            ->where('users.is_active', 1)
            ->where('users.deleted_at', null)
            ->findAll();
    }
}
