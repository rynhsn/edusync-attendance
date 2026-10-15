<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubjectReportGroup extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('kelompok_rapor', 'master_mapel')) {
            $this->forge->addColumn('master_mapel', [
                'kelompok_rapor' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'nasional',
                    'null'       => false,
                    'after'      => 'mapel_nama',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('kelompok_rapor', 'master_mapel')) {
            $this->forge->dropColumn('master_mapel', 'kelompok_rapor');
        }
    }
}
