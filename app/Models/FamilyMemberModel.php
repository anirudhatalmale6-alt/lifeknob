<?php

namespace App\Models;

use CodeIgniter\Model;

class FamilyMemberModel extends Model
{
    protected $table            = 'family_members';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'group_id',
        'user_id',
        'role',
        'joined_at',
    ];

    /**
     * Get all members of a group with full user info.
     */
    public function getMembersOfGroup(int $groupId): array
    {
        return $this->select('family_members.*, users.name, users.email, users.phone, users.avatar, users.role as user_role, users.last_seen_at, users.is_active')
            ->join('users', 'users.id = family_members.user_id')
            ->where('family_members.group_id', $groupId)
            ->findAll();
    }

    /**
     * Get only elder members of a group with user info.
     */
    public function getEldersInGroup(int $groupId): array
    {
        return $this->select('family_members.*, users.name, users.email, users.phone, users.avatar, users.last_seen_at, users.is_active')
            ->join('users', 'users.id = family_members.user_id')
            ->where('family_members.group_id', $groupId)
            ->where('users.role', 'elder')
            ->findAll();
    }

    /**
     * Get only family (non-elder) members of a group with user info.
     */
    public function getFamilyInGroup(int $groupId): array
    {
        return $this->select('family_members.*, users.name, users.email, users.phone, users.avatar, users.last_seen_at, users.is_active')
            ->join('users', 'users.id = family_members.user_id')
            ->where('family_members.group_id', $groupId)
            ->where('users.role', 'family')
            ->findAll();
    }

    /**
     * Check whether a user belongs to a specific group.
     */
    public function isUserInGroup(int $userId, int $groupId): bool
    {
        return $this->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->countAllResults() > 0;
    }
}
