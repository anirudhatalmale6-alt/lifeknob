<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\ConnectionModel;
use App\Models\UserModel;
use App\Models\CheckInModel;

class ConnectionController extends ResourceController
{
    protected $format = 'json';

    /**
     * POST /api/connection/connect
     * Body: user_id, code (the target user's 8-char code)
     */
    public function connect()
    {
        $userId = $this->request->getPost('user_id');
        $code   = $this->request->getPost('code');

        if (!$userId || !$code) {
            return $this->failValidationErrors('user_id and code are required');
        }

        $userModel = new UserModel();
        $connectionModel = new ConnectionModel();

        // Find the code owner
        $targetUser = $userModel->findByCode($code);
        if (!$targetUser) {
            return $this->failNotFound('No user found with that code');
        }

        // Can't connect to yourself
        if ((int) $targetUser->id === (int) $userId) {
            return $this->fail('You cannot connect to yourself', 400);
        }

        // Check if already connected
        if ($connectionModel->connectionExists($userId, $targetUser->id)) {
            return $this->fail('You are already connected to this person', 409);
        }

        // Check free slot
        if (!$connectionModel->canConnect($userId)) {
            $user = $userModel->find($userId);
            $plan = $user->plan ?? 'free';
            $max = $plan === 'paid' ? 5 : 1;
            return $this->fail("Connection limit reached. Your plan allows {$max} connection(s). Disconnect someone first.", 403);
        }

        // For free users adding a new connection when they already have/had one, check cooldown
        $currentCount = $connectionModel->getConnectionCount($userId);
        $user = $userModel->find($userId);
        if (($user->plan ?? 'free') === 'free' && !empty($user->last_code_change)) {
            if (!$connectionModel->canChangeConnection($userId)) {
                $remaining = $connectionModel->getCooldownRemaining($userId);
                $hours = ceil($remaining / 3600);
                return $this->fail("You can change your connection in {$hours} hours (3-day cooldown for free users).", 429);
            }
        }

        // Create the connection
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

    /**
     * POST /api/connection/disconnect
     * Body: user_id, connected_to (user ID to disconnect from)
     */
    public function disconnect()
    {
        $userId      = $this->request->getPost('user_id');
        $connectedTo = $this->request->getPost('connected_to');

        if (!$userId || !$connectedTo) {
            return $this->failValidationErrors('user_id and connected_to are required');
        }

        $connectionModel = new ConnectionModel();
        $userModel = new UserModel();

        // Check the connection exists
        if (!$connectionModel->connectionExists($userId, $connectedTo)) {
            return $this->failNotFound('Connection not found');
        }

        // For free users, check cooldown before allowing disconnect (which enables switching)
        $user = $userModel->find($userId);
        if (($user->plan ?? 'free') === 'free') {
            if (!$connectionModel->canChangeConnection($userId)) {
                $remaining = $connectionModel->getCooldownRemaining($userId);
                $hours = ceil($remaining / 3600);
                return $this->fail("You can change your connection in {$hours} hours (3-day cooldown for free users).", 429);
            }
        }

        // Remove the connection
        $connectionModel->disconnect($userId, $connectedTo);

        // For free users, set the cooldown timestamp
        if (($user->plan ?? 'free') === 'free') {
            $userModel->update($userId, ['last_code_change' => date('Y-m-d H:i:s')]);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Disconnected successfully',
        ]);
    }

    /**
     * GET /api/connection/mine?user_id=X
     * List all people I'm connected to (whose codes I entered), with their latest check-in.
     */
    public function myConnections()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $connectionModel = new ConnectionModel();
        $checkInModel = new CheckInModel();

        $connections = $connectionModel->getConnectionsForUser($userId);

        $result = [];
        foreach ($connections as $conn) {
            $latestCheckIn = $checkInModel->getLatestCheckIn($conn->user_id);
            $result[] = [
                'user_id'         => $conn->user_id,
                'name'            => $conn->name,
                'user_code'       => $conn->user_code,
                'connected_since' => $conn->connected_since,
                'last_seen_at'    => $conn->last_seen_at,
                'latest_checkin'  => $latestCheckIn ? [
                    'type'       => $latestCheckIn->type,
                    'created_at' => $latestCheckIn->created_at,
                    'note'       => $latestCheckIn->note ?? null,
                ] : null,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/connection/watchers?user_id=X
     * List all people who are connected to me (entered my code).
     */
    public function connectedToMe()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
        }

        $connectionModel = new ConnectionModel();
        $checkInModel = new CheckInModel();

        $watchers = $connectionModel->getConnectedToMe($userId);

        $result = [];
        foreach ($watchers as $watcher) {
            $latestCheckIn = $checkInModel->getLatestCheckIn($watcher->user_id);
            $result[] = [
                'user_id'         => $watcher->user_id,
                'name'            => $watcher->name,
                'user_code'       => $watcher->user_code,
                'connected_since' => $watcher->connected_since,
                'last_seen_at'    => $watcher->last_seen_at,
                'latest_checkin'  => $latestCheckIn ? [
                    'type'       => $latestCheckIn->type,
                    'created_at' => $latestCheckIn->created_at,
                    'note'       => $latestCheckIn->note ?? null,
                ] : null,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
        ]);
    }

    /**
     * GET /api/connection/info?user_id=X
     * Get connection info: current count, max allowed, plan, cooldown status.
     */
    public function info()
    {
        $userId = $this->request->getGet('user_id');

        if (!$userId) {
            return $this->failValidationErrors('user_id required');
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
