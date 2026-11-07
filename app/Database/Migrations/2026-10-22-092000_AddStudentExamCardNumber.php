<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentExamCardNumber extends Migration
{
    public function up()
    {
        // 1. Tambah kolom no_peserta ke master_siswa
        if ($this->db->tableExists('master_siswa') && ! $this->db->fieldExists('no_peserta', 'master_siswa')) {
            $this->forge->addColumn('master_siswa', [
                'no_peserta' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 30,
                    'null'       => true,
                    'after'      => 'nisn',
                ],
            ]);
            $this->db->query('CREATE INDEX idx_master_siswa_no_peserta ON master_siswa (no_peserta)');
        }

        // 2. Default settings untuk cetak kartu ujian/asesmen di school_website_settings
        if ($this->db->tableExists('school_website_settings')) {
            $defaultSettings = [
                'student_exam_card_title'       => 'KARTU ASESMENT SUMATIF AKHIR TAHUN',
                'student_exam_card_subtitle'    => 'TAHUN PELAJARAN 2022/2023',
                'student_exam_card_city_date'   => 'Pandeglang, 03 Juni 2023',
                'student_exam_card_signer_role' => 'Kepala Sekolah',
                'student_exam_card_signer_name' => 'Uswatun Hasanah, S.Pd',
                'student_exam_card_logo'           => 'smp',
                'student_exam_card_custom_logo'    => '',
                'student_exam_card_barcode'        => 'none',
                'student_exam_card_custom_barcode' => '',
                'student_exam_card_layout'         => 'landscape_4',
            ];

            $now = date('Y-m-d H:i:s');
            foreach ($defaultSettings as $key => $val) {
                $exists = $this->db->table('school_website_settings')->where('setting_key', $key)->countAllResults();
                if ($exists === 0) {
                    $this->db->table('school_website_settings')->insert([
                        'setting_key'   => $key,
                        'setting_value' => $val,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('master_siswa') && $this->db->fieldExists('no_peserta', 'master_siswa')) {
            $this->forge->dropColumn('master_siswa', 'no_peserta');
        }

        if ($this->db->tableExists('school_website_settings')) {
            $keys = [
                'student_exam_card_title',
                'student_exam_card_subtitle',
                'student_exam_card_city_date',
                'student_exam_card_signer_role',
                'student_exam_card_signer_name',
                'student_exam_card_logo',
                'student_exam_card_custom_logo',
                'student_exam_card_barcode',
                'student_exam_card_custom_barcode',
                'student_exam_card_layout',
            ];
            $this->db->table('school_website_settings')->whereIn('setting_key', $keys)->delete();
        }
    }
}
