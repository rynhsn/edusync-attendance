<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuItemModel extends Model
{
    protected $table         = 'menu_items';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['parent_id', 'title', 'url', 'target', 'icon', 'section_key', 'sort_order', 'status', 'type', 'page_id'];
    protected $useTimestamps = true;

    /**
     * Retrieve active menu items organized as a tree structure.
     *
     * @return array
     */
    public function getActiveTree()
    {
        $items = $this->where('status', 1)->orderBy('sort_order', 'ASC')->findAll();
        return $this->buildTree($items);
    }

    /**
     * Build a nested tree structure from a flat array of items.
     *
     * @param array     $items    Flat array of menu items.
     * @param int|null  $parentId The parent ID to filter by.
     *
     * @return array
     */
    public function buildTree(array $items, $parentId = null)
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $children = $this->buildTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        return $branch;
    }

    /**
     * Retrieve all menu items as a flattened tree with indentation metadata.
     *
     * @return array
     */
    public function getFlatTree()
    {
        $items = $this->orderBy('sort_order', 'ASC')->findAll();
        $tree  = $this->buildTree($items);
        $flat  = [];
        $this->flattenTree($tree, $flat);
        return $flat;
    }

    /**
     * Recursively flatten a tree structure into a single-dimensional array.
     *
     * @param array $tree The tree structure to flatten.
     * @param array &$flat The output flat array (passed by reference).
     *
     * @return void
     */
    private function flattenTree(array $tree, array &$flat)
    {
        foreach ($tree as $item) {
            $children = $item['children'] ?? [];
            unset($item['children']);
            $flat[] = $item;
            if (!empty($children)) {
                $this->flattenTree($children, $flat);
            }
        }
    }

    /**
     * Retrieve all descendant IDs of a given menu item.
     *
     * @param int $id The parent menu item ID.
     *
     * @return array
     */
    public function getDescendantIds($id)
    {
        $ids = [];
        $children = $this->where('parent_id', $id)->findAll();
        foreach ($children as $child) {
            $ids[] = $child['id'];
            $ids = array_merge($ids, $this->getDescendantIds($child['id']));
        }
        return $ids;
    }

    /**
     * Retrieve root-level menu items (no parent).
     *
     * @return array
     */
    public function getRootItems()
    {
        return $this->where('parent_id', null)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Retrieve root-level menu items excluding specified IDs.
     *
     * @param array $excludeIds Array of IDs to exclude.
     *
     * @return array
     */
    public function getAvailableParents(array $excludeIds)
    {
        return $this->where('parent_id', null)
                    ->whereNotIn('id', $excludeIds)
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    /**
     * Retrieve a menu item by its associated page ID.
     *
     * @param int $pageId The page ID.
     *
     * @return array|null
     */
    public function getByPageId(int $pageId)
    {
        return $this->where('page_id', $pageId)->first();
    }

    /**
     * Count the number of child menu items for a given parent.
     *
     * @param int $parentId The parent menu item ID.
     *
     * @return int
     */
    public function countChildren(int $parentId): int
    {
        return $this->where('parent_id', $parentId)
                    ->countAllResults();
    }

    /**
     * Batch reorder menu items with parent and sort order updates inside a transaction.
     *
     * @param array $items Array of items with id, parent_id, and sort_order.
     *
     * @return bool
     */
    public function reorderItems(array $items): bool
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        foreach ($items as $item) {
            $id        = (int) $item['id'];
            $parentId  = !empty($item['parent_id']) ? (int) $item['parent_id'] : null;
            $sortOrder = (int) $item['sort_order'];

            $db->table($this->table)
                ->where('id', $id)
                ->update([
                    'parent_id'  => $parentId,
                    'sort_order' => $sortOrder,
                ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return false;
        }

        $db->transCommit();
        return true;
    }
}
