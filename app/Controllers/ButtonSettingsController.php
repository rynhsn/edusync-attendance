<?php

namespace App\Controllers;

use App\Libraries\ActionAccess;
use App\Libraries\RoleAccess;
use App\Models\AuthUserModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ButtonSettingsController extends BaseController
{
    public function index()
    {
        $requestedRole = $this->request->getGet('role');
        $roles = array_diff_key(\App\Libraries\RoleAccess::ROLES, ['super_admin' => true]);
        $selectedRole = is_string($requestedRole) && isset($roles[$requestedRole])
            ? $requestedRole
            : 'user';
        $requestedUserId = filter_var($this->request->getGet('user_id'), FILTER_VALIDATE_INT);
        $users = (new AuthUserModel())
            ->select('user_id, display_name, username, email, role')
            ->where('role !=', 'super_admin')
            ->orderBy('display_name', 'ASC')
            ->findAll();
        $validUserIds = array_map(static fn(array $user): int => (int) $user['user_id'], $users);
        $selectedUserId = $requestedUserId !== false && in_array((int) $requestedUserId, $validUserIds, true)
            ? (int) $requestedUserId
            : null;
        $targetType = $this->request->getGet('target') === 'user' ? 'user' : 'role';
        if ($targetType === 'user' && $selectedUserId === null && $users !== []) {
            $selectedUserId = (int) $users[0]['user_id'];
        }
        $selectedUser = null;
        foreach ($users as $user) {
            if ((int) $user['user_id'] === $selectedUserId) {
                $selectedUser = $user;
                break;
            }
        }
        $effectiveRole = $targetType === 'user' && $selectedUser !== null
            ? (string) $selectedUser['role']
            : $selectedRole;
        $roleSettings = ActionAccess::settingsForRole($effectiveRole);
        $userSettings = $targetType === 'user' && $selectedUserId !== null
            ? ActionAccess::settingsForUser($selectedUserId)
            : [];

        return view('panel/button-settings', [
            'title' => 'Pengaturan Akses Tombol',
            'activePage' => 'button-settings',
            'roles' => $roles,
            'users' => $users,
            'actionGroups' => ActionAccess::catalog(),
            'settings' => $roleSettings,
            'userSettings' => $userSettings,
            'targetType' => $targetType,
            'selectedRole' => $selectedRole,
            'selectedUserId' => $selectedUserId,
            'selectedUser' => $selectedUser,
            'message' => session()->getFlashdata('buttonMessage'),
            'error' => session()->getFlashdata('buttonError'),
        ]);
    }

    public function save()
    {
        $targetType = $this->request->getPost('target');
        $role = $this->request->getPost('role');
        $roles = array_diff_key(RoleAccess::ROLES, ['super_admin' => true]);
        if (! in_array($targetType, ['role', 'user'], true)) {
            return redirect()->to(site_url('button-settings'))->with('buttonError', 'Jenis pengaturan tidak valid.');
        }
        $userId = null;
        if ($targetType === 'role' && (! is_string($role) || ! isset($roles[$role]))) {
            return redirect()->to(site_url('button-settings'))->with('buttonError', 'Role yang dipilih tidak valid.');
        }
        if ($targetType === 'user') {
            $userId = filter_var($this->request->getPost('user_id'), FILTER_VALIDATE_INT);
            if ($userId === false || $userId < 1) {
                return redirect()->to(site_url('button-settings?target=user'))->with('buttonError', 'User yang dipilih tidak valid.');
            }
            $selectedUser = (new AuthUserModel())->select('user_id, display_name, username, email, role')
                ->where('role !=', 'super_admin')
                ->find((int) $userId);
            if ($selectedUser === null) {
                return redirect()->to(site_url('button-settings?target=user'))->with('buttonError', 'User tidak ditemukan atau tidak dapat diatur.');
            }
            $role = $selectedUser['role'];
        }
        $redirectQuery = $targetType === 'role'
            ? 'target=role&role=' . rawurlencode((string) $role)
            : 'target=user&user_id=' . (int) $userId;
        $redirect = redirect()->to(site_url('button-settings?' . $redirectQuery));
        $submitted = $this->request->getPost('actions');
        if ($submitted !== null && ! is_array($submitted)) {
            return $redirect->with('buttonError', 'Pengaturan akses tombol tidak valid.');
        }
        $submitted ??= [];
        $allowedActions = ActionAccess::flatCatalog();
        if (array_diff(array_keys($submitted), array_keys($allowedActions)) !== []) {
            return $redirect->with('buttonError', 'Terdapat akses tombol yang tidak dikenal.');
        }

        $rows = [];
        foreach ($allowedActions as $key => $label) {
            $value = $submitted[$key] ?? ($targetType === 'role' ? '0' : '');
            if (! is_string($value) || ! in_array($value, $targetType === 'role' ? ['0', '1'] : ['', '0', '1'], true)) {
                return $redirect->with('buttonError', 'Nilai akses tombol tidak valid.');
            }
            if ($targetType === 'role' || $value !== '') {
                $row = [
                    'action_key' => $key,
                    'is_enabled' => (int) $value,
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                if ($targetType === 'role') {
                    $row['role'] = $role;
                } else {
                    $row['user_id'] = (int) $userId;
                }
                $rows[] = $row;
            }
        }

        $db = db_connect();
        $db->transBegin();
        try {
            $table = $targetType === 'role' ? 'role_action_settings' : 'user_action_settings';
            $whereColumn = $targetType === 'role' ? 'role' : 'user_id';
            $whereValue = $targetType === 'role' ? $role : (int) $userId;
            $db->table($table)->where($whereColumn, $whereValue)->delete();
            if (($rows !== [] && ! $db->table($table)->insertBatch($rows))
                || $db->transStatus() === false
            ) {
                throw new DatabaseException('Could not replace role action settings.');
            }
            $db->transCommit();
        } catch (DatabaseException $exception) {
            $db->transRollback();
            log_message('error', 'Failed to save {target} action settings: {message}', [
                'target' => $targetType === 'role' ? 'role ' . $role : 'user ' . $userId,
                'message' => $exception->getMessage(),
            ]);

            return $redirect->with('buttonError', 'Pengaturan akses tombol gagal disimpan. Silakan coba kembali.');
        }

        $targetLabel = $targetType === 'role'
            ? 'role ' . $roles[$role]
            : 'user ' . ($selectedUser['display_name'] ?: $selectedUser['username'] ?: $selectedUser['email']);

        return $redirect->with('buttonMessage', 'Akses tombol untuk ' . $targetLabel . ' berhasil disimpan.');
    }
}
