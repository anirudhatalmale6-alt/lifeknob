<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use App\Models\CheckInSettingModel;

class AuthController extends ResourceController
{
    protected $format = 'json';

    public function register()
    {
        $rules = [
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'name'     => 'required|min_length[2]|max_length[100]',
            'role'     => 'required|in_list[elder,family]',
            'phone'    => 'permit_empty|max_length[20]',
            'timezone' => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userModel = new UserModel();

        $userData = [
            'email'     => $this->request->getPost('email'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'name'      => $this->request->getPost('name'),
            'role'      => $this->request->getPost('role'),
            'phone'     => $this->request->getPost('phone') ?? null,
            'timezone'  => $this->request->getPost('timezone') ?? 'UTC',
            'user_code' => $userModel->generateUserCode(),
        ];

        $userId = $userModel->insert($userData);
        if (!$userId) {
            return $this->failServerError('Registration failed');
        }

        if ($userData['role'] === 'elder') {
            $settingModel = new CheckInSettingModel();
            $settingModel->insert([
                'user_id'              => $userId,
                'frequency_hours'      => 24,
                'reminder_minutes'     => 30,
                'alert_delay_minutes'  => 60,
                'is_active'            => 1,
            ]);
        }

        $token = $this->generateToken($userId);

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Registration successful',
            'data'    => [
                'user_id'   => $userId,
                'token'     => $token,
                'role'      => $userData['role'],
                'user_code' => $userData['user_code'],
            ],
        ]);
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($this->request->getPost('email'));

        if (!$user || !password_verify($this->request->getPost('password'), $user->password)) {
            return $this->failUnauthorized('Invalid email or password');
        }

        if (!$user->is_active) {
            return $this->failForbidden('Account is deactivated');
        }

        $token = $this->generateToken($user->id);
        $userModel->updateLastSeen($user->id);

        $firebaseToken = $this->request->getPost('firebase_token');
        if ($firebaseToken) {
            $userModel->updateFirebaseToken($user->id, $firebaseToken);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Login successful',
            'data'    => [
                'user_id'   => $user->id,
                'token'     => $token,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'user_code' => $user->user_code,
                'plan'      => $user->plan ?? 'free',
            ],
        ]);
    }

    public function updateFirebaseToken()
    {
        $userId = $this->request->getPost('user_id');
        $token  = $this->request->getPost('firebase_token');

        if (!$userId || !$token) {
            return $this->failValidationErrors('user_id and firebase_token required');
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

        $secret = getenv('JWT_SECRET') ?: 'lifeknob_secret_key_change_in_production';
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$payload", $secret);

        return "$header.$payload.$signature";
    }
}
