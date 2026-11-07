<?php

namespace App\Database\Migrations;

use App\Libraries\UserTeacherSync;
use CodeIgniter\Database\Migration;

class SyncExistingUsersToTeachers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('auth_users') && $this->db->tableExists('master_guru')) {
            UserTeacherSync::syncAllUnlinkedUsers();
        }
    }

    public function down()
    {
        // No-op to preserve teacher data
    }
}
