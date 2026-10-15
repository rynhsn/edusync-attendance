<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class CustomSidebarMenuModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getTree(): array
    {
        $rows = $this->db->table('custom_sidebar_items')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->getResultArray();
        $menus = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] === null && $row['parent_key'] === null) {
                $row['id'] = (int) $row['id'];
                $row['children'] = [];
                $menus[$row['id']] = $row;
            }
        }
        foreach ($rows as $row) {
            if ($row['parent_id'] !== null && isset($menus[(int) $row['parent_id']])) {
                $row['id'] = (int) $row['id'];
                $menus[(int) $row['parent_id']]['children'][] = $row;
            }
        }

        return array_values($menus);
    }

    public function getForBuiltInSections(): array
    {
        $rows = $this->db->table('custom_sidebar_items')
            ->where('parent_key !=', null)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->getResultArray();
        $items = [];
        foreach ($rows as $row) {
            $items[$row['parent_key']][] = $row;
        }

        return $items;
    }

    public function getAll(): array
    {
        return $this->db->table('custom_sidebar_items')
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->getResultArray();
    }

    public function getBuiltInIconOverrides(): array
    {
        if (! $this->db->tableExists('sidebar_menu_icon_overrides')) {
            return [];
        }

        $rows = $this->db->table('sidebar_menu_icon_overrides')
            ->get()
            ->getResultArray();
        $icons = [];
        foreach ($rows as $row) {
            $icons[$row['menu_key']] = $row['icon'];
        }

        return $icons;
    }

    public function saveBuiltInIcon(string $menuKey, string $icon): bool
    {
        if (! $this->db->tableExists('sidebar_menu_icon_overrides')) {
            throw new DatabaseException('The sidebar menu icon override table does not exist.');
        }

        $table = $this->db->table('sidebar_menu_icon_overrides');
        $existing = $table->where('menu_key', $menuKey)->get()->getRowArray();
        $values = [
            'icon' => $icon,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($existing !== null) {
            return $this->db->table('sidebar_menu_icon_overrides')
                ->where('menu_key', $menuKey)
                ->update($values);
        }

        $values['menu_key'] = $menuKey;

        return $this->db->table('sidebar_menu_icon_overrides')->insert($values);
    }

    public function find(int $id): ?array
    {
        $row = $this->db->table('custom_sidebar_items')->where('id', $id)->get()->getRowArray();

        return $row ?: null;
    }

    public function isRoot(int $id): bool
    {
        return $this->db->table('custom_sidebar_items')
            ->where('id', $id)
            ->where('parent_id', null)
            ->where('parent_key', null)
            ->countAllResults() === 1;
    }

    public function hasChildren(int $id): bool
    {
        return $this->db->table('custom_sidebar_items')
            ->where('parent_id', $id)
            ->countAllResults() > 0;
    }

    public function save(?int $id, ?int $parentId, ?string $parentKey, string $label, string $url, string $icon, int $sortOrder): bool
    {
        $values = [
            'parent_id' => $parentId,
            'parent_key' => $parentKey,
            'label' => $label,
            'url' => $url,
            'icon' => $icon,
            'sort_order' => $sortOrder,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id === null) {
            $values['created_at'] = date('Y-m-d H:i:s');

            return $this->db->table('custom_sidebar_items')->insert($values);
        }

        $updated = $this->db->table('custom_sidebar_items')->where('id', $id)->update($values);
        if (! $updated && $this->db->affectedRows() === 0 && $this->find($id) === null) {
            throw new DatabaseException('The custom sidebar item no longer exists.');
        }

        return true;
    }

    public function delete(int $id): bool
    {
        return $this->db->table('custom_sidebar_items')->where('id', $id)->delete();
    }
}
