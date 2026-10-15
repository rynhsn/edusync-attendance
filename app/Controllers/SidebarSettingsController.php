<?php

namespace App\Controllers;

use App\Libraries\RoleAccess;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SidebarSettingsController extends BaseController
{
    public function index()
    {
        $requestedRole = $this->request->getGet('role');
        $selectedRole = is_string($requestedRole) && isset(RoleAccess::ROLES[$requestedRole])
            ? $requestedRole
            : 'user';
        $settings = array_merge(
            RoleAccess::defaults($selectedRole),
            RoleAccess::settingsFor($selectedRole)
        );

        return view('panel/sidebar-settings', [
            'title' => 'Pengaturan Akses Menu Role',
            'activePage' => 'sidebar-settings',
            'roles' => RoleAccess::ROLES,
            'menuItems' => RoleAccess::catalog(),
            'menuSections' => RoleAccess::catalogSections(),
            'settings' => $settings,
            'selectedRole' => $selectedRole,
            'message' => session()->getFlashdata('sidebarMessage'),
            'error' => session()->getFlashdata('sidebarError'),
        ]);
    }

    public function save()
    {
        $role = $this->request->getPost('role');
        if (! is_string($role) || ! isset(RoleAccess::ROLES[$role])) {
            return redirect()->to(site_url('sidebar-settings'))
                ->with('sidebarError', 'Role yang dipilih tidak valid.');
        }
        $redirect = redirect()->to(site_url('sidebar-settings?role=' . rawurlencode($role)));

        $submitted = $this->request->getPost('menus');
        if ($submitted !== null && ! is_array($submitted)) {
            return $redirect->with('sidebarError', 'Pengaturan menu tidak valid.');
        }
        $submitted ??= [];
        $allowedKeys = RoleAccess::catalog();
        if (array_diff(array_keys($submitted), array_keys($allowedKeys)) !== []) {
            return $redirect->with('sidebarError', 'Terdapat menu yang tidak dikenal dalam pengaturan.');
        }

        $rows = [];
        foreach ($allowedKeys as $key => $label) {
            $value = $submitted[$key] ?? '0';
            if (! is_string($value) || ! in_array($value, ['0', '1'], true)) {
                return $redirect->with('sidebarError', 'Nilai akses menu tidak valid.');
            }
            $rows[] = [
                'role' => $role,
                'menu_key' => $key,
                'is_enabled' => (int) $value,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $db = db_connect();
        $db->transBegin();
        try {
            $deleted = $db->table('role_sidebar_settings')->where('role', $role)->delete();
            if (! $deleted || ! $db->table('role_sidebar_settings')->insertBatch($rows)) {
                throw new DatabaseException('Could not replace role sidebar settings.');
            }
            if ($db->transStatus() === false) {
                throw new DatabaseException('Sidebar settings transaction failed.');
            }
            $db->transCommit();
        } catch (DatabaseException $exception) {
            $db->transRollback();
            log_message('error', 'Failed to save sidebar settings for role {role}: {message}', [
                'role' => $role,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('sidebarError', 'Pengaturan sidebar gagal disimpan. Silakan coba kembali.');
        }

        return $redirect->with('sidebarMessage', 'Akses menu untuk role ' . RoleAccess::label($role) . ' berhasil disimpan.');
    }
}
