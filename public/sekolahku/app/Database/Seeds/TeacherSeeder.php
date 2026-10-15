<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('teachers')->truncate();

        $data = [
            [
                'name'             => 'Dr. Sari Wijaya, M.Pd.',
                'photo'            => '',
                'role'             => 'Kepala Sekolah',
                'experience'       => '20 Tahun Pengalaman',
                'education'        => 'S3 Pendidikan',
                'social_facebook'  => '',
                'social_instagram' => '',
                'social_linkedin'  => '',
                'sort_order'       => 1,
                'show'             => 1,
            ],
            [
                'name'             => 'Budi Santoso, S.Si., M.T.',
                'photo'            => '',
                'role'             => 'Guru Sains & Teknologi',
                'experience'       => '15 Tahun Pengalaman',
                'education'        => 'S2 Teknik',
                'social_facebook'  => '',
                'social_instagram' => '',
                'social_linkedin'  => '',
                'sort_order'       => 2,
                'show'             => 1,
            ],
            [
                'name'             => 'Ani Rahmawati, S.Pd., M.Hum.',
                'photo'            => '',
                'role'             => 'Guru Bahasa & Sastra',
                'experience'       => '12 Tahun Pengalaman',
                'education'        => 'S2 Linguistik',
                'social_facebook'  => '',
                'social_instagram' => '',
                'social_linkedin'  => '',
                'sort_order'       => 3,
                'show'             => 1,
            ],
            [
                'name'             => 'Hendra Pratama, S.Pd.',
                'photo'            => '',
                'role'             => 'Guru Olahraga',
                'experience'       => '10 Tahun Pengalaman',
                'education'        => 'S1 Pendidikan',
                'social_facebook'  => '',
                'social_instagram' => '',
                'social_linkedin'  => '',
                'sort_order'       => 4,
                'show'             => 1,
            ],
            [
                'name'             => 'Dewi Lestari, S.Pd., M.Pd.',
                'photo'            => '',
                'role'             => 'Guru Matematika',
                'experience'       => '18 Tahun Pengalaman',
                'education'        => 'S2 Pendidikan',
                'social_facebook'  => '',
                'social_instagram' => '',
                'social_linkedin'  => '',
                'sort_order'       => 5,
                'show'             => 1,
            ],
            [
                'name'             => 'Rudi Hermawan, S.Si., M.Kom.',
                'photo'            => '',
                'role'             => 'Guru Informatika',
                'experience'       => '14 Tahun Pengalaman',
                'education'        => 'S2 Komputer',
                'social_facebook'  => '',
                'social_instagram' => '',
                'social_linkedin'  => '',
                'sort_order'       => 6,
                'show'             => 1,
            ],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($data as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }

        $this->db->table('teachers')->insertBatch($data);
    }
}
