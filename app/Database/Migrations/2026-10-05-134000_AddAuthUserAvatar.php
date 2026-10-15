<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthUserAvatar extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('avatar_path', 'auth_users')) {
            $this->forge->addColumn('auth_users', [
                'avatar_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'password_hash',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('avatar_path', 'auth_users')) {
            $this->forge->dropColumn('auth_users', 'avatar_path');
        }
    }
}
