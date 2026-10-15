<?php

namespace App\Controllers\Admin;

use App\Models\FaqModel;

class Faq extends BaseController
{
    protected $faqModel;

    /**
     * Initialize the controller and load the FaqModel.
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
        $this->faqModel = new FaqModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'FAQ';
        $this->data['items'] = $this->faqModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->faqModel->pager;

        return view('admin/faq/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah FAQ';
        $this->data['item']  = [];
        return view('admin/faq/form', $this->data);
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

        if (!$this->faqModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan FAQ.');
        }

        return redirect()->to('/admin/faq')->with('success', 'FAQ berhasil ditambahkan.');
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
        $this->data['title'] = 'Edit FAQ';
        $this->data['item']  = $this->faqModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/faq')->with('error', 'FAQ tidak ditemukan.');
        }

        return view('admin/faq/form', $this->data);
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

        if (!$this->faqModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui FAQ.');
        }

        return redirect()->to('/admin/faq')->with('success', 'FAQ berhasil diperbarui.');
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

        $this->faqModel->reorder($ids);

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
        if (!$this->faqModel->delete($id)) {
            return redirect()->to('/admin/faq')->with('error', 'Gagal menghapus FAQ.');
        }

        return redirect()->to('/admin/faq')->with('success', 'FAQ berhasil dihapus.');
    }
}
