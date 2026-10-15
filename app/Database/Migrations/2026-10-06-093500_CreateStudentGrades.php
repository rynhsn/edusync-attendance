<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentGrades extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('academic_student_grades')) {
            return;
        }

        $this->forge->addField([
            'nilai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai_uts' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_uas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_praktik' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('nilai_id', true);
        $this->forge->addUniqueKey(['siswa_id', 'kelas_id', 'mapel_id', 'guru_id', 'tahun_ajaran_id'], 'uq_academic_student_grade');
        $this->forge->createTable('academic_student_grades');
    }

    public function down()
    {
        $this->forge->dropTable('academic_student_grades', true);
    }
}
