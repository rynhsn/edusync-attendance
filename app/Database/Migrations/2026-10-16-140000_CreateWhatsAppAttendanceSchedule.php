<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWhatsAppAttendanceSchedule extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('whatsapp_attendance_schedule')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true],
            'send_time' => ['type' => 'TIME', 'default' => '17:00:00'],
            'is_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('whatsapp_attendance_schedule');
        $this->db->table('whatsapp_attendance_schedule')->insert([
            'id' => 1,
            'send_time' => '17:00:00',
            'is_enabled' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_attendance_schedule', true);
    }
}
