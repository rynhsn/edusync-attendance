<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClassSessionToDailyAttendanceWhatsAppLog extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('whatsapp_daily_attendance_log')) {
            throw new \RuntimeException('Tabel whatsapp_daily_attendance_log belum tersedia.');
        }

        if (! $this->db->fieldExists('absensi_kelas_id', 'whatsapp_daily_attendance_log')) {
            $this->forge->addColumn('whatsapp_daily_attendance_log', [
                'absensi_kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'orang_tua_id'],
            ]);
        }

        $indexes = $this->db->query('SHOW INDEX FROM `whatsapp_daily_attendance_log`')->getResultArray();
        $indexColumns = [];
        foreach ($indexes as $index) {
            $indexColumns[$index['Key_name']][] = $index['Column_name'];
        }

        if (($indexColumns['whatsapp_daily_attendance_date_parent_unique'] ?? []) === ['tanggal', 'orang_tua_id']) {
            $this->forge->dropKey('whatsapp_daily_attendance_log', 'whatsapp_daily_attendance_date_parent_unique', false);
        }

        if (($indexColumns['whatsapp_daily_attendance_date_parent_session_unique'] ?? []) !== ['tanggal', 'orang_tua_id', 'absensi_kelas_id']) {
            $this->forge->addUniqueKey(
                ['tanggal', 'orang_tua_id', 'absensi_kelas_id'],
                'whatsapp_daily_attendance_date_parent_session_unique'
            );
            $this->forge->processIndexes('whatsapp_daily_attendance_log');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('whatsapp_daily_attendance_log')
            || ! $this->db->fieldExists('absensi_kelas_id', 'whatsapp_daily_attendance_log')) {
            return;
        }

        $this->forge->dropKey('whatsapp_daily_attendance_log', 'whatsapp_daily_attendance_date_parent_session_unique', false);
        $this->forge->dropColumn('whatsapp_daily_attendance_log', 'absensi_kelas_id');
        $this->forge->addUniqueKey(['tanggal', 'orang_tua_id'], 'whatsapp_daily_attendance_date_parent_unique');
        $this->forge->processIndexes('whatsapp_daily_attendance_log');
    }
}
