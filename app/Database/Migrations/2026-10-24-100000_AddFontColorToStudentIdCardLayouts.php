<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFontColorToStudentIdCardLayouts extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('student_id_card_layouts')) {
            return;
        }

        if (! $this->db->fieldExists('font_color', 'student_id_card_layouts')) {
            $this->forge->addColumn('student_id_card_layouts', [
                'font_color' => [
                    'type' => 'VARCHAR',
                    'constraint' => 30,
                    'null' => true,
                    'default' => '#102b58',
                    'after' => 'font_size',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('student_id_card_layouts') && $this->db->fieldExists('font_color', 'student_id_card_layouts')) {
            $this->forge->dropColumn('student_id_card_layouts', 'font_color');
        }
    }
}
