<?php

namespace App\Filters;

use App\Libraries\ActionAccess;
use App\Libraries\OnlineUserTracker;
use App\Libraries\RoleAccess;
use App\Models\AuthUserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ActionAccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $sessionUser = session()->get('authUser');
        if (! is_array($sessionUser) || empty($sessionUser['user_id'])) {
            return redirect()->to(site_url('login'));
        }

        $user = (new AuthUserModel())
            ->select('user_id, email, display_name, username, avatar_path, role, is_verified')
            ->find((int) $sessionUser['user_id']);
        if ($user === null || ! (bool) $user['is_verified']) {
            session()->remove('authUser');

            return redirect()->to(site_url('login'))
                ->with('authError', 'Sesi akun tidak lagi aktif. Silakan login kembali.');
        }

        session()->set('authUser', [
            'user_id' => (int) $user['user_id'],
            'email' => $user['email'],
            'display_name' => $user['display_name'],
            'username' => $user['username'],
            'avatar_path' => $user['avatar_path'],
            'role' => $user['role'],
        ]);
        OnlineUserTracker::touch();

        $actionKey = $arguments[0] ?? '';
        $menuKey = RoleAccess::menuKeyForUri($request->getUri()->getPath());
        if (! is_string($actionKey)
            || ! ActionAccess::can($actionKey, $user['role'])
            || ($menuKey !== null && $menuKey !== 'panel' && ! RoleAccess::isEnabled($user['role'], $menuKey))) {
            return redirect()->to(site_url('auth/access-denied'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
