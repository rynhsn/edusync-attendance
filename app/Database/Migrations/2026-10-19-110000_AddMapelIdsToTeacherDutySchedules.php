<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMapelIdsToTeacherDutySchedules extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('master_jadwal_piket_guru')) {
            return;
        }

        if (! $this->db->fieldExists('mapel_ids', 'master_jadwal_piket_guru')) {
            $this->forge->addColumn('master_jadwal_piket_guru', [
                'mapel_ids' => ['type' => 'TEXT', 'null' => true, 'after' => 'jam_selesai'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('master_jadwal_piket_guru') && $this->db->fieldExists('mapel_ids', 'master_jadwal_piket_guru')) {
            $this->forge->dropColumn('master_jadwal_piket_guru', 'mapel_ids');
        }
    }
}
