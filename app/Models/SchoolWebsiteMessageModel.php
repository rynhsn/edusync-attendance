<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolWebsiteMessageModel extends Model
{
    protected $table = 'school_website_messages';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'name',
        'email',
        'subject',
        'message',
        'is_read',
    ];
    protected $useTimestamps = true;

    public function getLatest(int $perPage = 25): array
    {
        return $this->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate($perPage);
    }

    public function getUnreadCount(): int
    {
        return $this->where('is_read', 0)->countAllResults();
    }
}
