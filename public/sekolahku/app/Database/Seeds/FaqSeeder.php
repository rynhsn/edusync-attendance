<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('faq')->truncate();
        $data = [
            [
                'question' => 'Bagaimana cara mendaftar di SekolahKu?',
                'answer' => 'Pendaftaran dapat dilakukan secara online melalui portal SPMB SekolahKu atau datang langsung ke kantor pendaftaran di sekolah. Informasi lengkap mengenai persyaratan dan jadwal pendaftaran dapat dilihat di halaman SPMB.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Apa saja program unggulan yang tersedia?',
                'answer' => 'SekolahKu memiliki program unggulan seperti Sains & Teknologi, Seni & Budaya, Olahraga, Bahasa Asing, Karakter & Religi, serta Digital Literacy. Selain itu terdapat berbagai ekstrakurikuler yang dapat dipilih sesuai minat siswa.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Apakah SekolahKu menyediakan beasiswa?',
                'answer' => 'Ya, SekolahKu menyediakan program beasiswa bagi siswa berprestasi akademik dan non-akademik, serta beasiswa untuk siswa kurang mampu. Informasi lebih lanjut dapat menghubungi bagian kesiswaan.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Bagaimana sistem pembelajaran di SekolahKu?',
                'answer' => 'SekolahKu menerapkan Kurikulum Merdeka dengan pendekatan pembelajaran aktif, kolaboratif, dan berbasis proyek. Kami juga mengintegrasikan teknologi digital dalam proses belajar mengajar.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Bagaimana cara menghubungi SekolahKu?',
                'answer' => 'Anda dapat menghubungi kami melalui telepon (021) 1234-5678, email info@sekolahku.sch.id, atau datang langsung ke Jl. Pendidikan No. 123, Kota Pelajar.',
                'sort_order' => 5,
            ],
        ];

        $this->db->table('faq')->insertBatch($data);
    }
}
