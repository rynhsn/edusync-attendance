<?php

namespace App\Models;

use CodeIgniter\Model;

class AchievementModel extends Model
{
    protected $table         = 'achievements';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['student_name', 'photo', 'class_name', 'achievement', 'level', 'year', 'medal', 'sort_order', 'show'];
    protected $useTimestamps = true;

    /**
     * Retrieve achievements ordered by sort order.
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
     * Batch update sort order for achievement items.
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
