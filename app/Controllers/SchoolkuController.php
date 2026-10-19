<?php

namespace App\Controllers;

use App\Models\MasterExtracurricularModel;
use App\Models\SchoolContactSettingsModel;
use App\Models\SchoolWebsiteMessageModel;
use App\Models\SchoolWebsiteNewsModel;
use App\Models\SchoolkuWebsiteContentModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class SchoolkuController extends BaseController
{
    public function home(): string
    {
        $contentModel = new SchoolkuWebsiteContentModel();
        $teachers = db_connect()->table('master_guru')
            ->select('guru_nama')
            ->orderBy('guru_nama', 'ASC')
            ->get()
            ->getResultArray();
        $contentSections = SchoolWebsiteContentController::sectionData();
        $visibleItems = static function (array $items): array {
            $items = array_values(array_filter(
                $items,
                static fn(array $item): bool => (int) ($item['show'] ?? 1) === 1
            ));
            usort(
                $items,
                static fn(array $a, array $b): int => ((int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0))
                    ?: ((int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0))
            );

            return $items;
        };
        $settings = $contentModel->getSettings([
            'hero_badge' => 'Sekolah Unggulan - Kurikulum Merdeka',
            'hero_title' => 'Selamat Datang di SekolahKu',
            'hero_subtitle' => 'Membangun Generasi Cerdas, Berkarakter, dan Berprestasi menuju Masa Depan Gemilang',
            'hero_btn_primary_text' => 'Jelajahi Sekolah',
            'hero_btn_primary_url' => '#profile',
            'hero_btn_secondary_text' => 'Hubungi Kami',
            'hero_btn_secondary_url' => '/news/contact',
            'hero_stats' => '[]',
            'counter_stats' => '[]',
            'principal' => '{"smp":{"name":"Kepala Sekolah SMP","role":"Kepala Sekolah SMP","quote":"","photo":""},"smk":{"name":"Kepala Sekolah SMK","role":"Kepala Sekolah SMK","quote":"","photo":""}}',
            'about' => '{}',
            'section_settings' => '{}',
        ]);
        $decode = static function (string $value): array {
            $result = json_decode($value, true);

            return is_array($result) ? $result : [];
        };
        $principals = $decode($settings['principal']);
        if (! isset($principals['smp']) && ! isset($principals['smk'])) {
            $legacyPrincipal = $principals;
            if (($legacyPrincipal['role'] ?? $legacyPrincipal['role_title'] ?? '') === 'Kepala Sekolah') {
                $legacyPrincipal['role'] = 'Kepala Sekolah SMP';
            }
            $principals = [
                'smp' => $legacyPrincipal,
                'smk' => [
                    'name' => 'Kepala Sekolah SMK',
                    'role' => 'Kepala Sekolah SMK',
                    'quote' => '',
                    'photo' => '',
                    'meta' => [],
                ],
            ];
        }
        foreach (['smp', 'smk'] as $schoolLevel) {
            $principals[$schoolLevel] = array_merge([
                'name' => 'Kepala Sekolah ' . strtoupper($schoolLevel),
                'role' => 'Kepala Sekolah ' . strtoupper($schoolLevel),
                'quote' => '',
                'photo' => '',
                'meta' => [],
            ], is_array($principals[$schoolLevel] ?? null) ? $principals[$schoolLevel] : []);
        }

        return view('sekolahku/home', $this->pageData('Beranda - SekolahKu') + [
            'hero_badge'            => $settings['hero_badge'],
            'hero_title'            => $settings['hero_title'],
            'hero_subtitle'         => $settings['hero_subtitle'],
            'hero_btn_primary_text' => $settings['hero_btn_primary_text'],
            'hero_btn_primary_url'  => $settings['hero_btn_primary_url'],
            'hero_btn_secondary_text' => $settings['hero_btn_secondary_text'],
            'hero_btn_secondary_url' => $settings['hero_btn_secondary_url'],
            'hero_stats'            => $decode($settings['hero_stats']),
            'counter_stats'         => $decode($settings['counter_stats']),
            'principal'             => $principals,
            'about'                 => $decode($settings['about']),
            'section_settings'      => $decode($settings['section_settings']),
            'programs'              => $contentModel->getPrograms(true),
            'extracurriculars'      => $visibleItems($contentSections['extracurriculars']),
            'teachers'              => $teachers,
            'achievements'          => $visibleItems($contentSections['achievements']),
            'testimonials'          => $visibleItems($contentSections['testimonials']),
            'news'                  => (new SchoolWebsiteNewsModel())->getRecent(3),
            'events'                => $this->upcomingWebsiteEvents($visibleItems($contentSections['events'])),
            'galleries'             => $this->featuredWebsiteGallery($visibleItems($contentSections['gallery'])),
            'faq'                   => array_slice($visibleItems($contentSections['faq']), 0, 12),
        ]);
    }

    public function contact(): string
    {
        return view('sekolahku/contact', $this->pageData('Kontak - SekolahKu'));
    }

    public function downloads(): string
    {
        $items = array_values(array_filter(
            SchoolWebsiteContentController::sectionData()['downloads'],
            static fn(array $item): bool => (int) ($item['show'] ?? 1) === 1
        ));
        usort(
            $items,
            static fn(array $a, array $b): int => ((string) ($a['category'] ?? '') <=> (string) ($b['category'] ?? ''))
                ?: ((int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0))
        );
        $categories = [];
        foreach ($items as $item) {
            $categories[$item['category']][] = $item;
        }

        return view('sekolahku/downloads', $this->pageData('Download - SekolahKu') + [
            'categories' => $categories,
        ]);
    }

    public function downloadFile(int $id): ResponseInterface
    {
        $item = null;
        foreach (SchoolWebsiteContentController::sectionData()['downloads'] as $download) {
            if ((int) ($download['id'] ?? 0) === $id && (int) ($download['show'] ?? 1) === 1) {
                $item = $download;
                break;
            }
        }
        if ($item === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $relativePath = (string) $item['file_path'];
        if (preg_match('/^[a-f0-9]{32}\.pdf$/', $relativePath) !== 1) {
            log_message('error', 'Invalid school website download path for item {id}.', ['id' => $id]);
            throw PageNotFoundException::forPageNotFound();
        }
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'school-website'
            . DIRECTORY_SEPARATOR . 'downloads' . DIRECTORY_SEPARATOR . $relativePath;
        if (! is_file($path)) {
            log_message('error', 'School website download file is missing for item {id}.', ['id' => $id]);
            throw PageNotFoundException::forPageNotFound();
        }

        return $this->response->download($path, null)->setFileName(basename($path));
    }

    private function upcomingWebsiteEvents(array $events): array
    {
        $events = array_values(array_filter(
            $events,
            static fn(array $event): bool => ! empty($event['event_date']) && $event['event_date'] >= date('Y-m-d')
        ));
        usort(
            $events,
            static fn(array $a, array $b): int => ((string) $a['event_date'] <=> (string) $b['event_date'])
                ?: ((int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0))
        );

        return array_slice($events, 0, 6);
    }

    private function featuredWebsiteGallery(array $galleries): array
    {
        usort(
            $galleries,
            static fn(array $a, array $b): int => ((int) ($b['is_featured'] ?? 0) <=> (int) ($a['is_featured'] ?? 0))
                ?: ((int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0))
                ?: ((int) ($a['id'] ?? 0) <=> (int) ($b['id'] ?? 0))
        );

        return array_slice($galleries, 0, 12);
    }

    public function news(): string
    {
        $model = new SchoolWebsiteNewsModel();
        $search = trim((string) $this->request->getGet('search'));
        $category = trim((string) $this->request->getGet('category'));
        $month = trim((string) $this->request->getGet('month'));
        $news = $model->getPublished(
            $search !== '' ? $search : null,
            $category !== '' ? $category : null,
            $month !== '' ? $month : null
        );

        return view('sekolahku/news', $this->pageData('Berita - SekolahKu') + [
            'news'        => $news,
            'pager'       => $model->pager,
            'categories'  => $model->getCategories(),
            'recent_news' => $model->getRecent(),
            'tags'        => $model->getTags(),
            'archive'     => $model->getArchive(),
            'search'      => $search,
            'category'    => $category,
            'month'       => $month,
        ]);
    }

    public function searchNews(): ResponseInterface
    {
        $query = trim((string) $this->request->getGet('q'));

        return $this->response->setJSON(
            $query === '' ? [] : (new SchoolWebsiteNewsModel())->searchByQuery($query)
        );
    }

    public function newsDetail(string $slug): string
    {
        $model = new SchoolWebsiteNewsModel();
        $post = $model->getPublishedBySlug($slug);
        if ($post === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $model->incrementViews((int) $post['id']);
        $post['views'] = (int) ($post['view_count'] ?? 0) + 1;

        return view('sekolahku/single_post', $this->pageData($post['title'] . ' - SekolahKu') + [
            'post'        => $post,
            'comments'    => [],
            'related'     => $model->getRelated($post),
            'prev_post'   => null,
            'next_post'   => null,
            'recent_news' => $model->getRecent(),
            'categories'  => $model->getCategories(),
            'tags'        => $model->getTags(),
            'archive'     => $model->getArchive(),
            'search'      => '',
        ]);
    }

    public function page(string $slug): string
    {
        $title = $this->titleFromSlug($slug);
        $date = date('Y-m-d H:i:s');

        return view('sekolahku/page', $this->pageData($title . ' - SekolahKu') + [
            'page' => [
                'id'         => 0,
                'title'      => $title,
                'slug'       => $slug,
                'excerpt'    => '',
                'content'    => '<p>Konten halaman ini belum tersedia.</p>',
                'created_at' => $date,
                'updated_at' => $date,
            ],
            'all_pages'  => [],
            'categories' => [],
            'recent_news' => [],
            'archive'    => [],
            'tags'       => [],
        ]);
    }

    public function sendContactMessage(): ResponseInterface
    {
        $input = $this->request->getPost(['name', 'email', 'subject', 'message']);
        $website = $this->request->getPost('website');

        if (is_string($website) && trim($website) !== '') {
            return $this->response->setJSON(['success' => true]);
        }

        foreach ($input as $value) {
            if ($value !== null && ! is_string($value)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'errors' => ['form' => 'Data formulir tidak valid.'],
                ]);
            }
        }

        $rules = [
            'name' => 'required|max_length[120]',
            'email' => 'required|valid_email|max_length[254]',
            'subject' => 'permit_empty|max_length[200]',
            'message' => 'required|max_length[5000]',
        ];
        $messages = [
            'name' => [
                'required' => 'Nama wajib diisi.',
                'max_length' => 'Nama maksimal 120 karakter.',
            ],
            'email' => [
                'required' => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
                'max_length' => 'Email maksimal 254 karakter.',
            ],
            'subject' => [
                'max_length' => 'Subjek maksimal 200 karakter.',
            ],
            'message' => [
                'required' => 'Pesan wajib diisi.',
                'max_length' => 'Pesan maksimal 5000 karakter.',
            ],
        ];

        if (! $this->validateData($input, $rules, $messages)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $message = [
            'name' => trim($input['name']),
            'email' => trim($input['email']),
            'subject' => trim($input['subject'] ?? '') ?: null,
            'message' => trim($input['message']),
            'is_read' => 0,
        ];

        try {
            if (! (new SchoolWebsiteMessageModel())->insert($message)) {
                throw new DatabaseException('Inserting a school website message returned false.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Unable to save a school website message: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'errors' => ['form' => 'Pesan gagal disimpan. Silakan coba kembali.'],
            ]);
        }

        return $this->response
            ->setJSON([
                'success' => true,
            ]);
    }

    public function sitemap(): ResponseInterface
    {
        $news = (new SchoolWebsiteNewsModel())->where('status', 'published')
            ->select('slug, updated_at, published_at')
            ->findAll();

        return $this->response
            ->setContentType('application/xml')
            ->setBody(view('sekolahku/sitemap', ['news' => $news, 'events' => [], 'galleries' => []]));
    }

    private function pageData(string $pageTitle): array
    {
        $contactSettings = (new SchoolContactSettingsModel())->getSettings();

        return [
            'page_title'          => $pageTitle,
            'site_name'           => 'MIFTAHUL HUDA',
            'site_tagline'        => 'Membangun generasi cerdas dan berkarakter',
            'site_description'    => 'Informasi resmi SekolahKu.',
            'site_logo_text'      => 'MIFTAHUL HUDA',
            'site_logo_icon'      => 'fas fa-graduation-cap',
            'contact_phone'       => $contactSettings['phone'],
            'contact_email'       => $contactSettings['email'],
            'contact_address'     => $contactSettings['address'],
            'contact_hours'       => $contactSettings['hours'],
            'social_facebook'     => '',
            'social_instagram'    => '',
            'social_youtube'      => '',
            'social_tiktok'       => '',
            'footer_description'  => 'Membangun generasi cerdas, berkarakter, dan berprestasi.',
            'footer_copyright'    => 'All rights reserved.',
            'footer_services'     => (new MasterExtracurricularModel())->getActiveActivities(),
            'footer_links'        => [],
            'spmb_url'            => site_url('news/spmb'),
            'theme_color'         => 'default',
            'section_settings'    => [],
            'page_banners'        => null,
        ];
    }

    private function titleFromSlug(string $slug): string
    {
        $words = preg_replace('/[-_]+/', ' ', $slug) ?? '';
        $words = trim(strip_tags($words));

        return $words !== '' ? mb_convert_case($words, MB_CASE_TITLE, 'UTF-8') : 'Informasi Sekolah';
    }
}
