<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;
use App\Models\SettingModel;

class Auth extends \App\Controllers\BaseController
{
    /**
     * Display the login form.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }

        $settingModel = new SettingModel();
        $settings = $settingModel->getMany(['site_name', 'site_logo_icon']);

        return view('admin/login', ['settings' => $settings]);
    }

    /**
     * Process login form submission.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function attempt()
    {
        $validation = service('validation');

        $validation->setRules([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->attemptLogin($username, $password);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        session()->regenerate();
        session()->set([
            'isLoggedIn' => true,
            'userId'     => $user['id'],
            'username'   => $user['username'],
            'fullName'   => $user['full_name'],
            'role'       => $user['role'],
        ]);

        session()->setFlashdata('success', 'Selamat datang, ' . $user['full_name'] . '!');

        return redirect()->to('/admin/dashboard');
    }

    /**
     * Destroy the user session and redirect to login.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda berhasil logout.');
    }
}
