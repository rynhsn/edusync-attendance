<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherPhoto extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('foto', 'master_guru')) {
            $this->forge->addColumn('master_guru', [
                'foto' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'guru_telepon',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('foto', 'master_guru')) {
            $this->forge->dropColumn('master_guru', 'foto');
        }
    }
}
