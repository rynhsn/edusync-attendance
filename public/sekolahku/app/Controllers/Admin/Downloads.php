<?php

namespace App\Controllers\Admin;

use App\Models\DownloadModel;

class Downloads extends BaseController
{
    protected $downloadModel;

    /**
     * Initialize the controller and load the DownloadModel.
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
        $this->downloadModel = new DownloadModel();
    }

    /**
     * Display paginated list of downloadable files.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Download Center';
        $this->data['items'] = $this->downloadModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->downloadModel->pager;

        return view('admin/downloads/index', $this->data);
    }

    /**
     * Show the form for creating a new download entry.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Download';
        $this->data['item']  = [];
        return view('admin/downloads/form', $this->data);
    }

    /**
     * Store a newly created download entry in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['show'] = $data['show'] ?? 0;

        if (!$this->downloadModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan download.');
        }

        return redirect()->to('/admin/downloads')->with('success', 'Download berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified download entry.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        $this->data['title'] = 'Edit Download';
        $this->data['item']  = $this->downloadModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/downloads')->with('error', 'Download tidak ditemukan.');
        }

        return view('admin/downloads/form', $this->data);
    }

    /**
     * Update the specified download entry in storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $data = $this->request->getPost();
        $data['show'] = $data['show'] ?? 0;

        if (!$this->downloadModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui download.');
        }

        return redirect()->to('/admin/downloads')->with('success', 'Download berhasil diperbarui.');
    }

    /**
     * Reorder downloads via drag-and-drop.
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

        $this->downloadModel->reorder($ids);

        return $this->response->setJSON(['success' => true, 'csrfHash' => csrf_hash()]);
    }

    /**
     * Delete the specified download entry from storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        if (!$this->downloadModel->delete($id)) {
            return redirect()->to('/admin/downloads')->with('error', 'Gagal menghapus download.');
        }

        return redirect()->to('/admin/downloads')->with('success', 'Download berhasil dihapus.');
    }
}
