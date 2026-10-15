<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Profile extends BaseController
{
    /**
     * Display the current user's profile page.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Profil Saya';
        $userModel = new UserModel();
        $this->data['item'] = $userModel->find(session()->get('userId'));

        return view('admin/profile/index', $this->data);
    }

    /**
     * Update the current user's profile data.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update()
    {
        $userId = session()->get('userId');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/profile')->with('error', 'User tidak ditemukan.');
        }

        $rules = [];

        $username = $this->request->getPost('username');
        if ($username && $username !== $user['username']) {
            $rules['username'] = 'alpha_numeric|is_unique[users.username,id,' . $userId . ']';
        }

        $fullName = $this->request->getPost('full_name');

        $email = $this->request->getPost('email');
        if ($email && $email !== $user['email']) {
            $rules['email'] = 'valid_email|is_unique[users.email,id,' . $userId . ']';
        }

        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');

        if ($oldPassword || $newPassword) {
            $rules['old_password'] = 'required';
            $rules['new_password'] = 'min_length[6]';
            $rules['confirm_password'] = 'matches[new_password]';
        }

        if (!empty($rules)) {
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        $data = [];

        if ($username && $username !== $user['username']) {
            $data['username'] = $username;
        }

        if ($fullName && $fullName !== $user['full_name']) {
            $data['full_name'] = $fullName;
        }

        if ($email && $email !== $user['email']) {
            $data['email'] = $email;
        }

        if ($oldPassword || $newPassword) {
            if (!password_verify($oldPassword, $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Password lama tidak sesuai.');
            }
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (empty($data)) {
            return redirect()->back()->with('error', 'Tidak ada perubahan yang dilakukan.');
        }

        $userModel->update($userId, $data);

        if (isset($data['username'])) {
            session()->set('username', $data['username']);
        }
        if (isset($data['full_name'])) {
            session()->set('fullName', $data['full_name']);
        }

        return redirect()->to('/admin/profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
