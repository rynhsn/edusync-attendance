<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class AdminNotificationModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function create(string $type, string $title, string $message, string $targetUrl): bool
    {
        if (! $this->isAvailable()) {
            log_message('error', 'Admin notification tables are missing. Run pending migrations to enable notifications.');

            return false;
        }

        try {
            if (! $this->db->table('admin_notifications')->insert([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'target_url' => $targetUrl,
                'created_at' => date('Y-m-d H:i:s'),
            ])) {
                log_message('error', 'Could not create admin notification of type {type}.', ['type' => $type]);

                return false;
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Could not create admin notification of type {type}: {message}', [
                'type' => $type,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }

        return true;
    }

    public function latestForUser(int $userId, int $limit = 5): array
    {
        if (! $this->isAvailable()) {
            log_message('error', 'Admin notification tables are missing. Run pending migrations to enable notifications.');

            return [];
        }

        return $this->db->table('admin_notifications n')
            ->select('n.id, n.type, n.title, n.message, n.target_url, n.created_at, r.read_at')
            ->join('admin_notification_reads r', 'r.notification_id = n.id AND r.user_id = ' . $this->db->escape($userId), 'left', false)
            ->orderBy('n.created_at', 'DESC')
            ->orderBy('n.id', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function allForUser(int $userId): array
    {
        if (! $this->isAvailable()) {
            log_message('error', 'Admin notification tables are missing. Run pending migrations to enable notifications.');

            return [];
        }

        return $this->db->table('admin_notifications n')
            ->select('n.id, n.type, n.title, n.message, n.target_url, n.created_at, r.read_at')
            ->join('admin_notification_reads r', 'r.notification_id = n.id AND r.user_id = ' . $this->db->escape($userId), 'left', false)
            ->orderBy('n.created_at', 'DESC')
            ->orderBy('n.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function unreadCountForUser(int $userId): int
    {
        if (! $this->isAvailable()) {
            log_message('error', 'Admin notification tables are missing. Run pending migrations to enable notifications.');

            return 0;
        }

        return $this->db->table('admin_notifications n')
            ->join('admin_notification_reads r', 'r.notification_id = n.id AND r.user_id = ' . $this->db->escape($userId), 'left', false)
            ->where('r.read_at', null)
            ->countAllResults();
    }

    public function markRead(int $notificationId, int $userId): ?array
    {
        if (! $this->isAvailable()) {
            throw new DatabaseException('Admin notification tables are missing. Run pending migrations.');
        }

        $notification = $this->db->table('admin_notifications')
            ->where('id', $notificationId)
            ->get()
            ->getRowArray();
        if ($notification === null) {
            return null;
        }

        $read = $this->db->table('admin_notification_reads')
            ->where('notification_id', $notificationId)
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();
        if ($read === null && ! $this->db->table('admin_notification_reads')->insert([
            'notification_id' => $notificationId,
            'user_id' => $userId,
            'read_at' => date('Y-m-d H:i:s'),
        ])) {
            throw new DatabaseException('Could not mark admin notification as read.');
        }

        return $notification;
    }

    public function isAvailable(): bool
    {
        return $this->db->tableExists('admin_notifications')
            && $this->db->tableExists('admin_notification_reads');
    }
}
