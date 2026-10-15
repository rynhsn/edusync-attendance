<?php

namespace App\Controllers;

use App\Models\SettingModel;
use App\Models\NewsModel;
use App\Models\NewsCommentModel;

class News extends BaseController
{
    /**
     * Display the news listing page.
     *
     * @return string
     */
    public function index()
    {
        $this->cachePage(1);

        $settingModel = new SettingModel();
        $newsModel    = new NewsModel();

        $settings = $settingModel->getMany([
            'site_name', 'site_tagline', 'site_description',
            'contact_phone', 'contact_email', 'contact_address', 'contact_hours',
            'social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok',
            'site_logo_text', 'site_logo_icon',
            'footer_description', 'footer_copyright', 'footer_services', 'footer_links',
            'theme_color',
            'page_banners',
        ]);

        $search   = $this->request->getGet('search');
        $category = $this->request->getGet('category');
        $month    = $this->request->getGet('month');

        service('pager')->only(['search', 'category', 'month']);

        if ($search) {
            $news = $newsModel->search($search);
        } else {
            $news = $newsModel->getFiltered($category, $month, 6);
        }

        $data = [
            'site_name'        => $settings['site_name'] ?? 'SekolahKu',
            'site_tagline'     => $settings['site_tagline'] ?? '',
            'site_description' => $settings['site_description'] ?? '',
            'site_logo_text'   => $settings['site_logo_text'] ?? 'SekolahKu',
            'site_logo_icon'   => $settings['site_logo_icon'] ?? 'graduation-cap',
            'contact_phone'    => $settings['contact_phone'] ?? '',
            'contact_email'    => $settings['contact_email'] ?? '',
            'contact_address'  => $settings['contact_address'] ?? '',
            'contact_hours'    => $settings['contact_hours'] ?? '',
            'social_facebook'  => $settings['social_facebook'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_youtube'   => $settings['social_youtube'] ?? '',
            'social_tiktok'    => $settings['social_tiktok'] ?? '',
            'footer_description' => $settings['footer_description'] ?? '',
            'footer_copyright'   => $settings['footer_copyright'] ?? '',
            'footer_services'    => $settings['footer_services'] ?? '',
            'footer_links'       => $settings['footer_links'] ?? '',
            'theme_color'       => $settings['theme_color'] ?? 'blue',
            'page_banners'      => $settings['page_banners'] ?? null,
            'page_title'       => 'Berita - ' . ($settings['site_name'] ?? ''),
            'news'             => $news,
            'pager'            => $newsModel->pager,
            'categories'       => $newsModel->getPublishedCategories(),
            'recent_news'      => $newsModel->getRecent(5),
            'tags'             => $newsModel->getAllTags(),
            'archive'          => $newsModel->getArchive(),
            'search'           => $search,
            'category'         => $category,
            'month'            => $month,
        ];

        return view('pages/news', $data);
    }

    /**
     * Display a single news article.
     *
     * @param string $slug
     *
     * @return string|\CodeIgniter\HTTP\RedirectResponse
     */
    public function detail($slug)
    {
        $this->cachePage(5);

        $settingModel = new SettingModel();
        $newsModel    = new NewsModel();
        $commentModel = new NewsCommentModel();

        $settings = $settingModel->getMany([
            'site_name', 'site_tagline', 'site_description',
            'contact_phone', 'contact_email', 'contact_address', 'contact_hours',
            'social_facebook', 'social_instagram', 'social_youtube', 'social_tiktok',
            'site_logo_text', 'site_logo_icon',
            'footer_description', 'footer_copyright', 'footer_services', 'footer_links',
            'theme_color',
            'page_banners',
        ]);

        $post = $newsModel->getBySlug($slug);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $newsModel->incrementViews($post['id']);

        $comments = $commentModel->getByNews($post['id']);
        $related  = $newsModel->getRelated($post);

        $prevPost = $newsModel->getPrevPost($post['id'], $post['published_at']);
        $nextPost = $newsModel->getNextPost($post['id'], $post['published_at']);

        $data = [
            'site_name'        => $settings['site_name'] ?? 'SekolahKu',
            'site_tagline'     => $settings['site_tagline'] ?? '',
            'site_description' => $settings['site_description'] ?? '',
            'site_logo_text'   => $settings['site_logo_text'] ?? 'SekolahKu',
            'site_logo_icon'   => $settings['site_logo_icon'] ?? 'graduation-cap',
            'contact_phone'    => $settings['contact_phone'] ?? '',
            'contact_email'    => $settings['contact_email'] ?? '',
            'contact_address'  => $settings['contact_address'] ?? '',
            'contact_hours'    => $settings['contact_hours'] ?? '',
            'social_facebook'  => $settings['social_facebook'] ?? '',
            'social_instagram' => $settings['social_instagram'] ?? '',
            'social_youtube'   => $settings['social_youtube'] ?? '',
            'social_tiktok'    => $settings['social_tiktok'] ?? '',
            'footer_description' => $settings['footer_description'] ?? '',
            'footer_copyright'   => $settings['footer_copyright'] ?? '',
            'footer_services'    => $settings['footer_services'] ?? '',
            'footer_links'       => $settings['footer_links'] ?? '',
            'theme_color'       => $settings['theme_color'] ?? 'blue',
            'page_banners'      => $settings['page_banners'] ?? null,
            'page_title'       => $post['title'] . ' - ' . ($settings['site_name'] ?? ''),
            'post'             => $post,
            'comments'         => $comments,
            'related'          => $related,
            'prev_post'        => $prevPost,
            'next_post'        => $nextPost,
            'recent_news'      => $newsModel->getRecent(5),
            'tags'             => $newsModel->getAllTags(),
            'archive'          => $newsModel->getArchive(),
            'search'           => '',
        ];

        return view('pages/single_post', $data);
    }

    /**
     * Search news articles via AJAX.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function search()
    {
        $query = $this->request->getGet('q');

        if (!$query) {
            return $this->response->setJSON([]);
        }

        $results = (new NewsModel())->searchByQuery($query);

        return $this->response->setJSON($results);
    }

    /**
     * Submit a comment on a news article.
     *
     * @param string $slug
     *
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function comment($slug)
    {
        if (!empty($this->request->getPost('website'))) {
            return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu persetujuan.');
        }

        $last = session()->get('comment_last_sent');
        if ($last && (time() - $last) < 30) {
            $wait = 30 - (time() - $last);
            return redirect()->back()->with('error', 'Tunggu ' . $wait . ' detik sebelum mengirim komentar lagi.');
        }

        $newsModel = new NewsModel();
        $post = $newsModel->getBySlugAnyStatus($slug);

        if (!$post) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'name'    => 'required|min_length[3]|max_length[100]',
            'email'   => 'required|valid_email',
            'message' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $commentModel = new NewsCommentModel();
        $commentModel->insert([
            'news_id'     => $post['id'],
            'parent_id'   => $this->request->getPost('parent_id') ?: null,
            'name'        => strip_tags($this->request->getPost('name')),
            'email'       => strip_tags($this->request->getPost('email')),
            'comment'     => strip_tags($this->request->getPost('message')),
            'is_approved' => 0,
        ]);

        session()->set('comment_last_sent', time());

        return redirect()->back()->with('success', 'Komentar berhasil dikirim dan menunggu persetujuan.');
    }
}
