<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->has('userId')) {
            return redirect()->to('/');
        }

        return view('auth/login');
    }

    public function attempt()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = model('UserModel')
            ->select('users.*, roles.nama as role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('email', $email)
            ->where('is_active', 1)
            ->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Email atau password salah.');
        }

        // Update last login
        model('UserModel')->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        $sessionData = [
            'userId'         => $user['id'],
            'userEmail'      => $user['email'],
            'userName'       => $user['nama_lengkap'],
            'userRole'       => $user['role_name'],
            'studyProgramId' => $user['study_program_id'],
            'lecturerId'     => $user['lecturer_id'],
        ];

        session()->set($sessionData);

        $beforeLoginUrl = session()->get('beforeLoginUrl');
        if ($beforeLoginUrl) {
            session()->remove('beforeLoginUrl');
            return redirect()->to($beforeLoginUrl);
        }

        return redirect()->to('/');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}