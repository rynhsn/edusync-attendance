<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run()
    {
        $this->db->table('galleries')->truncate();
        $data = [
            ['category' => 'Kegiatan', 'image' => 'https://placehold.co/800x600/0c4a6e/ffffff?text=Upacara+Bendera', 'caption' => 'Upacara bendera setiap hari Senin', 'is_featured' => 1, 'sort_order' => 1],
            ['category' => 'Kegiatan', 'image' => 'https://placehold.co/800x600/14b8a6/ffffff?text=Kegiatan+Belajar', 'caption' => 'Suasana belajar mengajar di kelas', 'is_featured' => 1, 'sort_order' => 2],
            ['category' => 'Prestasi', 'image' => 'https://placehold.co/800x600/f59e0b/ffffff?text=Penghargaan', 'caption' => 'Penyerahan penghargaan kepada siswa berprestasi', 'is_featured' => 1, 'sort_order' => 3],
            ['category' => 'Kegiatan', 'image' => 'https://placehold.co/800x600/ef4444/ffffff?text=Olahraga', 'caption' => 'Kegiatan olahraga dan ekstrakurikuler', 'is_featured' => 0, 'sort_order' => 4],
            ['category' => 'Akademik', 'image' => 'https://placehold.co/800x600/8b5cf6/ffffff?text=Lab+Komputer', 'caption' => 'Praktik di laboratorium komputer', 'is_featured' => 0, 'sort_order' => 5],
            ['category' => 'Kegiatan', 'image' => 'https://placehold.co/800x600/0ea5e9/ffffff?text=Seni+Budaya', 'caption' => 'Pentas seni budaya siswa', 'is_featured' => 0, 'sort_order' => 6],
        ];

        $this->db->table('galleries')->insertBatch($data);
    }
}
