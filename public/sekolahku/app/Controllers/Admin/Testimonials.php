<?php

namespace App\Controllers\Admin;

use App\Models\TestimonialModel;

class Testimonials extends BaseController
{
    protected $testimonialModel;

    /**
     * Initialize the controller and load the TestimonialModel.
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
        $this->testimonialModel = new TestimonialModel();
    }

    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Testimonial';
        $this->data['items'] = $this->testimonialModel->orderBy('sort_order', 'ASC')->paginate(10);
        $this->data['pager'] = $this->testimonialModel->pager;

        return view('admin/testimonials/index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Testimonial';
        $this->data['item']  = [];
        return view('admin/testimonials/form', $this->data);
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

        if (!$this->testimonialModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan testimonial.');
        }

        return redirect()->to('/admin/testimonials')->with('success', 'Testimonial berhasil ditambahkan.');
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
        $this->data['title'] = 'Edit Testimonial';
        $this->data['item']  = $this->testimonialModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/testimonials')->with('error', 'Testimonial tidak ditemukan.');
        }

        return view('admin/testimonials/form', $this->data);
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

        if (!$this->testimonialModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui testimonial.');
        }

        return redirect()->to('/admin/testimonials')->with('success', 'Testimonial berhasil diperbarui.');
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

        $this->testimonialModel->reorder($ids);

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
        if (!$this->testimonialModel->delete($id)) {
            return redirect()->to('/admin/testimonials')->with('error', 'Gagal menghapus testimonial.');
        }

        return redirect()->to('/admin/testimonials')->with('success', 'Testimonial berhasil dihapus.');
    }
}
