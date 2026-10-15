<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('testimonials')->truncate();
        $data = [
            ['name' => 'Alya Putri', 'role' => 'Siswi Kelas XII · Juara Olimpiade Sains', 'quote' => 'SekolahKu memberikan pengalaman belajar yang luar biasa. Gurunya sangat mendukung dan fasilitasnya lengkap. Saya bangga menjadi bagian dari SekolahKu.', 'sort_order' => 1],
            ['name' => 'Andi Pratama', 'role' => 'Orang Tua Siswa', 'quote' => 'Pendidikan karakter di SekolahKu sangat membekas. Anak saya jadi lebih mandiri, percaya diri, dan memiliki akhlak yang baik. Terima kasih SekolahKu.', 'sort_order' => 2],
            ['name' => 'Raka Firmansyah', 'role' => 'Alumni · Universitas Indonesia', 'quote' => 'Berkat bimbingan guru di SekolahKu, saya berhasil lolos ke PTN favorit melalui jalur SNBP. Program pembinaan olimpiadenya juga sangat membantu pengembangan diri.', 'sort_order' => 3],
        ];

        $this->db->table('testimonials')->insertBatch($data);
    }
}
