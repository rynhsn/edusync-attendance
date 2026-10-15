<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExtracurricularAttendance extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('absensi_ekstrakurikuler')) {
            return;
        }

        $this->forge->addField([
            'absensi_ekstrakurikuler_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_kegiatan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'tanggal' => ['type' => 'DATE'],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['hadir', 'sakit', 'izin', 'alpa'],
                'default' => 'hadir',
            ],
            'keterangan' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('absensi_ekstrakurikuler_id', true);
        $this->forge->addUniqueKey(
            ['siswa_id', 'nama_kegiatan', 'tanggal'],
            'uq_ekskul_siswa_kegiatan_tanggal'
        );
        $this->forge->addKey('tanggal');
        $this->forge->createTable('absensi_ekstrakurikuler');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_ekstrakurikuler', true);
    }
}
