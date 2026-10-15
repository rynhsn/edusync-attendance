<?php

namespace App\Models;

use CodeIgniter\Model;

class DownloadModel extends Model
{
    protected $table         = 'downloads';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['category', 'title', 'description', 'url', 'file_size', 'sort_order', 'show'];
    protected $useTimestamps = true;

    /**
     * Retrieve visible download items ordered by sort order.
     *
     * @return array
     */
    public function getVisible()
    {
        return $this->where('show', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Batch update sort order for download items.
     *
     * @param array $ids Ordered array of item IDs.
     *
     * @return bool
     */
    public function reorder(array $ids): bool
    {
        $case = '';
        foreach ($ids as $i => $id) {
            $id = (int) $id;
            $case .= "WHEN id = {$id} THEN {$i} ";
        }
        $idsList = implode(',', array_map('intval', $ids));
        $this->db->query("UPDATE {$this->table} SET sort_order = CASE {$case} END WHERE id IN ({$idsList})");
        return true;
    }
}
