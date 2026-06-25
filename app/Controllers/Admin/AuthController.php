<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/login');
    }

    public function attemptLogin()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if (!$user || $user->role !== 'admin' || !password_verify($password, $user->password)) {
            return redirect()->to('/admin/login')->with('error', 'Invalid credentials or insufficient permissions.');
        }

        session()->set([
            'admin_id'    => $user->id,
            'admin_name'  => $user->name,
            'admin_email' => $user->email,
            'is_admin'    => true,
        ]);

        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
