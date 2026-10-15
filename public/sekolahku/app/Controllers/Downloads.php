<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\DownloadModel;

class Downloads extends BaseController
{
    /**
     * Display the download center page with categorized files.
     *
     * @return string
     */
    public function index()
    {
        $this->cachePage(5);

        $settingModel = new SettingModel();
        $settings = $settingModel->getMany([
            'site_name', 'site_tagline', 'site_description',
            'site_logo_text', 'site_logo_icon',
            'contact_phone', 'contact_email', 'contact_address', 'contact_hours',
            'social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok',
            'footer_description', 'footer_copyright', 'footer_services', 'footer_links',
            'theme_color',
            'page_banners',
        ]);

        $items = (new DownloadModel())->getVisible();

        $categories = [];
        foreach ($items as $item) {
            $categories[$item['category']][] = $item;
        }

        $data = [
            'page_title'        => 'Download Center - ' . ($settings['site_name'] ?? ''),
            'site_name'          => $settings['site_name'] ?? 'SekolahKu',
            'site_tagline'       => $settings['site_tagline'] ?? '',
            'site_description'   => $settings['site_description'] ?? '',
            'site_logo_text'     => $settings['site_logo_text'] ?? 'SekolahKu',
            'site_logo_icon'     => $settings['site_logo_icon'] ?? 'graduation-cap',
            'contact_phone'      => $settings['contact_phone'] ?? '',
            'contact_email'      => $settings['contact_email'] ?? '',
            'contact_address'    => $settings['contact_address'] ?? '',
            'contact_hours'      => $settings['contact_hours'] ?? '',
            'social_facebook'    => $settings['social_facebook'] ?? '',
            'social_instagram'   => $settings['social_instagram'] ?? '',
            'social_youtube'     => $settings['social_youtube'] ?? '',
            'social_tiktok'      => $settings['social_tiktok'] ?? '',
            'footer_description' => $settings['footer_description'] ?? '',
            'footer_copyright'   => $settings['footer_copyright'] ?? '',
            'footer_services'    => $settings['footer_services'] ?? '',
            'footer_links'       => $settings['footer_links'] ?? '',
            'theme_color'        => $settings['theme_color'] ?? 'blue',
            'page_banners'       => $settings['page_banners'] ?? null,
            'categories'         => $categories,
        ];

        return view('pages/downloads', $data);
    }
}
