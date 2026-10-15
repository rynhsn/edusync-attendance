<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\PageModel;
use App\Models\NewsModel;

class Pages extends BaseController
{
    /**
     * Display a published static page by its slug.
     *
     * @param string $slug The page slug.
     *
     * @return string
     */
    public function view($slug)
    {
        $pageModel = new PageModel();
        $page = $pageModel->getBySlug($slug);

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $settingModel = new SettingModel();

        $settings = $settingModel->getMany([
            'site_name', 'site_tagline', 'site_description',
            'site_logo_text', 'site_logo_icon',
            'contact_phone', 'contact_email', 'contact_address', 'contact_hours',
            'social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok',
            'footer_description', 'footer_copyright', 'footer_services', 'footer_links',
            'theme_color',
            'spmb_url',
            'page_banners',
        ]);

        $newsModel = new NewsModel();

        $data = [
            'page_title'       => $page['meta_title'] ?: $page['title'],
            'site_name'         => $settings['site_name'] ?? 'SekolahKu',
            'site_tagline'      => $settings['site_tagline'] ?? '',
            'site_description'  => $settings['site_description'] ?? '',
            'site_logo_text'    => $settings['site_logo_text'] ?? 'SekolahKu',
            'site_logo_icon'    => $settings['site_logo_icon'] ?? 'graduation-cap',
            'contact_phone'     => $settings['contact_phone'] ?? '',
            'contact_email'     => $settings['contact_email'] ?? '',
            'contact_address'   => $settings['contact_address'] ?? '',
            'contact_hours'     => $settings['contact_hours'] ?? '',
            'social_facebook'   => $settings['social_facebook'] ?? '',
            'social_instagram'  => $settings['social_instagram'] ?? '',
            'social_youtube'    => $settings['social_youtube'] ?? '',
            'social_tiktok'     => $settings['social_tiktok'] ?? '',
            'footer_description' => $settings['footer_description'] ?? '',
            'footer_copyright'  => $settings['footer_copyright'] ?? '',
            'footer_services'   => $settings['footer_services'] ?? '',
            'footer_links'      => $settings['footer_links'] ?? '',
            'theme_color'       => $settings['theme_color'] ?? 'blue',
            'spmb_url'          => $settings['spmb_url'] ?? '#',
            'page_banners'      => $settings['page_banners'] ?? null,
            'page'              => $page,
            'all_pages'         => $pageModel->getPublishedAll(),
            'categories'        => $newsModel->getPublishedCategories(),
            'recent_news'       => $newsModel->getRecent(5),
            'archive'           => $newsModel->getArchive(),
            'tags'              => $newsModel->getAllTags(),
        ];

        return view('pages/page', $data);
    }
}
