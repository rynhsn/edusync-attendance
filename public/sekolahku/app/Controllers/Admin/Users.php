<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    /**
     * Initialize controller, load the user model, and restrict to superadmin only.
     *
     * @param \CodeIgniter\HTTP\RequestInterface  $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface            $logger
     *
     * @return void
     */
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        $this->userModel = new UserModel();

        if (($this->data['user']['role'] ?? '') !== 'superadmin') {
            redirect()->to('/admin/dashboard')->with('error', 'Akses ditolak. Hanya superadmin.')->send();
            exit;
        }
    }

    /**
     * Display paginated list of non-superadmin users.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Pengguna';
        $this->data['items'] = $this->userModel->where('role !=', 'superadmin')->paginate(10);
        $this->data['pager'] = $this->userModel->pager;

        return view('admin/users/index', $this->data);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Pengguna';
        $this->data['item']  = [];
        return view('admin/users/form', $this->data);
    }

    /**
     * Process user creation.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['is_active'] = $data['is_active'] ?? 0;
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if (($data['role'] ?? '') === 'superadmin') {
            return redirect()->back()->withInput()->with('error', 'Tidak dapat membuat akun Superadmin.');
        }

        if (!$this->userModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan pengguna.');
        }

        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Show the form for editing a user.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        $this->data['title'] = 'Edit Pengguna';
        $this->data['item'] = $this->userModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        if (($this->data['item']['role'] ?? '') === 'superadmin') {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat mengedit akun Superadmin.');
        }

        return view('admin/users/form', $this->data);
    }

    /**
     * Process user update.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        if (($user['role'] ?? '') === 'superadmin') {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat mengedit akun Superadmin.');
        }

        $data = $this->request->getPost();
        $data['is_active'] = $data['is_active'] ?? 0;

        if (($data['role'] ?? '') === 'superadmin') {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat mengedit akun Superadmin.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (!$this->userModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pengguna.');
        }

        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Delete a user by its ID.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        if ($id == session()->get('userId')) {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user = $this->userModel->find($id);

        if ($user && ($user['role'] ?? '') === 'superadmin') {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menghapus akun Superadmin.');
        }

        if (!$this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('error', 'Gagal menghapus pengguna.');
        }

        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
    }
}
