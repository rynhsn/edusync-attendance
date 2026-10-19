<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeacherDutySchedules extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('master_jadwal_piket_guru')) {
            return;
        }

        $this->forge->addField([
            'jadwal_piket_guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'hari' => ['type' => 'VARCHAR', 'constraint' => 10],
            'tugas' => ['type' => 'VARCHAR', 'constraint' => 20],
            'jam_mulai' => ['type' => 'TIME'],
            'jam_selesai' => ['type' => 'TIME'],
            'mapel_ids' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('jadwal_piket_guru_id', true);
        $this->forge->addKey(['guru_id', 'hari', 'is_active'], false, false, 'idx_jadwal_piket_guru_hari');
        $this->forge->createTable('master_jadwal_piket_guru');
    }

    public function down()
    {
        $this->forge->dropTable('master_jadwal_piket_guru', true);
    }
}
