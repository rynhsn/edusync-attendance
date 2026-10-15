<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('settings')->truncate();
        $settings = [
            // Site identity
            ['site_name', 'SekolahKu'],
            ['site_tagline', 'Membangun Generasi Cerdas, Berkarakter, dan Berprestasi'],
            ['site_description', 'Sekolah unggulan yang berkomitmen mencetak generasi penerus bangsa yang berkualitas'],
            ['site_logo_text', 'SekolahKu'],
            ['site_logo_icon', 'graduation-cap'],
            ['site_url', 'https://sekolahku.sch.id'],

            // Contact info
            ['contact_phone', '(021) 1234-5678'],
            ['contact_email', 'info@sekolahku.sch.id'],
            ['contact_address', 'Jl. Pendidikan No. 123, Kelurahan Cerdas, Kecamatan Unggul, Kota Pelajar 12345'],
            ['contact_hours', 'Senin - Jumat: 07.00 - 16.00 WIB'],

            // Social media
            ['social_facebook', '#'],
            ['social_instagram', '#'],
            ['social_youtube', '#'],
            ['social_tiktok', '#'],

            // SEO / Meta
            ['meta_author', 'SekolahKu'],
            ['meta_keywords', 'sekolah, pendidikan, kurikulum merdeka, sekolah unggulan, SPMB'],
            ['meta_description', 'SekolahKu adalah institusi pendidikan unggulan yang berkomitmen mencetak generasi cerdas, berkarakter, dan berprestasi dengan kurikulum Merdeka.'],

            // Hero section
            ['hero_badge', 'Terakreditasi A · Kurikulum Merdeka'],
            ['hero_title', 'Selamat Datang di SekolahKu'],
            ['hero_subtitle', 'Membangun Generasi Cerdas, Berkarakter, dan Berprestasi menuju Masa Depan Gemilang'],
            ['hero_btn_primary_text', 'Jelajahi Sekolah'],
            ['hero_btn_primary_url', '#profile'],
            ['hero_btn_secondary_text', 'Hubungi Kami'],
            ['hero_btn_secondary_url', '#contact'],
            ['spmb_url', '#'],

            // Hero stats (JSON)
            ['hero_stats', json_encode([
                ['label' => 'Siswa', 'value' => '1200+', 'icon' => 'user-graduate'],
                ['label' => 'Guru', 'value' => '85+', 'icon' => 'chalkboard-teacher'],
                ['label' => 'Prestasi', 'value' => '150+', 'icon' => 'trophy'],
            ])],

            // Principal (JSON)
            ['principal', json_encode([
                'name' => 'Dr. Sari Wijaya, M.Pd.',
                'photo' => '/images/default-principal.jpg',
                'role_title' => 'Kepala Sekolah',
                'welcome_message' => 'Selamat datang di SekolahKu. Kami berkomitmen mencetak generasi cerdas, berkarakter, dan berprestasi. Mari bersama-sama membangun masa depan gemilang untuk putra-putri kita.',
                'education' => 'S3 Pendidikan',
                'years_of_service' => '10 Thn Mengabdi',
            ])],

            // About (JSON)
            ['about', json_encode([
                'image' => 'https://placehold.co/600x400/0c4a6e/ffffff?text=SekolahKu',
                'content_title' => 'Mewujudkan Pendidikan Berkualitas untuk Masa Depan',
                'content_1' => 'SekolahKu adalah institusi pendidikan yang berdedikasi memberikan pengalaman belajar terbaik. Dengan kurikulum terkini dan tenaga pengajar profesional, kami siap membentuk karakter dan kompetensi siswa.',
                'content_2' => 'Kami percaya setiap anak memiliki potensi unik. Melalui pendekatan holistik, kami mendorong siswa tumbuh secara akademis, sosial, dan spiritual.',
                'accreditation' => 'A',
                'accreditation_label' => 'Standar Nasional',
                'highlights' => ['Akreditasi A', 'Kurikulum Merdeka', 'Lulusan 100% Terserap', 'Lab & Perpustakaan Digital'],
            ])],

            // Section settings (JSON)
            ['section_settings', json_encode([
                'hero' => ['title' => 'Selamat Datang di SekolahKu', 'subtitle' => 'Mewujudkan Generasi Cerdas, Berkarakter, dan Berdaya Saing Global', 'icon' => 'fa-school', 'show' => true],
                'profile' => ['title' => 'Profil Sekolah', 'subtitle' => 'Mengenal lebih dekat visi, misi, dan sejarah SekolahKu', 'icon' => 'fa-building-columns', 'show' => true],
                'programs' => ['title' => 'Program Unggulan', 'subtitle' => 'Program unggulan yang mendukung pengembangan bakat dan prestasi', 'icon' => 'fa-gem', 'show' => true],
                'extracurriculars' => ['title' => 'Ekstrakurikuler', 'subtitle' => 'Wadah pengembangan minat dan bakat di luar kelas', 'icon' => 'fa-futbol', 'show' => true],
                'teachers' => ['title' => 'Tenaga Pengajar', 'subtitle' => 'Guru profesional dan berpengalaman di bidangnya', 'icon' => 'fa-chalkboard-user', 'show' => true],
                'achievements' => ['title' => 'Prestasi Siswa', 'subtitle' => 'Capaian membanggakan yang telah diraih siswa/i SekolahKu', 'icon' => 'fa-trophy', 'show' => true],
                'testimonials' => ['title' => 'Testimoni', 'subtitle' => 'Apa kata mereka tentang SekolahKu?', 'icon' => 'fa-comment', 'show' => true],
                'news' => ['title' => 'Berita & Artikel', 'subtitle' => 'Informasi terkini seputar SekolahKu', 'icon' => 'fa-newspaper', 'show' => true],
                'events' => ['title' => 'Agenda & Kegiatan', 'subtitle' => 'Jadwal kegiatan dan acara mendatang', 'icon' => 'fa-calendar-days', 'show' => true],
                'gallery' => ['title' => 'Galeri', 'subtitle' => 'Dokumentasi kegiatan dan momen berharga', 'icon' => 'fa-images', 'show' => true],
                'faq' => ['title' => 'FAQ', 'subtitle' => 'Pertanyaan yang sering diajukan', 'icon' => 'fa-circle-question', 'show' => true],
                'downloads' => ['title' => 'Download Center', 'subtitle' => 'Unduh berkas-berkas penting seputar akademik, kurikulum, dan administrasi sekolah', 'icon' => 'fa-download', 'show' => true],
                'contact' => ['title' => 'Kontak Kami', 'subtitle' => 'Hubungi kami untuk informasi lebih lanjut', 'icon' => 'fa-address-card', 'show' => true],
            ])],

            // Page banners (JSON)
            ['page_banners', json_encode([
                'news' => ['badge' => 'Berita & Artikel', 'title' => 'Baca Berita Terbaru', 'subtitle' => 'Informasi terkini seputar kegiatan dan prestasi di lingkungan SekolahKu'],
                'single_post' => ['badge' => 'Detail Berita', 'title' => 'Baca Berita', 'subtitle' => 'Informasi lengkap seputar kegiatan dan prestasi di lingkungan SekolahKu'],
                'pages' => ['badge' => 'Halaman', 'title' => 'Baca Halaman', 'subtitle' => 'Informasi lengkap seputar halaman ini'],
                'downloads' => ['badge' => 'Download Center', 'title' => 'Pusat Unduhan', 'subtitle' => 'Unduh berkas-berkas penting seputar akademik, kurikulum, dan administrasi sekolah'],
            ])],

            // Footer
            ['footer_description', 'SekolahKu berkomitmen mencetak generasi penerus bangsa yang cerdas, berkarakter, dan berprestasi.'],
            ['footer_copyright', '© 2026 SekolahKu — All rights reserved.'],
            ['footer_services', json_encode([
                ['label' => 'Sains & Teknologi', 'url' => '#programs'],
                ['label' => 'Seni & Budaya', 'url' => '#programs'],
                ['label' => 'Olahraga', 'url' => '#programs'],
                ['label' => 'Bahasa Asing', 'url' => '#programs'],
                ['label' => 'Digital Literacy', 'url' => '#programs'],
            ])],
            ['footer_links', json_encode([
                ['label' => 'SPMB Online', 'url' => '#'],
                ['label' => 'E-Learning', 'url' => '#'],
                ['label' => 'Perpustakaan', 'url' => '#'],
                ['label' => 'FAQ', 'url' => '#faq'],
                ['label' => 'Pengaduan', 'url' => '#'],
            ])],

            // Theme
            ['theme_color', 'default'],

            // Counter stats (JSON)
            ['counter_stats', json_encode([
                ['icon' => 'user-graduate', 'number' => '1200', 'suffix' => '+', 'label' => 'Siswa'],
                ['icon' => 'chalkboard-teacher', 'number' => '85', 'suffix' => '+', 'label' => 'Guru'],
                ['icon' => 'trophy', 'number' => '150', 'suffix' => '+', 'label' => 'Prestasi'],
                ['icon' => 'book', 'number' => '50', 'suffix' => '+', 'label' => 'Program'],
            ])],
        ];

        $this->db->table('settings')->insertBatch(
            array_map(fn($s) => ['key' => $s[0], 'value' => $s[1]], $settings)
        );
    }
}
