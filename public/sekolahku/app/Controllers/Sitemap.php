<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\NewsModel;
use App\Models\EventModel;
use App\Models\GalleryModel;
use CodeIgniter\HTTP\ResponseInterface;

class Sitemap extends BaseController
{
    /**
     * Generate the XML sitemap.
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $settingModel = new SettingModel();
        $siteName = $settingModel->get('site_name') ?? 'SekolahKu';

        $news      = (new NewsModel())->getForSitemap();
        $events    = (new EventModel())->getForSitemap();
        $galleries = (new GalleryModel())->getForSitemap();

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . esc(base_url()) . '</loc>' . "\n";
        $xml .= '    <priority>1.0</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . esc(base_url('news')) . '</loc>' . "\n";
        $xml .= '    <priority>0.8</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . esc(base_url('contact')) . '</loc>' . "\n";
        $xml .= '    <priority>0.6</priority>' . "\n";
        $xml .= '  </url>' . "\n";

        foreach ($news as $n) {
            $lastmod = $n['updated_at'] ?? $n['published_at'] ?? date('Y-m-d');
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc(base_url('news/' . $n['slug'])) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . esc($lastmod) . '</lastmod>' . "\n";
            $xml .= '    <priority>0.7</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        foreach ($events as $e) {
            $lastmod = $e['updated_at'] ?? $e['event_date'] ?? date('Y-m-d');
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc(base_url('#events')) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . esc($lastmod) . '</lastmod>' . "\n";
            $xml .= '    <priority>0.5</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        foreach ($galleries as $g) {
            $lastmod = $g['updated_at'] ?? date('Y-m-d');
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . esc(base_url('#gallery')) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . esc($lastmod) . '</lastmod>' . "\n";
            $xml .= '    <priority>0.4</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        $this->response->setContentType('application/xml');
        return $this->response->setBody($xml);
    }
}
