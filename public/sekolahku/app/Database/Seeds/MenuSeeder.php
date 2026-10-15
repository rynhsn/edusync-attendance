<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->db->table('menu_items')->truncate();
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        $this->db->table('menu_items')->insertBatch([
            [
                'parent_id'   => null,
                'title'       => 'Beranda',
                'url'         => '',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => null,
                'sort_order'  => 0,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => null,
                'title'       => 'Profil',
                'url'         => '#profile',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'profile',
                'sort_order'  => 1,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => null,
                'title'       => 'Program',
                'url'         => '#programs',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'programs',
                'sort_order'  => 2,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => null,
                'title'       => 'Berita',
                'url'         => 'news',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => null,
                'sort_order'  => 3,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => null,
                'title'       => 'Kontak',
                'url'         => '#contact',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'contact',
                'sort_order'  => 4,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => null,
                'title'       => 'Lainnya',
                'url'         => '#',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => null,
                'sort_order'  => 5,
                'status'      => 1,
                'type'        => 'system',
            ],
        ]);

        $this->db->table('menu_items')->insertBatch([
            [
                'parent_id'   => 6,
                'title'       => 'Pengajar',
                'url'         => '#teachers',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'teachers',
                'sort_order'  => 0,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'Prestasi',
                'url'         => '#achievements',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'achievements',
                'sort_order'  => 1,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'Ekstrakurikuler',
                'url'         => '#extracurriculars',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'extracurriculars',
                'sort_order'  => 2,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'Testimoni',
                'url'         => '#testimonials',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'testimonials',
                'sort_order'  => 3,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'Galeri',
                'url'         => '#gallery',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'gallery',
                'sort_order'  => 4,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'Agenda',
                'url'         => '#events',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'events',
                'sort_order'  => 5,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'FAQ',
                'url'         => '#faq',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => 'faq',
                'sort_order'  => 6,
                'status'      => 1,
                'type'        => 'system',
            ],
            [
                'parent_id'   => 6,
                'title'       => 'Download',
                'url'         => 'downloads',
                'target'      => '_self',
                'icon'        => null,
                'section_key' => null,
                'sort_order'  => 7,
                'status'      => 1,
                'type'        => 'system',
            ],
        ]);
    }
}
