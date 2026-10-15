<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExtracurricularSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('extracurriculars')->truncate();
        $data = [
            ['title' => 'Robotik', 'description' => 'Belajar merakit dan memprogram robot untuk kompetisi nasional.', 'icon' => 'fas fa-microchip', 'icon_color' => '#14b8a6', 'sort_order' => 1],
            ['title' => 'Paduan Suara', 'description' => 'Mengembangkan bakat vokal dan apresiasi musik.', 'icon' => 'fas fa-music', 'icon_color' => '#f59e0b', 'sort_order' => 2],
            ['title' => 'Olahraga', 'description' => 'Futsal, basket, voli, taekwondo, dan atletik.', 'icon' => 'fas fa-futbol', 'icon_color' => '#ef4444', 'sort_order' => 3],
            ['title' => 'Seni Rupa', 'description' => 'Melukis, menggambar, desain grafis, dan kriya.', 'icon' => 'fas fa-paint-brush', 'icon_color' => '#8b5cf6', 'sort_order' => 4],
            ['title' => 'Jurnalistik', 'description' => 'Menulis, fotografi, dan publikasi majalah sekolah.', 'icon' => 'fas fa-book', 'icon_color' => '#0ea5e9', 'sort_order' => 5],
            ['title' => 'Pramuka', 'description' => 'Pembentukan karakter lewat kegiatan kepramukaan.', 'icon' => 'fas fa-leaf', 'icon_color' => '#10b981', 'sort_order' => 6],
        ];

        $this->db->table('extracurriculars')->insertBatch($data);
    }
}
