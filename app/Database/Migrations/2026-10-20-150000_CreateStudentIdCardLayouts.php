<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentIdCardLayouts extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('student_id_card_layouts')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category' => ['type' => 'VARCHAR', 'constraint' => 20],
            'side' => ['type' => 'VARCHAR', 'constraint' => 10, 'default' => 'front'],
            'field_name' => ['type' => 'VARCHAR', 'constraint' => 30],
            'position_x' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'position_y' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'font_family' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'font_size' => ['type' => 'DECIMAL', 'constraint' => '6,2', 'null' => true],
            'width' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'height' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
            'zoom' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 1.00],
            'pan_x' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'pan_y' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['category', 'side', 'field_name'], 'uq_student_id_card_layout');
        $this->forge->createTable('student_id_card_layouts');
    }

    public function down()
    {
        $this->forge->dropTable('student_id_card_layouts', true);
    }
}
