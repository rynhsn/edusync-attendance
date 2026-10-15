<?php

namespace App\Controllers\Admin;

use App\Models\MenuItemModel;
use App\Models\PageModel;

class Menus extends BaseController
{
    protected $menuModel;

    /**
     * Initialize the controller and load the MenuItemModel.
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
        $this->menuModel = new MenuItemModel();
    }

    /**
     * Display the navigation menu management page.
     *
     * @return string
     */
    public function index()
    {
        helper('menu');

        $pages = (new PageModel())->findAll();
        $pageTitles = [];
        foreach ($pages as $page) {
            $pageTitles[$page['id']] = $page['title'];
        }

        $this->data['title']     = 'Menu Navigasi';
        $this->data['allItems']  = $this->menuModel->getFlatTree();
        $this->data['pageTitles'] = $pageTitles;

        return view('admin/menus/index', $this->data);
    }

    /**
     * Show the form for creating a new menu item.
     *
     * @return string
     */
    public function new()
    {
        $this->data['title']    = 'Tambah Menu';
        $this->data['item']     = [];
        $this->data['parents']  = $this->menuModel->getRootItems();

        return view('admin/menus/form', $this->data);
    }

    /**
     * Store a newly created menu item in storage.
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function create()
    {
        $data = $this->request->getPost();
        $data['parent_id']   = $data['parent_id'] ?: null;
        $data['target']      = $data['target'] ?? '_self';
        $data['status']      = $data['status'] ?? 0;
        $data['section_key'] = $data['section_key'] ?: null;
        $data['sort_order']  = (int) ($data['sort_order'] ?? 0);
        $data['type']        = 'custom';

        if (!$this->menuModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan menu.');
        }

        return redirect()->to('/admin/menus')->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified menu item.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function edit($id)
    {
        $this->data['title']   = 'Edit Menu';
        $this->data['item']    = $this->menuModel->find($id);

        if (!$this->data['item']) {
            return redirect()->to('/admin/menus')->with('error', 'Menu tidak ditemukan.');
        }

        $excludeIds   = $this->menuModel->getDescendantIds($id);
        $excludeIds[] = $id;

        $this->data['parents'] = $this->menuModel->getAvailableParents($excludeIds);
        $this->data['locked']  = $this->data['item']['type'] !== 'custom';

        return view('admin/menus/form', $this->data);
    }

    /**
     * Update the specified menu item in storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function update($id)
    {
        $item = $this->menuModel->find($id);

        if (!$item) {
            return redirect()->to('/admin/menus')->with('error', 'Menu tidak ditemukan.');
        }

        $data = $this->request->getPost();

        if ($item['type'] !== 'custom') {
            $data = [
                'parent_id'  => $data['parent_id'] ?: null,
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ];
        } else {
            $data['parent_id']   = $data['parent_id'] ?: null;
            $data['target']      = $data['target'] ?? '_self';
            $data['status']      = $data['status'] ?? 0;
            $data['section_key'] = $data['section_key'] ?: null;
            $data['sort_order']  = (int) ($data['sort_order'] ?? 0);
        }

        if (!$this->menuModel->update($id, $data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui menu.');
        }

        return redirect()->to('/admin/menus')->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Delete the specified menu item from storage.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        $item = $this->menuModel->find($id);

        if (!$item) {
            return redirect()->to('/admin/menus')->with('error', 'Menu tidak ditemukan.');
        }

        if ($item['type'] !== 'custom') {
            return redirect()->to('/admin/menus')->with('error', 'Menu bawaan tidak dapat dihapus.');
        }

        if ($this->menuModel->countChildren($id) > 0) {
            return redirect()->to('/admin/menus')->with('error', 'Hapus menu anak terlebih dahulu.');
        }

        if (!$this->menuModel->delete($id)) {
            return redirect()->to('/admin/menus')->with('error', 'Gagal menghapus menu.');
        }

        return redirect()->to('/admin/menus')->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Reorder menu items via drag-and-drop.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function reorder()
    {
        $input = json_decode($this->request->getBody(), true);
        $items = $input['items'] ?? [];

        if (empty($items)) {
            return $this->response->setJSON(['success' => false]);
        }

        $success = $this->menuModel->reorderItems($items);

        if (!$success) {
            return $this->response->setJSON(['success' => false]);
        }

        return $this->response->setJSON(['success' => true, 'csrfHash' => csrf_hash()]);
    }
}
