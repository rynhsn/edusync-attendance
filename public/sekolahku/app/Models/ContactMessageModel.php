<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactMessageModel extends Model
{
    protected $table         = 'contact_messages';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'email', 'subject', 'message', 'is_read'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    /**
     * Count unread contact messages.
     *
     * @return int
     */
    public function getUnreadCount(): int
    {
        return $this->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Retrieve the most recent contact messages.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getRecent(int $limit = 5)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }
}
