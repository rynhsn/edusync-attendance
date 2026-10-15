<?php

namespace App\Controllers;

use App\Libraries\RoleAccess;
use App\Models\AuthUserModel;

class UsersController extends BaseController
{
    public function index()
    {
        $users = (new AuthUserModel())
            ->select('user_id, display_name, username, email, role, is_verified, created_at')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('panel/users', [
            'title'      => 'Data User',
            'activePage' => 'users',
            'users'      => $users,
            'roles'      => RoleAccess::ROLES,
            'message'    => session()->getFlashdata('userMessage'),
            'error'      => session()->getFlashdata('userError'),
        ]);
    }

    public function setRole(int $id)
    {
        $role = $this->request->getPost('role');
        $redirect = redirect()->to(site_url('users'));
        if ($id < 1 || ! is_string($role) || ! array_key_exists($role, RoleAccess::ROLES)) {
            return $redirect->with('userError', 'Role yang dipilih tidak valid.');
        }

        $currentUser = session()->get('authUser');
        if ((int) ($currentUser['user_id'] ?? 0) === $id) {
            return $redirect->with('userError', 'Role akun yang sedang digunakan tidak dapat diubah dari halaman ini.');
        }

        $users = new AuthUserModel();
        $target = $users->find($id);
        if ($target === null) {
            return $redirect->with('userError', 'Akun yang akan diubah tidak ditemukan.');
        }
        if ($target['role'] === $role) {
            return $redirect->with('userMessage', 'Role akun tersebut sudah sesuai.');
        }

        if ($target['role'] === 'super_admin'
            && $users->where('role', 'super_admin')->countAllResults() <= 1) {
            return $redirect->with('userError', 'Role Super Admin terakhir tidak dapat diubah.');
        }

        $configuredAdminEmail = strtolower(trim((string) env('auth.super_admin_email')));
        if ($target['role'] === 'super_admin'
            && strcasecmp((string) $target['email'], $configuredAdminEmail) === 0) {
            return $redirect->with('userError', 'Akun Super Admin utama tidak dapat diturunkan rolenya.');
        }

        if (! $users->update($id, ['role' => $role])) {
            log_message('error', 'Failed to change EduSync user role for user {id}.', ['id' => $id]);
            return $redirect->with('userError', 'Role gagal diperbarui. Silakan coba kembali.');
        }

        return $redirect->with('userMessage', 'Role ' . RoleAccess::label($role) . ' berhasil diterapkan.');
    }

    public function passwords()
    {
        $currentUserId = (int) (session()->get('authUser')['user_id'] ?? 0);
        $users = (new AuthUserModel())
            ->select('user_id, display_name, username, email, role, is_verified')
            ->where('role !=', 'super_admin')
            ->where('user_id !=', $currentUserId)
            ->orderBy('display_name', 'ASC')
            ->findAll();

        return view('panel/user-passwords', [
            'title'      => 'Reset Password User',
            'activePage' => 'user-passwords',
            'users'      => $users,
            'message'    => session()->getFlashdata('passwordResetMessage'),
            'error'      => session()->getFlashdata('passwordResetError'),
        ]);
    }

    public function resetPassword(int $id)
    {
        $redirect = redirect()->to(site_url('user-passwords'));
        $password = $this->request->getPost('password');
        $confirmation = $this->request->getPost('password_confirm');

        if (! is_string($password) || strlen($password) < 8 || strlen($password) > 72) {
            return $redirect->with('passwordResetError', 'Password baru harus terdiri dari 8–72 karakter.');
        }
        if (! is_string($confirmation) || ! hash_equals($password, $confirmation)) {
            return $redirect->with('passwordResetError', 'Konfirmasi password baru tidak cocok.');
        }
        if ((int) (session()->get('authUser')['user_id'] ?? 0) === $id) {
            return $redirect->with('passwordResetError', 'Password akun yang sedang digunakan harus diubah melalui halaman Profil.');
        }

        $users = new AuthUserModel();
        $target = $users->find($id);
        if ($target === null || $target['role'] === 'super_admin') {
            return $redirect->with('passwordResetError', 'Akun user tidak ditemukan atau tidak dapat direset dari halaman ini.');
        }

        if (! $users->update($id, ['password_hash' => password_hash($password, PASSWORD_DEFAULT)])) {
            log_message('error', 'Failed to reset password for user {id}.', ['id' => $id]);
            return $redirect->with('passwordResetError', 'Password gagal direset. Silakan coba kembali.');
        }

        return $redirect->with('passwordResetMessage', 'Password akun ' . ($target['display_name'] ?: $target['username'] ?: $target['email']) . ' berhasil direset.');
    }
}
