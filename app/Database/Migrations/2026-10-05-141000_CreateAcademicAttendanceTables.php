<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicAttendanceTables extends Migration
{
    public function up()
    {
        $this->createSubjectsTable();
        $this->createTeachersTable();
        $this->createAcademicYearsTable();
        $this->createLessonPeriodsTable();
        $this->createClassAttendanceTables();
    }

    public function down()
    {
        $this->forge->dropTable('absensi_absensi_kelas_siswa', true);
        $this->forge->dropTable('absensi_absensi_kelas', true);
        $this->forge->dropTable('master_jam_pelajaran', true);
        $this->forge->dropTable('master_tahun_ajaran', true);
        $this->forge->dropTable('master_guru', true);
        $this->forge->dropTable('master_mapel', true);
    }

    private function createSubjectsTable(): void
    {
        if ($this->db->tableExists('master_mapel')) {
            return;
        }

        $this->forge->addField([
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'mapel_kode' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'mapel_nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'mapel_keterangan' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('mapel_id', true);
        $this->forge->addUniqueKey('mapel_kode');
        $this->forge->createTable('master_mapel');
    }

    private function createTeachersTable(): void
    {
        if ($this->db->tableExists('master_guru')) {
            return;
        }

        $this->forge->addField([
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'guru_nip' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'guru_nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'guru_email' => ['type' => 'VARCHAR', 'constraint' => 254, 'null' => true],
            'guru_telepon' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('guru_id', true);
        $this->forge->addUniqueKey('guru_nip');
        $this->forge->createTable('master_guru');
    }

    private function createAcademicYearsTable(): void
    {
        if ($this->db->tableExists('master_tahun_ajaran')) {
            return;
        }

        $this->forge->addField([
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun_ajaran_nama' => ['type' => 'VARCHAR', 'constraint' => 20],
            'tanggal_mulai' => ['type' => 'DATE', 'null' => true],
            'tanggal_selesai' => ['type' => 'DATE', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('tahun_ajaran_id', true);
        $this->forge->addUniqueKey('tahun_ajaran_nama');
        $this->forge->createTable('master_tahun_ajaran');
    }

    private function createLessonPeriodsTable(): void
    {
        if ($this->db->tableExists('master_jam_pelajaran')) {
            return;
        }

        $this->forge->addField([
            'jam_pelajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jam_ke' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'jam_nama' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'jam_mulai' => ['type' => 'TIME'],
            'jam_selesai' => ['type' => 'TIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('jam_pelajaran_id', true);
        $this->forge->addUniqueKey('jam_ke');
        $this->forge->createTable('master_jam_pelajaran');
    }

    private function createClassAttendanceTables(): void
    {
        if (! $this->db->tableExists('absensi_absensi_kelas')) {
            $this->forge->addField([
                'absensi_kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'jam_pelajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tanggal' => ['type' => 'DATE'],
                'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('absensi_kelas_id', true);
            $this->forge->addUniqueKey(
                ['kelas_id', 'mapel_id', 'jam_pelajaran_id', 'tahun_ajaran_id', 'tanggal'],
                'uq_absensi_kelas_sesi'
            );
            $this->forge->addKey('tanggal');
            $this->forge->createTable('absensi_absensi_kelas');
        }

        if (! $this->db->tableExists('absensi_absensi_kelas_siswa')) {
            $this->forge->addField([
                'absensi_kelas_siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'absensi_kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['hadir', 'sakit', 'izin', 'alpa'],
                    'default'    => 'hadir',
                ],
                'keterangan' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('absensi_kelas_siswa_id', true);
            $this->forge->addUniqueKey(['absensi_kelas_id', 'siswa_id'], 'uq_absensi_kelas_siswa');
            $this->forge->addKey('siswa_id');
            $this->forge->createTable('absensi_absensi_kelas_siswa');
        }
    }
}
