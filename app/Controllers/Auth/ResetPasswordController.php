<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;

class ResetPasswordController extends BaseController
{
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function sendResetLink()
    {
        $email = $this->request->getPost('email');

        $user = model('UserModel')->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        }

        helper('text');
        $token = random_string('alnum', 64);

        $db = \Config\Database::connect();
        
        $db->table('password_resets')->where('email', $email)->delete();

        $db->table('password_resets')->insert([
            'email'      => $email,
            'token'      => $token,
            'created_at' => Time::now('Asia/Jakarta', 'en_US')->toDateTimeString(),
        ]);

        $resetLink = base_url('reset-password/' . $token);

        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password - SIM LKPS');
        
        $message = "
        <p>Halo,</p>
        <p>Kami menerima permintaan untuk mereset password akun Anda di SIM LKPS.</p>
        <p>Silakan klik link di bawah ini untuk membuat password baru:</p>
        <p><a href='{$resetLink}'>{$resetLink}</a></p>
        <p>Link ini akan kadaluarsa dalam 2 jam.</p>
        <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
        <br>
        <p>Terima kasih,<br>Admin SIM LKPS</p>
        ";

        $emailService->setMessage($message);

        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda. Silakan cek Inbox atau folder Spam.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim email reset password. Pastikan konfigurasi SMTP benar.');
        }
    }

    public function resetPassword($token)
    {
        $db = \Config\Database::connect();
        
        $resetRecord = $db->table('password_resets')->where('token', $token)->get()->getRowArray();

        if (!$resetRecord) {
            return redirect()->to('/forgot-password')->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
        }

        $createdAt = Time::parse($resetRecord['created_at']);
        if ($createdAt->difference(Time::now())->getHours() >= 2) {
            $db->table('password_resets')->where('token', $token)->delete();
            return redirect()->to('/forgot-password')->with('error', 'Token reset password sudah kadaluarsa.');
        }

        return view('auth/reset_password', ['token' => $token]);
    }

    public function updatePassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        if ($password !== $passwordConfirm) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        if (strlen($password) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        $db = \Config\Database::connect();
        $resetRecord = $db->table('password_resets')->where('token', $token)->get()->getRowArray();

        if (!$resetRecord) {
            return redirect()->to('/forgot-password')->with('error', 'Token tidak valid.');
        }

        $userModel = model('UserModel');
        $user = $userModel->where('email', $resetRecord['email'])->first();

        if ($user) {
            $userModel->update($user['id'], [
                'password' => $password
            ]);

            $db->table('password_resets')->where('email', $resetRecord['email'])->delete();

            return redirect()->to('/login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
        }

        return redirect()->to('/forgot-password')->with('error', 'Terjadi kesalahan, silakan coba lagi.');
    }
}
