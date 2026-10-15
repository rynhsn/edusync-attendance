<?php

namespace App\Controllers\Admin;

use App\Models\ExtracurricularModel;

class Extracurriculars extends BaseController
{
    protected $extracurricularModel;

    /**
     * Initialize the controller.
     *
     * @param \CodeIgniter\HTTP\RequestInterface $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        $this->extracurricularModel = new ExtracurricularModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Ekstrakurikuler';
        $this->data['items'] = $this->extracurricularModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->extracurricularModel->pager;

        return view('admin/extracurriculars/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Ekstrakurikuler';
        $this->data['item']  = [];
        return view('admin/extracurriculars/form', $this->data);
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

        if (!$this->extracurricularModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan ekstrakurikuler.');
        }

        return redirect()->to('/admin/extracurriculars')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
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
        $this->data['title'] = 'Edit Ekstrakurikuler';
        $this->data['item']  = $this->extracurricularModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/extracurriculars')->with('error', 'Ekstrakurikuler tidak ditemukan.');
        }

        return view('admin/extracurriculars/form', $this->data);
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

        if (!$this->extracurricularModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui ekstrakurikuler.');
        }

        return redirect()->to('/admin/extracurriculars')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    /**
     * Reorder extracurriculars via AJAX.
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

        $this->extracurricularModel->reorder($ids);

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
        if (!$this->extracurricularModel->delete($id)) {
            return redirect()->to('/admin/extracurriculars')->with('error', 'Gagal menghapus ekstrakurikuler.');
        }

        return redirect()->to('/admin/extracurriculars')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}
