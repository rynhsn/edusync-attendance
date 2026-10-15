<?php

namespace App\Controllers\Admin;

use App\Models\PageModel;
use App\Models\MenuItemModel;

class Pages extends BaseController
{
    protected $pageModel;

    /**
     * Initialize the controller and load the PageModel.
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
        $this->pageModel = new PageModel();
    }

    /**
     * Display paginated list of static pages.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Halaman Statis';
        $this->data['items'] = $this->pageModel->orderBy('created_at', 'DESC')->paginate(10);
        $this->data['pager'] = $this->pageModel->pager;

        return view('admin/pages/index', $this->data);
    }

    /**
     * Show the form for creating a new static page.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title'] = 'Tambah Halaman';
        $this->data['item']  = [];

        return view('admin/pages/form', $this->data);
    }

    /**
     * Store a newly created static page in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['slug']   = url_title($data['title'] ?? '', '-', true);
        $data['status'] = $data['status'] ?? 'draft';

        if (!$this->pageModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan halaman.');
        }

        $pageId = $this->pageModel->getInsertID();

        if (!empty($data['add_to_menu'])) {
            $menuModel = new MenuItemModel();
            $menuModel->insert([
                'title'      => $data['title'],
                'url'        => $data['slug'],
                'target'     => '_self',
                'sort_order' => 0,
                'status'     => $data['status'] === 'published' ? 1 : 0,
                'type'       => 'page',
                'page_id'    => $pageId,
            ]);
        }

        return redirect()->to('/admin/pages')->with('success', 'Halaman berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified static page.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        $this->data['title'] = 'Edit Halaman';
        $this->data['item']  = $this->pageModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/pages')->with('error', 'Halaman tidak ditemukan.');
        }

        $menuModel = new MenuItemModel();
        $this->data['menuItem'] = $menuModel->getByPageId($id);

        return view('admin/pages/form', $this->data);
    }

    /**
     * Update the specified static page in storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $data = $this->request->getPost();
        $data['status'] = $data['status'] ?? 'draft';

        if ($data['title'] ?? null) {
            $data['slug'] = url_title($data['title'], '-', true);
        }

        if (!$this->pageModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui halaman.');
        }

        $menuModel = new MenuItemModel();
        $menuItem  = $menuModel->getByPageId($id);

        if (!empty($data['add_to_menu'])) {
            if ($menuItem) {
                $menuModel->update($menuItem['id'], [
                    'title'  => $data['title'],
                    'url'    => $data['slug'],
                    'status' => $data['status'] === 'published' ? 1 : 0,
                ]);
            } else {
                $menuModel->insert([
                    'title'      => $data['title'],
                    'url'        => $data['slug'],
                    'target'     => '_self',
                    'sort_order' => 0,
                    'status'     => $data['status'] === 'published' ? 1 : 0,
                    'type'       => 'page',
                    'page_id'    => $id,
                ]);
            }
        } elseif ($menuItem) {
            $menuModel->update($menuItem['id'], [
                'type'    => 'custom',
                'page_id' => null,
            ]);
        }

        return redirect()->to('/admin/pages')->with('success', 'Halaman berhasil diperbarui.');
    }

    /**
     * Delete the specified static page and reset its menu link.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        $menuModel = new MenuItemModel();
        $menuItem  = $menuModel->getByPageId($id);

        if ($menuItem) {
            $menuModel->update($menuItem['id'], [
                'type'    => 'custom',
                'page_id' => null,
            ]);
        }

        if (!$this->pageModel->delete($id)) {
            return redirect()->to('/admin/pages')->with('error', 'Gagal menghapus halaman.');
        }

        return redirect()->to('/admin/pages')->with('success', 'Halaman berhasil dihapus.');
    }
}
