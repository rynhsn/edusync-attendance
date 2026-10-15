<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthCredentials extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('username', 'auth_users')) {
            $this->forge->addColumn('auth_users', [
                'username' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'null'       => true,
                    'after'      => 'display_name',
                ],
            ]);
            $this->db->query('CREATE UNIQUE INDEX auth_users_username_unique ON auth_users (username)');
        }

        if (! $this->db->fieldExists('password_hash', 'auth_users')) {
            $this->forge->addColumn('auth_users', [
                'password_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'username',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('password_hash', 'auth_users')) {
            $this->forge->dropColumn('auth_users', 'password_hash');
        }

        if ($this->db->fieldExists('username', 'auth_users')) {
            $this->db->query('DROP INDEX auth_users_username_unique ON auth_users');
            $this->forge->dropColumn('auth_users', 'username');
        }
    }
}
