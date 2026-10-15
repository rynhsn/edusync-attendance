<?php

namespace App\Controllers\Admin;

use App\Models\ContactMessageModel;

class Messages extends BaseController
{
    protected $messageModel;

    /**
     * Initialize the controller and load the ContactMessageModel.
     *
     * @param \CodeIgniter\HTTP\RequestInterface  $request
     * @param \CodeIgniter\HTTP\ResponseInterface $response
     * @param \Psr\Log\LoggerInterface            $logger
     */
    public function initController($request, $response, $logger)
    {
        parent::initController($request, $response, $logger);
        $this->messageModel = new ContactMessageModel();
    }

    /**
     * Display paginated list of contact messages.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Pesan Masuk';
        $this->data['items'] = $this->messageModel->orderBy('created_at', 'DESC')->paginate(10);
        $this->data['pager'] = $this->messageModel->pager;

        return view('admin/messages/index', $this->data);
    }

    /**
     * Show a single message detail and mark it as read.
     *
     * @param int $id
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function show($id)
    {
        $message = $this->messageModel->find($id);

        if (!$message) {
            return redirect()->to('/admin/messages')->with('error', 'Pesan tidak ditemukan.');
        }

        if (!$message['is_read']) {
            $this->messageModel->update($id, ['is_read' => 1]);
        }

        $this->data['title'] = 'Detail Pesan';
        $this->data['item']  = $message;

        return view('admin/messages/show', $this->data);
    }

    /**
     * Delete a contact message by ID.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        if (!$this->messageModel->delete($id)) {
            return redirect()->to('/admin/messages')->with('error', 'Gagal menghapus pesan.');
        }

        return redirect()->to('/admin/messages')->with('success', 'Pesan berhasil dihapus.');
    }
}
