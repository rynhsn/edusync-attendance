<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAcademicYearSemester extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('semester', 'master_tahun_ajaran')) {
            $this->forge->addColumn('master_tahun_ajaran', [
                'semester' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'after'      => 'tahun_ajaran_nama',
                ],
            ]);
        }

        $this->db->query('ALTER TABLE master_tahun_ajaran DROP INDEX tahun_ajaran_nama');
        $this->db->query(
            'ALTER TABLE master_tahun_ajaran ADD UNIQUE KEY uq_tahun_ajaran_semester (tahun_ajaran_nama, semester)'
        );
    }

    public function down()
    {
        $this->db->query('ALTER TABLE master_tahun_ajaran DROP INDEX uq_tahun_ajaran_semester');
        $this->db->query('ALTER TABLE master_tahun_ajaran ADD UNIQUE KEY tahun_ajaran_nama (tahun_ajaran_nama)');

        if ($this->db->fieldExists('semester', 'master_tahun_ajaran')) {
            $this->forge->dropColumn('master_tahun_ajaran', 'semester');
        }
    }
}
