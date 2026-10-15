<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherMenuAccess extends Migration
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
            $this->db->query('CREATE UNIQUE INDEX auth_users_guru_unique ON auth_users (guru_id)');
        }

        if (! $this->db->tableExists('teacher_sidebar_settings')) {
            $this->forge->addField([
                'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'menu_key' => ['type' => 'VARCHAR', 'constraint' => 50],
                'is_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey(['guru_id', 'menu_key'], true, true);
            $this->forge->createTable('teacher_sidebar_settings');
        }
    }

    public function down()
    {
        $this->forge->dropTable('teacher_sidebar_settings', true);

        if ($this->db->fieldExists('guru_id', 'auth_users')) {
            $this->db->query('DROP INDEX auth_users_guru_unique ON auth_users');
            $this->forge->dropColumn('auth_users', 'guru_id');
        }
    }
}
