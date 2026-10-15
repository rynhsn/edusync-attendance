<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKnowledgeGrade extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('nilai_pengetahuan', 'academic_student_grades')) {
            $this->forge->addColumn('academic_student_grades', [
                'nilai_pengetahuan' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => true,
                    'after'      => 'nilai_uts',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('nilai_pengetahuan', 'academic_student_grades')) {
            $this->forge->dropColumn('academic_student_grades', 'nilai_pengetahuan');
        }
    }
}
