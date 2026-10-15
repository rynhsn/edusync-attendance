<?php

namespace App\Controllers;

use App\Models\AuthUserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    private AuthUserModel $users;

    public function __construct()
    {
        $this->users = new AuthUserModel();
    }

    public function login()
    {
        if (session()->has('authUser')) {
            return redirect()->to($this->destination());
        }

        return view('auth/login', [
            'title'   => 'Login Admin',
            'message' => session()->getFlashdata('authMessage'),
            'error'   => session()->getFlashdata('authError'),
        ]);
    }

    public function register()
    {
        if (session()->has('authUser')) {
            return redirect()->to($this->destination());
        }

        return view('auth/register', [
            'title'   => 'Register',
            'message' => session()->getFlashdata('authMessage'),
            'error'   => session()->getFlashdata('authError'),
        ]);
    }

    public function verify()
    {
        $email = (string) session()->get('otpEmail');
        if ($email === '') {
            return redirect()->to(site_url('register'));
        }

        return view('auth/verify', [
            'title'   => 'Verifikasi Email',
            'email'   => $email,
            'message' => session()->getFlashdata('authMessage'),
            'error'   => session()->getFlashdata('authError'),
        ]);
    }

    public function accessDenied()
    {
        return view('auth/access-denied', ['title' => 'Akses Ditolak']);
    }

    public function registerAccount()
    {
        $email       = strtolower(trim((string) $this->request->getPost('email')));
        $displayName = trim((string) $this->request->getPost('display_name'));
        $username    = strtolower(trim((string) $this->request->getPost('username')));
        $password    = (string) $this->request->getPost('password');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($email) > 254) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Masukkan alamat email yang valid.');
        }

        if ($displayName === '' || mb_strlen($displayName) > 100) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Nama wajib diisi dan maksimal 100 karakter.');
        }

        if (preg_match('/^[a-z0-9._-]{3,32}$/', $username) !== 1) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Username harus 3–32 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.');
        }

        if (strlen($password) < 8 || strlen($password) > 72) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Password harus terdiri dari 8–72 karakter.');
        }

        $user = $this->users->where('email', $email)->first();
        if ($user !== null && (bool) $user['is_verified']) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Email sudah terverifikasi. Silakan login.');
        }

        $usernameOwner = $this->users->where('username', $username)->first();
        if ($usernameOwner !== null && (int) $usernameOwner['user_id'] !== (int) ($user['user_id'] ?? 0)) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Username sudah digunakan. Silakan pilih username lain.');
        }

        $superAdminEmail = strtolower(trim((string) env('auth.super_admin_email')));
        $account = [
            'email'        => $email,
            'display_name' => $displayName,
            'username'     => $username,
            'password_hash'=> password_hash($password, PASSWORD_DEFAULT),
            'role'         => hash_equals($superAdminEmail, $email) ? 'super_admin' : 'user',
            'is_verified'  => 0,
        ];

        if ($user === null) {
            $userId = $this->users->insert($account, true);
            if ($userId === false) {
                log_message('error', 'Failed to create an EduSync account.');
                return redirect()->to(site_url('register'))
                    ->with('authError', 'Akun gagal dibuat. Silakan coba kembali.');
            }

            $user = $this->users->find($userId);
        } elseif (! $this->users->update($user['user_id'], $account)) {
            log_message('error', 'Failed to update an unverified EduSync account.');
            return redirect()->to(site_url('register'))
                ->with('authError', 'Akun gagal diperbarui. Silakan coba kembali.');
        } else {
            $user = $this->users->find($user['user_id']);
        }

        if ($user === null) {
            log_message('error', 'EduSync account could not be loaded after registration.');
            return redirect()->to(site_url('register'))
                ->with('authError', 'Akun gagal diproses. Silakan coba kembali.');
        }

        return $this->issueVerificationCode($user, 'register');
    }

    public function resendVerificationCode()
    {
        $email = (string) session()->get('otpEmail');
        $user  = $email !== '' ? $this->users->where('email', $email)->first() : null;

        if ($user === null || (bool) $user['is_verified']) {
            session()->remove(['otpEmail']);
            return redirect()->to(site_url('register'))
                ->with('authError', 'Sesi verifikasi tidak valid. Silakan daftar kembali.');
        }

        return $this->issueVerificationCode($user, 'register');
    }

    public function verifyCode()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $code  = trim((string) $this->request->getPost('code'));

        if (! hash_equals((string) session()->get('otpEmail'), $email)) {
            return redirect()->to(site_url('register'))
                ->with('authError', 'Sesi verifikasi tidak valid. Silakan daftar kembali.');
        }

        $user = $this->users->where('email', $email)->first();
        if ($user === null || (bool) $user['is_verified'] || empty($user['otp_hash']) || empty($user['otp_expires_at'])) {
            return redirect()->to(site_url('auth/verify'))
                ->with('authError', 'Kode tidak valid. Minta kode baru.');
        }

        if ((int) $user['otp_attempts'] >= 5) {
            if (! $this->clearOtp($user['user_id'])) {
                return redirect()->to(site_url('register'))
                    ->with('authError', 'Kode tidak dapat digunakan lagi. Silakan daftar kembali.');
            }

            session()->remove(['otpEmail']);
            return redirect()->to(site_url('register'))
                ->with('authError', 'Batas percobaan tercapai. Silakan daftar kembali.');
        }

        if (strtotime($user['otp_expires_at']) < time()) {
            if (! $this->clearOtp($user['user_id'])) {
                return redirect()->to(site_url('register'))
                    ->with('authError', 'Kode kedaluwarsa dan gagal dibersihkan. Silakan daftar kembali.');
            }

            session()->remove(['otpEmail']);
            return redirect()->to(site_url('register'))
                ->with('authError', 'Kode sudah kedaluwarsa. Silakan daftar kembali untuk meminta kode baru.');
        }

        if (preg_match('/^\d{6}$/', $code) !== 1 || ! password_verify($code, $user['otp_hash'])) {
            $attempts = (int) $user['otp_attempts'] + 1;
            if (! $this->users->update($user['user_id'], ['otp_attempts' => $attempts])) {
                log_message('error', 'Failed to record an EduSync OTP verification attempt.');
                return redirect()->to(site_url('auth/verify'))
                    ->with('authError', 'Percobaan verifikasi gagal diproses. Silakan coba kembali.');
            }

            return redirect()->to(site_url('auth/verify'))
                ->with('authError', 'Kode verifikasi salah.');
        }

        if (! $this->users->update($user['user_id'], [
            'is_verified'    => 1,
            'otp_hash'       => null,
            'otp_expires_at' => null,
            'otp_sent_at'    => null,
            'otp_attempts'   => 0,
        ])) {
            log_message('error', 'Failed to consume an EduSync OTP after successful verification.');
            return redirect()->to(site_url('auth/verify'))
                ->with('authError', 'Verifikasi gagal disimpan. Silakan coba kembali.');
        }

        session()->remove(['otpEmail']);

        return redirect()->to(site_url('login'))
            ->with('authMessage', 'Email berhasil diverifikasi. Silakan login dengan username dan password.');
    }

    public function authenticate()
    {
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $password = (string) $this->request->getPost('password');

        $user = $this->users->where('username', $username)->first();
        if ($user === null || empty($user['password_hash']) || ! password_verify($password, $user['password_hash'])) {
            return redirect()->to(site_url('login'))
                ->with('authError', 'Username atau password salah.');
        }

        if (! (bool) $user['is_verified']) {
            return redirect()->to(site_url('login'))
                ->with('authError', 'Email belum diverifikasi. Silakan selesaikan pendaftaran terlebih dahulu.');
        }

        session()->regenerate(true);
        session()->set('authUser', [
            'user_id'      => (int) $user['user_id'],
            'email'        => $user['email'],
            'display_name' => $user['display_name'],
            'avatar_path'  => $user['avatar_path'] ?? null,
            'role'         => $user['role'],
        ]);

        return redirect()->to($this->destination());
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(site_url('login'));
    }

    private function issueVerificationCode(array $user, string $purpose)
    {
        if (! empty($user['otp_sent_at'])) {
            $lastSent = strtotime($user['otp_sent_at']);
            if ($lastSent !== false && time() - $lastSent < 60) {
                session()->set('otpEmail', $user['email']);
                return redirect()->to(site_url('auth/verify'))
                    ->with('authError', 'Tunggu satu menit sebelum meminta kode baru.');
            }
        }

        $otp       = (string) random_int(100000, 999999);
        $sentAt    = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', time() + 600);

        if (! $this->users->update($user['user_id'], [
            'otp_hash'       => password_hash($otp, PASSWORD_DEFAULT),
            'otp_expires_at' => $expiresAt,
            'otp_sent_at'    => $sentAt,
            'otp_attempts'   => 0,
        ])) {
            log_message('error', 'Failed to save an EduSync email verification code.');
            return redirect()->to(site_url('register'))
                ->with('authError', 'Kode verifikasi gagal dibuat. Silakan coba kembali.');
        }

        $mailError = $this->sendCode($user['email'], $otp);
        if ($mailError !== null) {
            $this->clearOtp($user['user_id']);
            $message = $mailError === 'configuration'
                ? 'Konfigurasi email belum valid. Gunakan Google App Password 16 karakter tanpa spasi, dan samakan email pengirim dengan akun SMTP di .env.'
                : 'Gmail menolak pengiriman OTP. Periksa App Password, verifikasi 2 langkah, dan alamat email tujuan.';

            return redirect()->to(site_url('register'))
                ->with('authError', $message);
        }

        session()->set('otpEmail', $user['email']);

        return redirect()->to(site_url('auth/verify'))
            ->with('authMessage', 'Kode verifikasi dikirim. Kode berlaku selama 10 menit.');
    }

    private function sendCode(string $recipient, string $code): ?string
    {
        $config = config('Email');
        $smtpPassword = preg_replace('/\s+/', '', $config->SMTPPass);

        if ($smtpPassword === null
            || $config->protocol !== 'smtp'
            || $config->SMTPHost === ''
            || $config->SMTPUser === ''
            || strlen($smtpPassword) !== 16
            || strcasecmp(trim($config->fromEmail), trim($config->SMTPUser)) !== 0) {
            log_message('error', 'OTP email not sent because Gmail SMTP configuration is invalid.');
            return 'configuration';
        }

        $config->SMTPPass = $smtpPassword;
        $email = service('email');
        $email->setTo($recipient);
        $email->setSubject('Kode verifikasi registrasi EduSync');
        $email->setMessage(
            "Kode verifikasi email EduSync Anda: {$code}\n\n"
            . "Kode ini berlaku selama 10 menit dan hanya dapat digunakan sekali.\n"
            . "Jangan bagikan kode ini kepada siapa pun."
        );

        if (! $email->send()) {
            log_message('error', 'Failed to send an EduSync email verification code.');
            $email->clear(true);
            return 'delivery';
        }

        $email->clear(true);
        return null;
    }

    private function clearOtp(int|string $userId): bool
    {
        $cleared = $this->users->update($userId, [
            'otp_hash'       => null,
            'otp_expires_at' => null,
            'otp_sent_at'    => null,
            'otp_attempts'   => 0,
        ]);

        if (! $cleared) {
            log_message('error', 'Failed to clear an EduSync OTP.');
        }

        return $cleared;
    }

    private function destination(): string
    {
        $user = session()->get('authUser');

        return is_array($user) ? site_url('panel') : site_url('login');
    }
}
