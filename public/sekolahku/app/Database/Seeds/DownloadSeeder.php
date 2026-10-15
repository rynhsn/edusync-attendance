<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DownloadSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('downloads')->truncate();
        $data = [
            [
                'category'   => 'Kalender Akademik',
                'title'      => 'Kalender Akademik 2026/2027',
                'description'=> 'Kalender akademik tahun pelajaran 2026/2027 untuk seluruh jenjang pendidikan.',
                'url'        => 'https://drive.google.com/example-kalender-akademik',
                'file_size'  => '1.2 MB',
                'sort_order' => 0,
            ],
            [
                'category'   => 'Kalender Akademik',
                'title'      => 'Jadwal UTS Semester Ganjil 2026/2027',
                'description'=> 'Jadwal pelaksanaan Ujian Tengah Semester Ganjil tahun ajaran 2026/2027.',
                'url'        => 'https://drive.google.com/example-jadwal-uts',
                'file_size'  => '850 KB',
                'sort_order' => 1,
            ],
            [
                'category'   => 'Kurikulum',
                'title'      => 'Kurikulum Merdeka - Panduan Implementasi',
                'description'=> 'Panduan implementasi Kurikulum Merdeka untuk guru dan tenaga kependidikan.',
                'url'        => 'https://drive.google.com/example-kurikulum-merdeka',
                'file_size'  => '3.5 MB',
                'sort_order' => 0,
            ],
            [
                'category'   => 'Kurikulum',
                'title'      => 'Capaian Pembelajaran per Fase',
                'description'=> 'Dokumen capaian pembelajaran untuk setiap fase dalam Kurikulum Merdeka.',
                'url'        => 'https://drive.google.com/example-capaian-pembelajaran',
                'file_size'  => '2.1 MB',
                'sort_order' => 1,
            ],
            [
                'category'   => 'Formulir',
                'title'      => 'Formulir Pendaftaran Siswa Baru',
                'description'=> 'Formulir pendaftaran untuk calon siswa baru tahun ajaran 2026/2027.',
                'url'        => 'https://drive.google.com/example-formulir-ppdb',
                'file_size'  => '450 KB',
                'sort_order' => 0,
            ],
            [
                'category'   => 'Formulir',
                'title'      => 'Surat Pernyataan Orang Tua/Wali',
                'description'=> 'Formulir surat pernyataan orang tua/wali untuk keperluan administrasi sekolah.',
                'url'        => 'https://drive.google.com/example-surat-pernyataan',
                'file_size'  => '320 KB',
                'sort_order' => 1,
            ],
        ];

        $this->db->table('downloads')->insertBatch($data);
    }
}
