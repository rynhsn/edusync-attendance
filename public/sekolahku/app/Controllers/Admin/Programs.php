<?php

namespace App\Controllers\Admin;

use App\Models\ProgramModel;

class Programs extends BaseController
{
    protected $programModel;

    /**
     * Initialize the controller and load the ProgramModel.
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
        $this->programModel = new ProgramModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Program Unggulan';
        $this->data['items'] = $this->programModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->programModel->pager;

        return view('admin/programs/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Program';
        $this->data['item']  = [];
        return view('admin/programs/form', $this->data);
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

        if (!$this->programModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan program.');
        }

        return redirect()->to('/admin/programs')->with('success', 'Program berhasil ditambahkan.');
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
        $this->data['title'] = 'Edit Program';
        $this->data['item']  = $this->programModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/programs')->with('error', 'Program tidak ditemukan.');
        }

        return view('admin/programs/form', $this->data);
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

        if (!$this->programModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui program.');
        }

        return redirect()->to('/admin/programs')->with('success', 'Program berhasil diperbarui.');
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

        $this->programModel->reorder($ids);

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
        if (!$this->programModel->delete($id)) {
            return redirect()->to('/admin/programs')->with('error', 'Gagal menghapus program.');
        }

        return redirect()->to('/admin/programs')->with('success', 'Program berhasil dihapus.');
    }
}
