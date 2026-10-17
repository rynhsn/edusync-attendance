<?php

namespace App\Controllers;

use App\Models\AdminNotificationModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class NotificationsController extends BaseController
{
    public function index()
    {
        $userId = (int) (session()->get('authUser')['user_id'] ?? 0);
        $notificationModel = new AdminNotificationModel();

        return view('panel/notifications', [
            'title' => 'Notifikasi',
            'activePage' => 'notifications',
            'notifications' => $notificationModel->allForUser($userId),
            'storageAvailable' => $notificationModel->isAvailable(),
        ]);
    }

    public function open(int $id)
    {
        $userId = (int) (session()->get('authUser')['user_id'] ?? 0);
        try {
            $notification = (new AdminNotificationModel())->markRead($id, $userId);
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to mark admin notification {id} as read: {message}', [
                'id' => $id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->to(site_url('notifications'))
                ->with('notificationError', 'Notifikasi tidak dapat dibuka. Silakan coba kembali.');
        }

        if ($notification === null) {
            return redirect()->to(site_url('notifications'));
        }

        return redirect()->to(site_url(ltrim((string) $notification['target_url'], '/')));
    }
}
