<?php

namespace App\Models;

use CodeIgniter\Model;

class GalleryModel extends Model
{
    protected $table         = 'galleries';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['category', 'image', 'caption', 'is_featured', 'sort_order', 'show'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    /**
     * Retrieve distinct gallery categories.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->distinct()
                    ->select('category')
                    ->orderBy('category', 'ASC')
                    ->findAll();
    }

    /**
     * Retrieve visible gallery items ordered by sort order.
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
     * Retrieve gallery data for XML sitemap generation.
     *
     * @return array
     */
    public function getForSitemap(): array
    {
        return $this->select('updated_at')
                    ->where('show', 1)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Batch update sort order for gallery items.
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
