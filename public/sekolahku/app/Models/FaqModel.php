<?php

namespace App\Models;

use CodeIgniter\Model;

class FaqModel extends Model
{
    protected $table         = 'faq';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['question', 'answer', 'category', 'sort_order', 'show'];
    protected $useTimestamps = true;

    /**
     * Retrieve visible FAQ items ordered by sort order.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getVisible(int $limit = 50)
    {
        return $this->where('show', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll($limit);
    }

    /**
     * Batch update sort order for FAQ items.
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
