<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherUserSync extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('guru_id', 'auth_users')) {
            $this->forge->addColumn('auth_users', [
                'guru_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'role',
                ],
            ]);
            $this->db->query(
                'ALTER TABLE auth_users '
                . 'ADD UNIQUE KEY auth_users_guru_unique (guru_id), '
                . 'ADD CONSTRAINT fk_auth_users_guru FOREIGN KEY (guru_id) '
                . 'REFERENCES master_guru (guru_id) ON UPDATE CASCADE ON DELETE SET NULL'
            );
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('guru_id', 'auth_users')) {
            $this->db->query(
                'ALTER TABLE auth_users '
                . 'DROP FOREIGN KEY fk_auth_users_guru, '
                . 'DROP INDEX auth_users_guru_unique, '
                . 'DROP COLUMN guru_id'
            );
        }
    }
}
