<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SpmbApplicationModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function create(array $application): int
    {
        if (! $this->db->table('spmb_applications')->insert($application)) {
            throw new DatabaseException('Could not save SPMB application.');
        }

        $id = (int) $this->db->insertID();
        if ($id < 1) {
            throw new DatabaseException('The SPMB application was not assigned an identifier.');
        }

        return $id;
    }

    public function findById(int $id): ?array
    {
        $application = $this->db->table('spmb_applications')->where('id', $id)->get()->getRowArray();

        return $application ?: null;
    }

    public function getLatest(int $limit = 100): array
    {
        return $this->db->table('spmb_applications')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
