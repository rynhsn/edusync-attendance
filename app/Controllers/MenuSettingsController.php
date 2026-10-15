<?php

namespace App\Controllers;

use App\Models\CustomSidebarMenuModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class MenuSettingsController extends BaseController
{
    public function index()
    {
        $model = new CustomSidebarMenuModel();

        return view('panel/menu-settings', [
            'title' => 'Pengaturan Menu Sidebar',
            'activePage' => 'menu-settings',
            'menus' => $model->getTree(),
            'sectionItems' => $model->getForBuiltInSections(),
            'sections' => \App\Libraries\RoleAccess::menuSections(),
            'builtInItems' => \App\Libraries\RoleAccess::builtInMenuIconSettings(),
            'items' => $model->getAll(),
            'editId' => (int) ($this->request->getGet('edit') ?? 0),
            'message' => session()->getFlashdata('menuSettingsMessage'),
            'error' => session()->getFlashdata('menuSettingsError'),
        ]);
    }

    public function save()
    {
        $model = new CustomSidebarMenuModel();
        $rawId = $this->request->getPost('id');
        $rawParent = $this->request->getPost('parent');
        $rawLabel = $this->request->getPost('label');
        $rawUrl = $this->request->getPost('url');
        $rawIcon = $this->request->getPost('icon');
        $rawSortOrder = $this->request->getPost('sort_order');
        [$id, $validId] = $this->parseOptionalId($rawId);
        [$parentId, $parentKey, $validParent] = $this->parseParent($rawParent);
        $validText = is_string($rawLabel) && is_string($rawUrl) && is_string($rawIcon);
        $label = is_string($rawLabel) ? trim($rawLabel) : '';
        $url = is_string($rawUrl) ? trim($rawUrl) : '';
        $icon = is_string($rawIcon) ? trim($rawIcon) : '';
        $sortOrder = is_string($rawSortOrder) || is_int($rawSortOrder)
            ? filter_var($rawSortOrder, FILTER_VALIDATE_INT)
            : false;
        $redirect = redirect()->to(site_url('menu-settings'));

        if (! $validText
            || ! $validId
            || ! $validParent
            || ($id !== null && $model->find($id) === null)
            || $label === ''
            || mb_strlen($label) > 80
            || preg_match('/^bi-[a-z][a-z0-9-]{0,60}$/', $icon) !== 1
            || ($url !== '' && preg_match('/^[A-Za-z0-9][A-Za-z0-9/_-]{0,254}$/', $url) !== 1)
            || str_contains($url, '..')
            || $sortOrder === false
            || $sortOrder < 0
            || $sortOrder > 9999) {
            return $redirect->with('menuSettingsError', 'Nama, URL, ikon, atau urutan menu tidak valid.');
        }

        if ($parentId !== null && (! $model->isRoot($parentId) || $id === $parentId)) {
            return $redirect->with('menuSettingsError', 'Submenu harus ditempatkan langsung di bawah menu utama yang valid.');
        }
        if ($id !== null && $model->hasChildren($id) && ($parentId !== null || $parentKey !== null)) {
            return $redirect->with('menuSettingsError', 'Menu yang memiliki submenu tidak dapat dipindahkan menjadi submenu.');
        }
        if (($parentId !== null || $parentKey !== null) && $url === '') {
            return $redirect->with('menuSettingsError', 'Submenu harus memiliki URL tujuan.');
        }

        try {
            if (! $model->save($id, $parentId, $parentKey, $label, $url, $icon, $sortOrder)) {
                throw new DatabaseException('Could not save custom sidebar item.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save custom sidebar menu: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('menuSettingsError', 'Menu gagal disimpan. Silakan coba kembali.');
        }

        return $redirect->with('menuSettingsMessage', 'Menu sidebar berhasil disimpan.');
    }

    public function saveIcon()
    {
        $rawKey = $this->request->getPost('menu_key');
        $rawIcon = $this->request->getPost('icon');
        $menuKey = is_string($rawKey) ? trim($rawKey) : '';
        $icon = is_string($rawIcon) ? trim($rawIcon) : '';
        $validKeys = array_column(\App\Libraries\RoleAccess::builtInMenuIconSettings(), 'key');
        $redirect = redirect()->to(site_url('menu-settings'));

        if (! in_array($menuKey, $validKeys, true)
            || preg_match('/^bi-[a-z][a-z0-9-]{0,60}$/', $icon) !== 1) {
            return $redirect->with('menuSettingsError', 'Menu atau ikon yang dipilih tidak valid.');
        }

        try {
            if (! (new CustomSidebarMenuModel())->saveBuiltInIcon($menuKey, $icon)) {
                throw new DatabaseException('Could not save built-in sidebar menu icon.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to save built-in sidebar icon for {key}: {message}', [
                'key' => $menuKey,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('menuSettingsError', 'Ikon menu gagal disimpan. Pastikan migrasi pengaturan ikon sudah dijalankan.');
        }

        return $redirect->with('menuSettingsMessage', 'Ikon menu berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $redirect = redirect()->to(site_url('menu-settings'));
        $model = new CustomSidebarMenuModel();

        if ($model->find($id) === null) {
            return $redirect->with('menuSettingsError', 'Menu yang akan dihapus tidak ditemukan.');
        }
        if ($model->hasChildren($id)) {
            return $redirect->with('menuSettingsError', 'Pindahkan atau hapus submenu terlebih dahulu.');
        }

        try {
            if (! $model->delete($id)) {
                throw new DatabaseException('Could not delete custom sidebar item.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Failed to delete custom sidebar menu {id}: {message}', [
                'id' => $id,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('menuSettingsError', 'Menu gagal dihapus. Silakan coba kembali.');
        }

        return $redirect->with('menuSettingsMessage', 'Menu berhasil dihapus.');
    }

    private function parseOptionalId(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [null, true];
        }
        $integer = is_string($value) || is_int($value)
            ? filter_var($value, FILTER_VALIDATE_INT)
            : false;

        return $integer !== false && $integer > 0
            ? [(int) $integer, true]
            : [null, false];
    }

    private function parseParent(mixed $value): array
    {
        if ($value === null || $value === '') {
            return [null, null, true];
        }
        if (! is_string($value)) {
            return [null, null, false];
        }
        if (str_starts_with($value, 'custom:')) {
            [$id, $valid] = $this->parseOptionalId(substr($value, 7));

            return $id !== null && $valid
                ? [$id, null, true]
                : [null, null, false];
        }
        if (str_starts_with($value, 'section:')) {
            $key = substr($value, 8);

            return array_key_exists($key, \App\Libraries\RoleAccess::menuSections())
                ? [null, $key, true]
                : [null, null, false];
        }

        return [null, null, false];
    }
}
