<?php

use App\Models\MenuItemModel;
use App\Models\SettingModel;

if (!function_exists('get_menu_tree')) {
    function get_menu_tree($sectionSettings = null)
    {
        $model = new MenuItemModel();
        $items = $model->where('status', 1)->orderBy('sort_order', 'ASC')->findAll();

        // Filter out page-type items whose linked page is not published
        $pageIds = array_filter(array_column($items, 'page_id'));
        if (!empty($pageIds)) {
            $pageModel = new \App\Models\PageModel();
            $publishedRows = $pageModel->whereIn('id', $pageIds)->where('status', 'published')->findAll();
            $publishedIds = array_column($publishedRows, 'id');
            $items = array_filter($items, function ($item) use ($publishedIds) {
                if ($item['type'] === 'page' && !empty($item['page_id'])) {
                    return in_array($item['page_id'], $publishedIds);
                }
                return true;
            });
        }

        if ($sectionSettings === null) {
            $ss = get_section_settings_array();
        } else {
            $ss = is_string($sectionSettings) ? json_decode($sectionSettings, true) : $sectionSettings;
            $ss = is_array($ss) ? $ss : [];
        }

        $items = array_filter($items, function ($item) use ($ss) {
            if (!empty($item['section_key'])) {
                return !empty($ss[$item['section_key']]['show']);
            }
            return true;
        });

        return build_menu_tree($items);
    }
}

if (!function_exists('get_section_settings_array')) {
    function get_section_settings_array()
    {
        static $ss = null;
        if ($ss === null) {
            $model = new SettingModel();
            $raw   = $model->get('section_settings');
            $ss    = !empty($raw) ? json_decode($raw, true) : [];
        }
        return $ss;
    }
}

if (!function_exists('build_menu_tree')) {
    function build_menu_tree(array $items, $parentId = null)
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item['parent_id'] === $parentId) {
                $children = build_menu_tree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        return array_values($branch);
    }
}

if (!function_exists('resolve_menu_url')) {
    function resolve_menu_url(string $url)
    {
        if (preg_match('/^\{(\w+)\}$/', $url, $m)) {
            $setting = (new SettingModel())->get($m[1]);
            return $setting ?: '#';
        }
        return $url;
    }
}

if (!function_exists('render_menu_items')) {
    function render_menu_items(array $items, $isChild = false)
    {
        $html = '';
        $currentUrl = service('uri')->getPath();
        $currentUrl = ltrim($currentUrl, '/');

        foreach ($items as $item) {
            $hasChildren = !empty($item['children']);
            $url   = !empty($item['section_key']) ? '#' . $item['section_key'] : resolve_menu_url($item['url']);
            $target = $item['target'] === '_blank' ? ' target="_blank" rel="noopener"' : '';

            $isActive = false;
            if ($url === '' || $url === '/') {
                $isActive = $currentUrl === '' || $currentUrl === '/';
            } elseif ($url !== '#' && !str_starts_with($url, '#')) {
                $cleanUrl = rtrim($url, '/');
                $isActive = $currentUrl === $cleanUrl
                         || str_starts_with($currentUrl, $cleanUrl . '/');
            }
            $activeClass = $isActive ? ' class="active"' : '';

            if ($hasChildren) {
                $html .= '<div class="nav-dropdown">';
                $html .= '<button class="nav-dd-btn" aria-expanded="false" aria-haspopup="true">'
                      . esc($item['title']) . ' <i class="fas fa-chevron-down" aria-hidden="true"></i>'
                      . '</button>';
                $html .= '<div class="nav-dd-menu" role="menu">';
                $html .= render_menu_items($item['children'], true);
                $html .= '</div></div>';
            } else {
                $html .= '<a href="' . base_url($url) . '"' . $target . $activeClass . ' role="menuitem">'
                      . esc($item['title'])
                      . '</a>';
            }
        }
        return $html;
    }
}
