<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\ProgramModel;
use App\Models\ExtracurricularModel;
use App\Models\TeacherModel;
use App\Models\AchievementModel;
use App\Models\TestimonialModel;
use App\Models\NewsModel;
use App\Models\EventModel;
use App\Models\GalleryModel;
use App\Models\FaqModel;

class Home extends BaseController
{
    /**
     * Display the homepage with site settings and featured content.
     *
     * @return string
     */
    public function index()
    {
        $settingModel = new SettingModel();

        $settings = $settingModel->getMany([
            'site_name', 'site_tagline', 'site_description',
            'site_logo_text', 'site_logo_icon',
            'contact_phone', 'contact_email', 'contact_address', 'contact_hours',
            'social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok',
            'footer_description', 'footer_copyright', 'footer_services', 'footer_links',
            'hero_badge', 'hero_title', 'hero_subtitle',
            'hero_btn_primary_text', 'hero_btn_primary_url',
            'hero_btn_secondary_text', 'hero_btn_secondary_url',
            'spmb_url',
            'theme_color',
            'counter_stats', 'hero_stats', 'principal', 'about', 'section_settings', 'page_banners',
        ]);

        helper('menu');
        $cache = service('cache');
        $menuTree = $cache->get('menu_tree');
        if (!$menuTree) {
            $menuTree = get_menu_tree($settings['section_settings'] ?? null);
            $cache->save('menu_tree', $menuTree, 300);
        }

        $data = [
            'page_title'         => $settings['site_name'] ?? 'SekolahKu',
            'site_name'           => $settings['site_name'] ?? 'SekolahKu',
            'site_tagline'        => $settings['site_tagline'] ?? '',
            'site_description'    => $settings['site_description'] ?? '',
            'site_logo_text'      => $settings['site_logo_text'] ?? 'SekolahKu',
            'site_logo_icon'      => $settings['site_logo_icon'] ?? 'graduation-cap',
            'contact_phone'       => $settings['contact_phone'] ?? '',
            'contact_email'       => $settings['contact_email'] ?? '',
            'contact_address'     => $settings['contact_address'] ?? '',
            'contact_hours'       => $settings['contact_hours'] ?? '',
            'social_facebook'     => $settings['social_facebook'] ?? '',
            'social_instagram'    => $settings['social_instagram'] ?? '',
            'social_youtube'      => $settings['social_youtube'] ?? '',
            'social_tiktok'       => $settings['social_tiktok'] ?? '',
            'footer_description'  => $settings['footer_description'] ?? '',
            'footer_copyright'    => $settings['footer_copyright'] ?? '',
            'footer_services'     => json_decode($settings['footer_services'] ?? '[]', true) ?: [],
            'footer_links'        => json_decode($settings['footer_links'] ?? '[]', true) ?: [],
            'hero_badge'          => $settings['hero_badge'] ?? '',
            'hero_title'          => $settings['hero_title'] ?? '',
            'hero_subtitle'       => $settings['hero_subtitle'] ?? '',
            'hero_btn_primary_text'  => $settings['hero_btn_primary_text'] ?? '',
            'hero_btn_primary_url'   => $settings['hero_btn_primary_url'] ?? '',
            'hero_btn_secondary_text' => $settings['hero_btn_secondary_text'] ?? '',
            'hero_btn_secondary_url'  => $settings['hero_btn_secondary_url'] ?? '',
            'spmb_url'            => $settings['spmb_url'] ?? '#',
            'theme_color'         => $settings['theme_color'] ?? 'blue',
            'section_settings'    => json_decode($settings['section_settings'] ?? '[]', true) ?: [],
            'page_banners'        => $settings['page_banners'] ?? null,
            'menu_tree'           => $menuTree,
            'counter_stats'       => json_decode($settings['counter_stats'] ?? '[]', true) ?: [],
            'hero_stats'          => json_decode($settings['hero_stats'] ?? '[]', true) ?: [],
            'principal'           => json_decode($settings['principal'] ?? '{}', true) ?: [],
            'about'               => json_decode($settings['about'] ?? '{}', true) ?: [],
            'programs'            => (new ProgramModel())->getOrdered(50),
            'extracurriculars'    => (new ExtracurricularModel())->getOrdered(50),
            'teachers'            => (new TeacherModel())->getOrdered(50),
            'achievements'        => (new AchievementModel())->getOrdered(50),
            'testimonials'        => (new TestimonialModel())->getOrdered(50),
            'news'                => (new NewsModel())->getFeatured(3),
            'events'              => (new EventModel())->getUpcoming(4),
            'galleries'           => (new GalleryModel())->getVisible(50),
            'faq'                 => (new FaqModel())->getVisible(50),
        ];

        return view('pages/home', $data);
    }
}
