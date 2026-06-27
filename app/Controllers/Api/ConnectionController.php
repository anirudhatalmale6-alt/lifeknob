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

        $user = $userModel->find($userId);
        $max = $user->max_connections ?? (($user->plan ?? 'free') === 'paid' ? 5 : 1);

        // Check if connection already exists
        $existingConn = $connectionModel->where('user_id', $userId)
            ->where('connected_to', $targetUser->id)
            ->first();

        if ($existingConn) {
            if ($existingConn->status === 'inactive') {
                // Reactivating after disconnect - check limit
                $currentCount = $connectionModel->getConnectionCount($userId);
                if ($currentCount >= $max) {
                    return $this->fail("Connection limit reached. Your plan allows {$max} connection(s).", 403);
                }
                $connectionModel->update($existingConn->id, ['status' => 'pending']);
                $connId = $existingConn->id;
            } else {
                return $this->fail('You already have a connection to this person', 409);
            }
        } else {
            // New connection - check limit
            $currentCount = $connectionModel->getConnectionCount($userId);
            if ($currentCount >= $max) {
                return $this->fail("Connection limit reached. Your plan allows {$max} connection(s).", 403);
            }
            $connId = $connectionModel->connect($userId, $targetUser->id);
            if (!$connId) {
                return $this->failServerError('Failed to create connection');
            }
        }

        $connStatus = 'pending';

        // Auto-connect for test users (TEST prefix codes)
        if (str_starts_with($targetUser->user_code, 'TEST')) {
            $connectionModel->update($connId, ['status' => 'accepted']);
            if (!$connectionModel->connectionExists($targetUser->id, $userId)) {
                $reverseId = $connectionModel->connect($targetUser->id, $userId);
                if ($reverseId) {
                    $connectionModel->update($reverseId, ['status' => 'accepted']);
                }
            } else {
                $connectionModel->where('user_id', $targetUser->id)
                    ->where('connected_to', $userId)
                    ->set(['status' => 'accepted'])->update();
            }
            $connStatus = 'accepted';
        } else {
            // Auto-accept: if the other person already added us
            $reverseConn = $connectionModel->where('user_id', $targetUser->id)
                ->where('connected_to', $userId)
                ->first();

            if ($reverseConn) {
                $connectionModel->update($connId, ['status' => 'accepted']);
                $connectionModel->update($reverseConn->id, ['status' => 'accepted']);
                $connStatus = 'accepted';
            }
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => $connStatus === 'accepted'
                ? 'Connected with ' . $targetUser->name . '!'
                : 'Connection request sent to ' . $targetUser->name,
            'data'    => [
                'connection_id'     => $connId,
                'connection_status' => $connStatus,
                'connected_to'      => [
                    'user_id'   => $targetUser->id,
                    'name'      => $targetUser->name,
                    'user_code' => $targetUser->user_code,
                ],
            ],
        ]);
    }

    public function acceptRequest()
    {
        $userId = $this->getUserId();
        $connectionId = $this->input('connection_id');

        if (!$userId || !$connectionId) {
            return $this->failValidationErrors('connection_id is required');
        }

        $connectionModel = new ConnectionModel();
        $result = $connectionModel->acceptConnection((int) $connectionId, $userId);

        if (!$result) {
            return $this->failNotFound('Request not found or already processed');
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Connection accepted',
        ]);
    }

    public function rejectRequest()
    {
        $userId = $this->getUserId();
        $connectionId = $this->input('connection_id');

        if (!$userId || !$connectionId) {
            return $this->failValidationErrors('connection_id is required');
        }

        $connectionModel = new ConnectionModel();
        $result = $connectionModel->rejectConnection((int) $connectionId, $userId);

        return $this->respond([
            'status'  => 'success',
            'message' => 'Connection rejected',
        ]);
    }

    public function pendingRequests()
    {
        $userId = $this->getUserId();

        if (!$userId) {
            return $this->failValidationErrors('Authentication required');
        }

        $connectionModel = new ConnectionModel();
        $requests = $connectionModel->getPendingRequests($userId);

        $result = [];
        foreach ($requests as $req) {
            $result[] = [
                'connection_id' => $req->connection_id,
                'user_id'       => $req->user_id,
                'name'          => $req->name,
                'user_code'     => $req->user_code,
                'avatar'        => $req->avatar ?? null,
                'requested_at'  => $req->requested_at,
            ];
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $result,
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

        $connectionModel->disconnect($userId, $connectedTo);

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
            $data = [
                'id'              => $conn->connection_id ?? $conn->user_id,
                'user_id'         => $conn->user_id,
                'name'            => $conn->name,
                'user_code'       => $conn->user_code,
                'avatar'          => $conn->avatar ?? null,
                'status'          => $conn->status ?? 'accepted',
                'connected_since' => $conn->connected_since ?? null,
            ];

            if (($conn->status ?? 'accepted') === 'accepted') {
                $latestCheckIn = $checkInModel->getLatestCheckIn($conn->user_id);
                $data['last_check_in'] = $latestCheckIn ? $latestCheckIn->created_at : null;
                $data['last_check_in_type'] = $latestCheckIn ? $latestCheckIn->type : null;
            }

            $result[] = $data;
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
            $data = [
                'id'              => $watcher->connection_id ?? $watcher->user_id,
                'user_id'         => $watcher->user_id,
                'name'            => $watcher->name,
                'user_code'       => $watcher->user_code,
                'avatar'          => $watcher->avatar ?? null,
                'status'          => $watcher->status ?? 'accepted',
                'connected_since' => $watcher->connected_since ?? null,
            ];

            if (($watcher->status ?? 'accepted') === 'accepted') {
                $latestCheckIn = $checkInModel->getLatestCheckIn($watcher->user_id);
                $data['last_check_in'] = $latestCheckIn ? $latestCheckIn->created_at : null;
                $data['last_check_in_type'] = $latestCheckIn ? $latestCheckIn->type : null;
            }

            $result[] = $data;
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

        $maxConnections = $user->max_connections ?? (($user->plan ?? 'free') === 'paid' ? 5 : 1);
        $currentCount = $connectionModel->getConnectionCount($userId);
        $canChange = $connectionModel->canChangeConnection($userId);
        $cooldownRemaining = $connectionModel->getCooldownRemaining($userId);

        return $this->respond([
            'status' => 'success',
            'data'   => [
                'user_code'          => $user->user_code,
                'plan'               => $user->plan ?? 'free',
                'max_connections'    => $maxConnections,
                'current_connections' => $currentCount,
                'can_change'         => $canChange,
                'cooldown_remaining_seconds' => $cooldownRemaining,
                'cooldown_remaining_hours'   => $cooldownRemaining > 0 ? ceil($cooldownRemaining / 3600) : 0,
            ],
        ]);
    }

    public function updateConnection()
    {
        $userId = $this->getUserId();
        $connectionId = $this->input("connection_id");
        $name = $this->input("name");

        if (!$userId || !$connectionId || !$name) {
            return $this->failValidationErrors("connection_id and name are required");
        }

        $connectionModel = new ConnectionModel();
        $conn = $connectionModel->find($connectionId);

        if (!$conn || (int) $conn->user_id !== (int) $userId) {
            return $this->failNotFound("Connection not found");
        }

        $connectionModel->update($connectionId, ["display_name" => $name]);

        return $this->respond([
            "status" => "success",
            "message" => "Connection name updated",
        ]);
    }
}
