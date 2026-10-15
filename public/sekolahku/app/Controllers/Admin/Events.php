<?php

namespace App\Controllers\Admin;

use App\Models\EventModel;

class Events extends BaseController
{
    protected $eventModel;

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
        $this->eventModel = new EventModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Acara & Kegiatan';
        $this->data['items'] = $this->eventModel->orderBy('event_date', 'DESC')->paginate(10);
        $this->data['pager'] = $this->eventModel->pager;

        return view('admin/events/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Acara';
        $this->data['item']  = [];
        return view('admin/events/form', $this->data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $slug = url_title($this->request->getPost('title'), '-', true);

        $data = $this->request->getPost();
        $data['slug'] = $slug;
        $data['show'] = $data['show'] ?? 0;

        if (!$this->eventModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan acara.');
        }

        return redirect()->to('/admin/events')->with('success', 'Acara berhasil ditambahkan.');
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
        $this->data['title'] = 'Edit Acara';
        $this->data['item'] = $this->eventModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/events')->with('error', 'Acara tidak ditemukan.');
        }

        return view('admin/events/form', $this->data);
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

        if ($data['title'] ?? null) {
            $data['slug'] = url_title($data['title'], '-', true);
        }

        if (!$this->eventModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui acara.');
        }

        return redirect()->to('/admin/events')->with('success', 'Acara berhasil diperbarui.');
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
        if (!$this->eventModel->delete($id)) {
            return redirect()->to('/admin/events')->with('error', 'Gagal menghapus acara.');
        }

        return redirect()->to('/admin/events')->with('success', 'Acara berhasil dihapus.');
    }
}
