<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'email',
        'password',
        'name',
        'phone',
        'role',
        'avatar',
        'firebase_token',
        'timezone',
        'is_active',
        'last_seen_at',
        'user_code',
        'plan',
        'last_code_change',
        'sos_number',
        'sos_name',
        'ambulance_number',
        'device_id',
        'quiet_hours_enabled',
    ];

    protected $validationRules = [];
    protected $skipValidation = true;

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email address is already registered.',
        ],
    ];

    protected $beforeInsert = [];
    protected $beforeUpdate = [];

    /**
     * Find a user by email address.
     */
    public function findByEmail(string $email): ?object
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Get all users with role=elder.
     */
    public function getElders(): array
    {
        return $this->where('role', 'elder')->findAll();
    }

    /**
     * Get all users with role=family.
     */
    public function getFamilyMembers(): array
    {
        return $this->where('role', 'family')->findAll();
    }

    /**
     * Update a user's Firebase Cloud Messaging token.
     */
    public function updateFirebaseToken(int $userId, string $token): bool
    {
        return $this->update($userId, ['firebase_token' => $token]);
    }

    /**
     * Update the last_seen_at timestamp for a user.
     */
    public function updateLastSeen(int $userId): bool
    {
        return $this->update($userId, ['last_seen_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * Find a user by their unique connection code.
     */
    public function findByCode(string $code): ?object
    {
        return $this->where('user_code', $code)->first();
    }

    public function findByDeviceId(string $deviceId): ?object
    {
        return $this->where('device_id', $deviceId)->first();
    }

    /**
     * Generate a unique 8-character uppercase alphanumeric user code.
     */
    public function generateUserCode(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while ($this->where('user_code', $code)->countAllResults() > 0);

        return $code;
    }
}
