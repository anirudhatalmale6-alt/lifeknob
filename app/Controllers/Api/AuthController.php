<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\CheckInSettingModel;

class AuthController extends ApiBaseController
{
    public function register()
    {
        $email    = $this->input('email');
        $password = $this->input('password');
        $name     = $this->input('name');
        $phone    = $this->input('phone');
        $timezone = $this->input('timezone') ?? 'UTC';

        if (empty($email) || empty($password) || empty($name)) {
            return $this->failValidationErrors('name, email, and password are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->failValidationErrors('Invalid email address');
        }

        if (strlen($password) < 6) {
            return $this->failValidationErrors('Password must be at least 6 characters');
        }

        $userModel = new UserModel();

        if ($userModel->where('email', $email)->first()) {
            return $this->fail('This email address is already registered.', 409);
        }

        $userCode = $userModel->generateUserCode();

        $userData = [
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'name'      => $name,
            'role'      => 'elder',
            'phone'     => $phone,
            'timezone'  => $timezone,
            'user_code' => $userCode,
        ];

        $userId = $userModel->insert($userData);
        if (!$userId) {
            return $this->failServerError('Registration failed');
        }

        $settingModel = new CheckInSettingModel();
        $settingModel->insert([
            'user_id'              => $userId,
            'frequency_hours'      => 24,
            'reminder_minutes'     => 30,
            'alert_delay_minutes'  => 60,
            'is_active'            => 1,
        ]);

        $token = $this->generateToken($userId);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Registration successful',
            'token'   => $token,
            'user'    => [
                'id'        => (int) $userId,
                'name'      => $name,
                'email'     => $email,
                'phone'     => $phone ?? '',
                'user_code' => $userCode,
                'plan'      => 'free',
                'max_connections'  => 1,
                'used_connections' => 0,
            ],
        ]);
    }

    public function login()
    {
        $email    = $this->input('email');
        $password = $this->input('password');

        if (empty($email) || empty($password)) {
            return $this->failValidationErrors('email and password are required');
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user->password)) {
            return $this->failUnauthorized('Invalid email or password');
        }

        if (!$user->is_active) {
            return $this->failForbidden('Account is deactivated');
        }

        $token = $this->generateToken($user->id);
        $userModel->updateLastSeen($user->id);

        $firebaseToken = $this->input('firebase_token');
        if ($firebaseToken) {
            $userModel->updateFirebaseToken($user->id, $firebaseToken);
        }

        $db = \Config\Database::connect();
        $usedConnections = $db->table('connections')
            ->where('user_id', $user->id)
            ->countAllResults();

        $maxConnections = ($user->plan ?? 'free') === 'paid' ? 5 : 1;

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'        => (int) $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'phone'     => $user->phone ?? '',
                'user_code' => $user->user_code,
                'plan'      => $user->plan ?? 'free',
                'max_connections'  => $maxConnections,
                'used_connections' => $usedConnections,
            ],
        ]);
    }

    public function autoRegister()
    {
        $deviceId = $this->input('device_id');

        if (empty($deviceId)) {
            return $this->failValidationErrors('device_id is required');
        }

        $userModel = new UserModel();

        $existing = $userModel->findByDeviceId($deviceId);
        if ($existing) {
            $token = $this->generateToken($existing->id);
            $userModel->updateLastSeen($existing->id);

            $db = \Config\Database::connect();
            $usedConnections = $db->table('connections')
                ->where('user_id', $existing->id)
                ->countAllResults();
            $maxConnections = ($existing->plan ?? 'free') === 'paid' ? 5 : 1;

            return $this->respond([
                'status'  => 'success',
                'message' => 'Welcome back',
                'token'   => $token,
                'user'    => [
                    'id'               => (int) $existing->id,
                    'name'             => $existing->name ?? '',
                    'email'            => $existing->email ?? '',
                    'phone'            => $existing->phone ?? '',
                    'user_code'        => $existing->user_code,
                    'plan'             => $existing->plan ?? 'free',
                    'max_connections'  => $maxConnections,
                    'used_connections' => $usedConnections,
                    'sos_number'       => $existing->sos_number ?? null,
                    'sos_name'         => $existing->sos_name ?? null,
                    'ambulance_number' => $existing->ambulance_number ?? null,
                    'avatar'           => $existing->avatar ?? null,
                ],
            ]);
        }

        $userCode = $userModel->generateUserCode();

        $userId = $userModel->insert([
            'device_id'  => $deviceId,
            'user_code'  => $userCode,
            'role'       => 'elder',
            'name'       => '',
            'password'   => '',
            'is_active'  => 1,
        ]);

        if (!$userId) {
            return $this->failServerError('Could not create account');
        }

        $settingModel = new CheckInSettingModel();
        $settingModel->insert([
            'user_id'              => $userId,
            'frequency_hours'      => 24,
            'reminder_minutes'     => 30,
            'alert_delay_minutes'  => 60,
            'is_active'            => 1,
        ]);

        // First check-in: registration = alive
        $db = \Config\Database::connect();
        $db->table('check_ins')->insert([
            'user_id'    => $userId,
            'type'       => 'ok',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $token = $this->generateToken($userId);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Account created',
            'token'   => $token,
            'user'    => [
                'id'               => (int) $userId,
                'name'             => '',
                'email'            => '',
                'phone'            => '',
                'user_code'        => $userCode,
                'plan'             => 'free',
                'max_connections'  => 1,
                'used_connections' => 0,
                'sos_number'       => null,
                'sos_name'         => null,
                'ambulance_number' => null,
                'avatar'           => null,
            ],
        ]);
    }

    public function updateFirebaseToken()
    {
        $userId = $this->getUserId();
        $token  = $this->input('firebase_token');

        if (!$userId || !$token) {
            return $this->failValidationErrors('firebase_token required');
        }

        $userModel = new UserModel();
        $userModel->updateFirebaseToken($userId, $token);

        return $this->respond(['status' => 'success']);
    }

    private function generateToken(int $userId): string
    {
        $payload = [
            'user_id'    => $userId,
            'issued_at'  => time(),
            'expires_at' => time() + (86400 * 30),
        ];

        $secret = env('JWT_SECRET', getenv('JWT_SECRET') ?: 'lifeknob_secret_key_change_in_production');
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$payload", $secret);

        return "$header.$payload.$signature";
    }
}
