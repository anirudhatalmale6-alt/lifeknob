<?php

namespace App\Controllers\Api;

use App\Models\ConnectionModel;
use App\Models\UserModel;
use App\Models\CheckInModel;

class ConnectionController extends ApiBaseController
{
    public function connect()
    {
        $userId = $this->getUserId();
        $code   = $this->input('code');

        if (!$userId || !$code) {
            return $this->failValidationErrors('code is required');
        }

        $userModel = new UserModel();
        $connectionModel = new ConnectionModel();

        $targetUser = $userModel->findByCode($code);
        if (!$targetUser) {
            return $this->failNotFound('No user found with that code');
        }

        if ((int) $targetUser->id === (int) $userId) {
            return $this->fail('You cannot connect to yourself', 400);
        }

        if ($connectionModel->connectionExists($userId, $targetUser->id)) {
            return $this->fail('You are already connected to this person', 409);
        }

        if (!$connectionModel->canConnect($userId)) {
            $user = $userModel->find($userId);
            $plan = $user->plan ?? 'free';
            $max = $plan === 'paid' ? 5 : 1;
            return $this->fail("Connection limit reached. Your plan allows {$max} connection(s).", 403);
        }

        $user = $userModel->find($userId);
        if (($user->plan ?? 'free') === 'free' && !empty($user->last_code_change)) {
            if (!$connectionModel->canChangeConnection($userId)) {
                $remaining = $connectionModel->getCooldownRemaining($userId);
                $hours = ceil($remaining / 3600);
                return $this->fail("You can change your connection in {$hours} hours (3-day cooldown for free users).", 429);
            }
        }

        $connId = $connectionModel->connect($userId, $targetUser->id);
        if (!$connId) {
            return $this->failServerError('Failed to create connection');
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Connected to ' . $targetUser->name,
            'data'    => [
                'connection_id' => $connId,
                'connected_to'  => [
                    'user_id'   => $targetUser->id,
                    'name'      => $targetUser->name,
                    'user_code' => $targetUser->user_code,
                ],
            ],
        ]);
    }

    public function disconnect()
    {
        $userId      = $this->getUserId();
        $connectedTo = $this->input('connected_to') ?? $this->input('connection_id');

        if (!$userId || !$connectedTo) {
            return $this->failValidationErrors('connected_to is required');
        }

        $connectionModel = new ConnectionModel();
        $userModel = new UserModel();

        if (!$connectionModel->connectionExists($userId, $connectedTo)) {
            return $this->failNotFound('Connection not found');
        }

        $user = $userModel->find($userId);
        if (($user->plan ?? 'free') === 'free') {
            if (!$connectionModel->canChangeConnection($userId)) {
                $remaining = $connectionModel->getCooldownRemaining($userId);
                $hours = ceil($remaining / 3600);
                return $this->fail("You can change your connection in {$hours} hours (3-day cooldown).", 429);
            }
        }

        $connectionModel->disconnect($userId, $connectedTo);

        if (($user->plan ?? 'free') === 'free') {
            $userModel->update($userId, ['last_code_change' => date('Y-m-d H:i:s')]);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Disconnected successfully',
        ]);
    }

    public function myConnections()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $connectionModel = new ConnectionModel();
        $checkInModel = new CheckInModel();

        $connections = $connectionModel->getConnectionsForUser($userId);

        $result = [];
        foreach ($connections as $conn) {
            $latestCheckIn = $checkInModel->getLatestCheckIn($conn->user_id);
            $result[] = [
                'id'              => $conn->id ?? $conn->user_id,
                'user_id'         => $conn->user_id,
                'name'            => $conn->name,
                'user_code'       => $conn->user_code,
                'connected_since' => $conn->connected_since ?? null,
                'last_check_in'   => $latestCheckIn ? $latestCheckIn->created_at : null,
                'last_check_in_type' => $latestCheckIn ? $latestCheckIn->type : null,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    public function connectedToMe()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $connectionModel = new ConnectionModel();
        $checkInModel = new CheckInModel();

        $watchers = $connectionModel->getConnectedToMe($userId);

        $result = [];
        foreach ($watchers as $watcher) {
            $latestCheckIn = $checkInModel->getLatestCheckIn($watcher->user_id);
            $result[] = [
                'id'              => $watcher->id ?? $watcher->user_id,
                'user_id'         => $watcher->user_id,
                'name'            => $watcher->name,
                'user_code'       => $watcher->user_code,
                'connected_since' => $watcher->connected_since ?? null,
                'last_check_in'   => $latestCheckIn ? $latestCheckIn->created_at : null,
                'last_check_in_type' => $latestCheckIn ? $latestCheckIn->type : null,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    public function info()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $userModel = new UserModel();
        $connectionModel = new ConnectionModel();

        $user = $userModel->find($userId);
        if (!$user) {
            return $this->failNotFound('User not found');
        }

        $plan = $user->plan ?? 'free';
        $maxConnections = $plan === 'paid' ? 5 : 1;
        $currentCount = $connectionModel->getConnectionCount($userId);
        $canChange = $connectionModel->canChangeConnection($userId);
        $cooldownRemaining = $connectionModel->getCooldownRemaining($userId);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'user_code'          => $user->user_code,
                'plan'               => $plan,
                'max_connections'    => $maxConnections,
                'current_connections' => $currentCount,
                'can_change'         => $canChange,
                'cooldown_remaining_seconds' => $cooldownRemaining,
                'cooldown_remaining_hours'   => $cooldownRemaining > 0 ? ceil($cooldownRemaining / 3600) : 0,
            ],
        ]);
    }
}
