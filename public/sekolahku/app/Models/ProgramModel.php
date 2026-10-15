<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramModel extends Model
{
    protected $table         = 'programs';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['title', 'description', 'icon', 'link_url', 'link_text', 'sort_order', 'show'];
    protected $useTimestamps = true;

    /**
     * Retrieve programs ordered by sort order.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getOrdered(int $limit = 50)
    {
        return $this->orderBy('sort_order', 'ASC')
                    ->findAll($limit);
    }

    /**
     * Batch update sort order for program items.
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
