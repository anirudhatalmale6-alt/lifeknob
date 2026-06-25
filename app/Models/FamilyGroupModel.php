<?php

namespace App\Models;

use CodeIgniter\Model;

class FamilyGroupModel extends Model
{
    protected $table            = 'family_groups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'name',
        'invite_code',
        'created_by',
    ];

    /**
     * Find a group by its invite code.
     */
    public function findByInviteCode(string $code): ?object
    {
        return $this->where('invite_code', $code)->first();
    }

    /**
     * Get all groups that a user belongs to (via family_members join).
     */
    public function getGroupsForUser(int $userId): array
    {
        return $this->select('family_groups.*')
            ->join('family_members', 'family_members.group_id = family_groups.id')
            ->where('family_members.user_id', $userId)
            ->findAll();
    }

    /**
     * Generate a unique 8-character alphanumeric invite code.
     */
    public function generateInviteCode(): string
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while ($this->where('invite_code', $code)->countAllResults() > 0);

        return $code;
    }
}
