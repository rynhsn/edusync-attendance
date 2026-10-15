<?php

namespace App\Controllers\Admin;

use App\Models\NewsCommentModel;

class Comments extends BaseController
{
    protected $commentModel;

    /**
     * Initialize controller, load the comment model.
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
        $this->commentModel = new NewsCommentModel();
    }

    /**
     * Display paginated list of comments.
     *
     * @return string
     */
    public function index()
    {
        $this->data['title'] = 'Komentar';
        $this->data['items'] = $this->commentModel
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $this->data['pager'] = $this->commentModel->pager;

        return view('admin/comments/index', $this->data);
    }

    /**
     * Approve a comment by its ID.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function approve($id)
    {
        if (!$this->commentModel->update($id, ['is_approved' => 1])) {
            return redirect()->to('/admin/comments')->with('error', 'Gagal menyetujui komentar.');
        }

        return redirect()->to('/admin/comments')->with('success', 'Komentar berhasil disetujui.');
    }

    /**
     * Reject (delete) a comment by its ID.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function reject($id)
    {
        if (!$this->commentModel->delete($id)) {
            return redirect()->to('/admin/comments')->with('error', 'Gagal menolak komentar.');
        }

        return redirect()->to('/admin/comments')->with('success', 'Komentar berhasil ditolak dan dihapus.');
    }

    /**
     * Delete a comment by its ID.
     *
     * @param int $id
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id)
    {
        if (!$this->commentModel->delete($id)) {
            return redirect()->to('/admin/comments')->with('error', 'Gagal menghapus komentar.');
        }

        return redirect()->to('/admin/comments')->with('success', 'Komentar berhasil dihapus.');
    }
}
