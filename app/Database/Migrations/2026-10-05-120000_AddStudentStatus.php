<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentStatus extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('status', 'master_siswa')) {
            $this->forge->addColumn('master_siswa', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'aktif',
                    'null'       => false,
                    'after'      => 'foto',
                ],
            ]);
        }

        $this->db->table('master_siswa')
            ->where('deleted_at IS NOT NULL', null, false)
            ->update(['status' => 'tidak_aktif']);
    }

    public function down()
    {
        if ($this->db->fieldExists('status', 'master_siswa')) {
            $this->forge->dropColumn('master_siswa', 'status');
        }
    }
}
