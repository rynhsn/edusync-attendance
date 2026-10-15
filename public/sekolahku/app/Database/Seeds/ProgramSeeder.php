<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('programs')->truncate();
        $data = [
            ['title' => 'Sains & Teknologi', 'description' => 'Laboratorium modern dan pembelajaran berbasis proyek untuk riset dan inovasi.', 'icon' => 'fas fa-flask', 'sort_order' => 1],
            ['title' => 'Seni & Budaya', 'description' => 'Wadah pengembangan bakat seni melalui musik, tari, teater, dan seni rupa.', 'icon' => 'fas fa-palette', 'sort_order' => 2],
            ['title' => 'Olahraga', 'description' => 'Fasilitas lengkap dan pembinaan atlet muda di berbagai cabang olahraga.', 'icon' => 'fas fa-running', 'sort_order' => 3],
            ['title' => 'Bahasa Asing', 'description' => 'Program bilingual dengan native speaker untuk menghadapi era globalisasi.', 'icon' => 'fas fa-globe', 'sort_order' => 4],
            ['title' => 'Karakter & Religi', 'description' => 'Pendidikan karakter berbasis nilai agama untuk pribadi berakhlak mulia.', 'icon' => 'fas fa-hand-holding-heart', 'sort_order' => 5],
            ['title' => 'Digital Literacy', 'description' => 'Pembelajaran coding, desain grafis, dan literasi digital untuk era 4.0.', 'icon' => 'fas fa-laptop-code', 'sort_order' => 6],
        ];

        $this->db->table('programs')->insertBatch($data);
    }
}
