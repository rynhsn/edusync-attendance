<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyAttendanceWhatsAppLog extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('whatsapp_daily_attendance_log')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tanggal' => ['type' => 'DATE'],
            'orang_tua_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'attempts' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'default' => 0],
            'last_attempt_at' => ['type' => 'DATETIME', 'null' => true],
            'sent_at' => ['type' => 'DATETIME', 'null' => true],
            'last_error' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['tanggal', 'orang_tua_id'], false, true);
        $this->forge->createTable('whatsapp_daily_attendance_log');
    }

    public function down()
    {
        $this->forge->dropTable('whatsapp_daily_attendance_log', true);
    }
}
