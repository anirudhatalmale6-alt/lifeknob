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
    ];

    protected $validationRules = [
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]',
        'name'     => 'required|min_length[2]',
        'role'     => 'required|in_list[elder,family,admin]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email address is already registered.',
        ],
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before saving.
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);
        }

        return $data;
    }

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
}
