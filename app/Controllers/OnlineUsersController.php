<?php

namespace App\Controllers;

use App\Libraries\OnlineUserTracker;
use App\Libraries\RoleAccess;
use Config\Database;

class OnlineUsersController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        return view('panel/online-users', [
            'title' => 'User Sedang Login',
            'activePage' => 'online-users',
            'users' => OnlineUserTracker::activeUsers(),
            'roleLabels' => RoleAccess::ROLES,
            'storageAvailable' => $db->tableExists('admin_online_sessions'),
        ]);
    }
}
