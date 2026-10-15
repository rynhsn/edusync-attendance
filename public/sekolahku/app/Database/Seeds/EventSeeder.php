<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('events')->truncate();
        $data = [
            [
                'title' => 'Upacara HUT RI ke-81',
                'slug' => 'upacara-hut-ri-ke-81',
                'description' => 'Seluruh siswa dan guru mengikuti upacara peringatan Hari Kemerdekaan RI.',
                'location' => 'Lapangan Sekolah',
                'event_date' => '2026-08-15',
                'event_time' => '07.00 WIB',
                'sort_order' => 1,
            ],
            [
                'title' => 'Lomba Antar Kelas',
                'slug' => 'lomba-antar-kelas',
                'description' => 'Perlombaan antar kelas meliputi cerdas cermat, seni, dan olahraga.',
                'location' => 'Aula Sekolah',
                'event_date' => '2026-08-22',
                'event_time' => '08.00 WIB',
                'sort_order' => 2,
            ],
            [
                'title' => 'Kegiatan Bakti Sosial',
                'slug' => 'kegiatan-bakti-sosial',
                'description' => 'Kunjungan dan bakti sosial siswa ke panti asuhan di sekitar sekolah.',
                'location' => 'Panti Asuhan',
                'event_date' => '2026-09-05',
                'event_time' => '07.30 WIB',
                'sort_order' => 3,
            ],
            [
                'title' => 'Pentas Seni Akhir Semester',
                'slug' => 'pentas-seni-akhir-semester',
                'description' => 'Pertunjukan seni dari siswa sebagai puncak kegiatan akhir semester.',
                'location' => 'Gedung Serba Guna',
                'event_date' => '2026-09-12',
                'event_time' => '09.00 WIB',
                'sort_order' => 4,
            ],
        ];

        $this->db->table('events')->insertBatch($data);
    }
}
