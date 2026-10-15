<?php

namespace App\Controllers;

use App\Models\SchoolWebsiteMessageModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;

class SchoolWebsiteMessagesController extends BaseController
{
    public function index()
    {
        $messages = new SchoolWebsiteMessageModel();

        return view('panel/website-messages', [
            'title' => 'Pesan Website',
            'activePage' => 'website-messages',
            'messages' => $messages->getLatest(),
            'pager' => $messages->pager,
            'unreadCount' => (new SchoolWebsiteMessageModel())->getUnreadCount(),
            'message' => session()->getFlashdata('websiteMessagesMessage'),
            'error' => session()->getFlashdata('websiteMessagesError'),
        ]);
    }

    public function markRead(int $id): RedirectResponse
    {
        $messages = new SchoolWebsiteMessageModel();
        if ($messages->find($id) === null) {
            return redirect()->to(site_url('website-messages'))
                ->with('websiteMessagesError', 'Pesan tidak ditemukan.');
        }

        try {
            if (! $messages->update($id, ['is_read' => 1])) {
                throw new DatabaseException('Updating the school website message read status returned false.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Unable to mark a school website message as read: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('website-messages'))
                ->with('websiteMessagesError', 'Status pesan gagal diperbarui. Silakan coba kembali.');
        }

        return redirect()->to(site_url('website-messages'))
            ->with('websiteMessagesMessage', 'Pesan ditandai sudah dibaca.');
    }
}
