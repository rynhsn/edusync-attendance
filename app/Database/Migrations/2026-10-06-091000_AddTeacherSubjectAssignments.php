<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherSubjectAssignments extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('teacher_sidebar_settings')) {
            $this->forge->dropTable('teacher_sidebar_settings');
        }

        if ($this->db->fieldExists('guru_id', 'auth_users')) {
            $this->db->query('ALTER TABLE auth_users DROP INDEX auth_users_guru_unique, DROP COLUMN guru_id');
        }

        if (! $this->db->tableExists('teacher_mapel')) {
            $this->forge->addField([
                'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey(['guru_id', 'mapel_id'], true, true);
            $this->forge->addKey('mapel_id');
            $this->forge->createTable('teacher_mapel');
        }
    }

    public function down()
    {
        $this->forge->dropTable('teacher_mapel', true);
    }
}
