<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCbtOnlineAssessmentTables extends Migration
{
    public function up()
    {
        // 1. Tabel cbt_ujian
        if (! $this->db->tableExists('cbt_ujian')) {
            $this->forge->addField([
                'ujian_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'judul' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'deskripsi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'mapel_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'kelas_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'guru_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'durasi_menit' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 60,
                ],
                'waktu_mulai' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'waktu_selesai' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'null'       => true,
                ],
                'petunjuk' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'acak_soal' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'acak_opsi' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'tampil_nilai' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'tampil_pembahasan' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                ],
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'aktif', // 'draft', 'aktif', 'selesai'
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('ujian_id', true);
            $this->forge->addKey('mapel_id');
            $this->forge->addKey('kelas_id');
            $this->forge->addKey('guru_id');
            $this->forge->createTable('cbt_ujian');
        }

        // 2. Tabel cbt_soal
        if (! $this->db->tableExists('cbt_soal')) {
            $this->forge->addField([
                'soal_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'tipe_soal' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'pilihan_ganda', // 'pilihan_ganda', 'essay'
                ],
                'pertanyaan' => [
                    'type' => 'TEXT',
                ],
                'opsi_a' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'opsi_b' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'opsi_c' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'opsi_d' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'opsi_e' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'kunci_jawaban' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true, // 'A', 'B', 'C', 'D', 'E'
                ],
                'bobot_nilai' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 1.00,
                ],
                'urutan' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('soal_id', true);
            $this->forge->addKey('ujian_id');
            $this->forge->addForeignKey('ujian_id', 'cbt_ujian', 'ujian_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('cbt_soal');
        }

        // 3. Tabel cbt_peserta_ujian
        if (! $this->db->tableExists('cbt_peserta_ujian')) {
            $this->forge->addField([
                'peserta_ujian_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'siswa_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                ],
                'waktu_mulai' => [
                    'type' => 'DATETIME',
                ],
                'waktu_selesai' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'sisa_detik' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                ],
                'jumlah_benar' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'jumlah_salah' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'jumlah_tidak_dijawab' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                ],
                'nilai_pg' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 0.00,
                ],
                'nilai_essay' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 0.00,
                ],
                'nilai_total' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 0.00,
                ],
                'status_pengerjaan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'default'    => 'sedang_mengerjakan', // 'sedang_mengerjakan', 'selesai'
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('peserta_ujian_id', true);
            $this->forge->addKey(['ujian_id', 'siswa_id']);
            $this->forge->addKey('siswa_id');
            $this->forge->addForeignKey('ujian_id', 'cbt_ujian', 'ujian_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('cbt_peserta_ujian');
        }

        // 4. Tabel cbt_jawaban_siswa
        if (! $this->db->tableExists('cbt_jawaban_siswa')) {
            $this->forge->addField([
                'jawaban_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'peserta_ujian_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'soal_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'jawaban_pg' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                ],
                'jawaban_essay' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'is_correct' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => true, // 1 = benar, 0 = salah, NULL = essay belum dinilai
                ],
                'nilai_didapat' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'default'    => 0.00,
                ],
                'catatan_guru' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'ragu_ragu' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('jawaban_id', true);
            $this->forge->addKey(['peserta_ujian_id', 'soal_id']);
            $this->forge->addForeignKey('peserta_ujian_id', 'cbt_peserta_ujian', 'peserta_ujian_id', 'CASCADE', 'CASCADE');
            $this->forge->addForeignKey('soal_id', 'cbt_soal', 'soal_id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('cbt_jawaban_siswa');
        }
    }

    public function down()
    {
        $this->forge->dropTable('cbt_jawaban_siswa', true);
        $this->forge->dropTable('cbt_peserta_ujian', true);
        $this->forge->dropTable('cbt_soal', true);
        $this->forge->dropTable('cbt_ujian', true);
    }
}
