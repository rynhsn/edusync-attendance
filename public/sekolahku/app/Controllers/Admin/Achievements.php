<?php

namespace App\Controllers\Admin;

use App\Models\AchievementModel;

class Achievements extends BaseController
{
    protected $achievementModel;

    /**
     * Initialize the controller and load the AchievementModel.
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
        $this->achievementModel = new AchievementModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Prestasi';
        $this->data['items'] = $this->achievementModel->orderBy('year', 'DESC')->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->achievementModel->pager;

        return view('admin/achievements/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Prestasi';
        $this->data['item']  = [];
        return view('admin/achievements/form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['show'] = $data['show'] ?? 0;

        if (!$this->achievementModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan prestasi.');
        }

        return redirect()->to('/admin/achievements')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        $this->data['title'] = 'Edit Prestasi';
        $this->data['item']  = $this->achievementModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/achievements')->with('error', 'Prestasi tidak ditemukan.');
        }

        return view('admin/achievements/form', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $data = $this->request->getPost();
        $data['show'] = $data['show'] ?? 0;

        if (!$this->achievementModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui prestasi.');
        }

        return redirect()->to('/admin/achievements')->with('success', 'Prestasi berhasil diperbarui.');
    }

    /**
     * Reorder resources via drag-and-drop.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function reorder()
    {
        $input = json_decode($this->request->getBody(), true);
        $ids = $input['ids'] ?? [];

        if (empty($ids)) {
            return $this->response->setJSON(['success' => false]);
        }

        $this->achievementModel->reorder($ids);

        return $this->response->setJSON(['success' => true, 'csrfHash' => csrf_hash()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        if (!$this->achievementModel->delete($id)) {
            return redirect()->to('/admin/achievements')->with('error', 'Gagal menghapus prestasi.');
        }

        return redirect()->to('/admin/achievements')->with('success', 'Prestasi berhasil dihapus.');
    }
}
