<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKokurikulerAssessments extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('academic_student_kokurikuler')) {
            return;
        }

        $this->forge->addField([
            'kokurikuler_nilai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_kegiatan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nilai' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('kokurikuler_nilai_id', true);
        $this->forge->addUniqueKey(
            ['siswa_id', 'kelas_id', 'tahun_ajaran_id', 'nama_kegiatan'],
            'uq_student_kokurikuler_assessment'
        );
        $this->forge->createTable('academic_student_kokurikuler');
    }

    public function down()
    {
        $this->forge->dropTable('academic_student_kokurikuler', true);
    }
}
