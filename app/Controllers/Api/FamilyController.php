<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\FamilyGroupModel;
use App\Models\FamilyMemberModel;
use App\Models\UserModel;

class FamilyController extends ResourceController
{
    protected $format = 'json';

    public function createGroup()
    {
        $userId = $this->request->getPost('user_id');
        $name   = $this->request->getPost('name');

        if (!$userId || !$name) {
            return $this->failValidationErrors('user_id and name required');
        }

        $groupModel = new FamilyGroupModel();

        $groupId = $groupModel->insert([
            'name'       => $name,
            'invite_code' => $groupModel->generateInviteCode(),
            'created_by' => $userId,
        ]);

        if (!$groupId) {
            return $this->failServerError('Failed to create group');
        }

        $memberModel = new FamilyMemberModel();
        $user = (new UserModel())->find($userId);
        $memberModel->insert([
            'group_id'  => $groupId,
            'user_id'   => $userId,
            'role'      => $user->role ?? 'family',
            'joined_at' => date('Y-m-d H:i:s'),
        ]);

        $group = $groupModel->find($groupId);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Family group created',
            'data'    => [
                'group_id'    => $groupId,
                'name'        => $group->name,
                'invite_code' => $group->invite_code,
            ],
        ]);
    }

    public function joinGroup()
    {
        $userId     = $this->request->getPost('user_id');
        $inviteCode = $this->request->getPost('invite_code');

        if (!$userId || !$inviteCode) {
            return $this->failValidationErrors('user_id and invite_code required');
        }

        $groupModel = new FamilyGroupModel();
        $group = $groupModel->findByInviteCode($inviteCode);

        if (!$group) {
            return $this->failNotFound('Invalid invite code');
        }

        $memberModel = new FamilyMemberModel();

        if ($memberModel->isUserInGroup($userId, $group->id)) {
            return $this->fail('Already a member of this group');
        }

        $user = (new UserModel())->find($userId);
        $memberModel->insert([
            'group_id'  => $group->id,
            'user_id'   => $userId,
            'role'      => $user->role ?? 'family',
            'joined_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Joined family group: ' . $group->name,
            'data'    => [
                'group_id' => $group->id,
                'name'     => $group->name,
            ],
        ]);
    }

    public function getGroups()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $groupModel = new FamilyGroupModel();
        $groups = $groupModel->getGroupsForUser($userId);

        $memberModel = new FamilyMemberModel();
        $result = [];

        foreach ($groups as $group) {
            $members = $memberModel->getMembersOfGroup($group->id);
            $result[] = [
                'id'          => $group->id,
                'name'        => $group->name,
                'invite_code' => $group->invite_code,
                'members'     => $members,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    public function getMembers()
    {
        $groupId = $this->request->getGet('group_id');

        if (!$groupId) {
            return $this->failValidationErrors('group_id required');
        }

        $memberModel = new FamilyMemberModel();
        $members = $memberModel->getMembersOfGroup($groupId);

        return $this->respond([
            'status' => 'success',
            'data'   => $members,
        ]);
    }

    public function leaveGroup()
    {
        $userId  = $this->request->getPost('user_id');
        $groupId = $this->request->getPost('group_id');

        if (!$userId || !$groupId) {
            return $this->failValidationErrors('user_id and group_id required');
        }

        $memberModel = new FamilyMemberModel();
        $memberModel->where('user_id', $userId)->where('group_id', $groupId)->delete();

        return $this->respond([
            'status'  => 'success',
            'message' => 'Left the group',
        ]);
    }
}
