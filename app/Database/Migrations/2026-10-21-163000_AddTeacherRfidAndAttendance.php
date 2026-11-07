<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherRfidAndAttendance extends Migration
{
    public function up()
    {
        // 1. Tambahkan kolom rfid_tag ke master_guru
        if ($this->db->tableExists('master_guru') && ! $this->db->fieldExists('rfid_tag', 'master_guru')) {
            $this->forge->addColumn('master_guru', [
                'rfid_tag' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'after'      => 'guru_telepon',
                ],
            ]);
            $this->db->query('CREATE INDEX idx_master_guru_rfid_tag ON master_guru (rfid_tag)');
        }

        // 2. Tambahkan kolom guru_id ke absensi_absensi_sekolah
        if ($this->db->tableExists('absensi_absensi_sekolah') && ! $this->db->fieldExists('guru_id', 'absensi_absensi_sekolah')) {
            $this->forge->addColumn('absensi_absensi_sekolah', [
                'guru_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'siswa_id',
                ],
            ]);
            $this->db->query('CREATE INDEX idx_absensi_sekolah_guru_id ON absensi_absensi_sekolah (guru_id)');
        }

        // 3. Tambahkan kolom guru_id ke absensi_log_tap dan buat siswa_id nullable
        if ($this->db->tableExists('absensi_log_tap')) {
            if (! $this->db->fieldExists('guru_id', 'absensi_log_tap')) {
                $this->forge->addColumn('absensi_log_tap', [
                    'guru_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => 'siswa_id',
                    ],
                ]);
                $this->db->query('CREATE INDEX idx_absensi_log_tap_guru_id ON absensi_log_tap (guru_id)');
            }

            // Pastikan siswa_id di absensi_log_tap dapat bernilai NULL (karena guru tidak punya siswa_id)
            $this->forge->modifyColumn('absensi_log_tap', [
                'siswa_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('master_guru') && $this->db->fieldExists('rfid_tag', 'master_guru')) {
            $this->forge->dropColumn('master_guru', 'rfid_tag');
        }

        if ($this->db->tableExists('absensi_absensi_sekolah') && $this->db->fieldExists('guru_id', 'absensi_absensi_sekolah')) {
            $this->forge->dropColumn('absensi_absensi_sekolah', 'guru_id');
        }

        if ($this->db->tableExists('absensi_log_tap') && $this->db->fieldExists('guru_id', 'absensi_log_tap')) {
            $this->forge->dropColumn('absensi_log_tap', 'guru_id');
        }
    }
}
