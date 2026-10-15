<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentAssessments extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('academic_student_personality')) {
            $this->forge->addField([
                'penilaian_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'nilai_sikap' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
                'nilai_kerajinan' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
                'nilai_kebersihan' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
                'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('penilaian_id', true);
            $this->forge->addUniqueKey(['siswa_id', 'kelas_id', 'tahun_ajaran_id'], 'uq_student_personality_assessment');
            $this->forge->createTable('academic_student_personality');
        }

        if (! $this->db->tableExists('academic_student_extracurricular')) {
            $this->forge->addField([
                'ekstrakurikuler_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'nama_kegiatan' => ['type' => 'VARCHAR', 'constraint' => 100],
                'nilai' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
                'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('ekstrakurikuler_id', true);
            $this->forge->addUniqueKey(
                ['siswa_id', 'kelas_id', 'tahun_ajaran_id', 'nama_kegiatan'],
                'uq_student_extracurricular_assessment'
            );
            $this->forge->createTable('academic_student_extracurricular');
        }
    }

    public function down()
    {
        $this->forge->dropTable('academic_student_extracurricular', true);
        $this->forge->dropTable('academic_student_personality', true);
    }
}
