<?php

namespace App\Controllers;

use App\Models\SchoolWebsiteNewsModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\RedirectResponse;

class SchoolWebsiteNewsController extends BaseController
{
    public function index()
    {
        return view('panel/website-news', [
            'title' => 'Kelola News',
            'activePage' => 'website-news',
            'news' => (new SchoolWebsiteNewsModel())->getLatest(),
            'message' => session()->getFlashdata('newsMessage'),
            'error' => session()->getFlashdata('newsError'),
        ]);
    }

    public function create()
    {
        return view('panel/website-news-form', [
            'title' => 'Tambah Berita',
            'activePage' => 'website-news',
            'article' => $this->emptyArticle(),
            'error' => session()->getFlashdata('newsError'),
        ]);
    }

    public function edit(int $id)
    {
        $article = (new SchoolWebsiteNewsModel())->find($id);
        if ($article === null) {
            return redirect()->to(site_url('website-news'))
                ->with('newsError', 'Berita tidak ditemukan.');
        }

        $tags = json_decode((string) ($article['tags'] ?? '[]'), true);
        $article['tags_input'] = is_array($tags) ? implode(', ', $tags) : '';
        $article['published_at_input'] = empty($article['published_at'])
            ? ''
            : date('Y-m-d\TH:i', strtotime($article['published_at']));

        return view('panel/website-news-form', [
            'title' => 'Edit Berita',
            'activePage' => 'website-news',
            'article' => $article,
            'error' => session()->getFlashdata('newsError'),
        ]);
    }

    public function save(): RedirectResponse
    {
        $id = (int) $this->request->getPost('id');
        $id = $id > 0 ? $id : null;
        $rules = [
            'title' => 'required|max_length[200]',
            'slug' => 'permit_empty|max_length[220]|regex_match[/^[a-z0-9]+(?:-[a-z0-9]+)*$/]',
            'category' => 'permit_empty|max_length[150]',
            'tags' => 'permit_empty|max_length[2000]',
            'excerpt' => 'permit_empty|max_length[1000]',
            'content' => 'required|max_length[50000]',
            'image' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[draft,published]',
            'author' => 'permit_empty|max_length[100]',
            'published_at' => 'permit_empty',
        ];
        if (! $this->validate($rules)) {
            return $this->formRedirect($id, 'Periksa kembali kolom berita.');
        }

        $model = new SchoolWebsiteNewsModel();
        if ($id !== null && $model->find($id) === null) {
            return redirect()->to(site_url('website-news'))
                ->with('newsError', 'Berita tidak ditemukan.');
        }

        $title = trim((string) $this->request->getPost('title'));
        $slugInput = trim((string) $this->request->getPost('slug'));
        $slug = $slugInput !== '' ? $slugInput : $this->slugFromTitle($title);
        if ($slug === '' || $model->slugExists($slug, $id)) {
            return $this->formRedirect($id, 'Slug berita tidak valid atau sudah digunakan.');
        }

        $image = trim((string) $this->request->getPost('image'));
        if ($image !== '' && ! $this->isSafeImageSource($image)) {
            return $this->formRedirect($id, 'Gambar harus menggunakan URL http/https atau path lokal.');
        }

        $publishedAtInput = trim((string) $this->request->getPost('published_at'));
        $publishedAt = null;
        if ($publishedAtInput !== '') {
            $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $publishedAtInput);
            if ($date === false || $date->format('Y-m-d\TH:i') !== $publishedAtInput) {
                return $this->formRedirect($id, 'Tanggal publikasi tidak valid.');
            }
            $publishedAt = $date->format('Y-m-d H:i:s');
        } elseif ($this->request->getPost('status') === 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }

        $tags = array_values(array_unique(array_filter(array_map(
            static fn (string $tag): string => trim($tag),
            explode(',', (string) $this->request->getPost('tags'))
        ))));
        $authUser = session()->get('authUser');
        $author = trim((string) $this->request->getPost('author'));
        if ($author === '' && is_array($authUser)) {
            $author = trim((string) ($authUser['display_name'] ?? $authUser['username'] ?? ''));
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'category' => trim((string) $this->request->getPost('category')),
            'tags' => json_encode($tags, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'excerpt' => trim((string) $this->request->getPost('excerpt')),
            'content' => trim((string) $this->request->getPost('content')),
            'image' => $image,
            'status' => (string) $this->request->getPost('status'),
            'author' => $author,
            'published_at' => $publishedAt,
            'featured' => $this->request->getPost('featured') === '1' ? 1 : 0,
        ];

        try {
            $saved = $id === null
                ? $model->insert($data)
                : $model->update($id, $data);
        } catch (DatabaseException $exception) {
            log_message('error', 'Unable to save school website news: {message}', ['message' => $exception->getMessage()]);

            return $this->formRedirect($id, 'Berita gagal disimpan. Periksa kembali slug dan coba lagi.');
        }

        if (! $saved) {
            log_message('error', 'School website news validation or database save failed: {errors}', ['errors' => json_encode($model->errors())]);

            return $this->formRedirect($id, 'Berita gagal disimpan. Periksa kembali data yang dimasukkan.');
        }

        return redirect()->to(site_url('website-news'))
            ->with('newsMessage', 'Berita berhasil disimpan.');
    }

    public function delete(int $id): RedirectResponse
    {
        $model = new SchoolWebsiteNewsModel();
        if ($model->find($id) === null) {
            return redirect()->to(site_url('website-news'))
                ->with('newsError', 'Berita tidak ditemukan.');
        }

        try {
            if (! $model->delete($id)) {
                throw new DatabaseException('Could not delete the school website news item.');
            }
        } catch (DatabaseException $exception) {
            log_message('error', 'Unable to delete school website news: {message}', ['message' => $exception->getMessage()]);

            return redirect()->to(site_url('website-news'))
                ->with('newsError', 'Berita gagal dihapus. Silakan coba kembali.');
        }

        return redirect()->to(site_url('website-news'))
            ->with('newsMessage', 'Berita berhasil dihapus.');
    }

    private function formRedirect(?int $id, string $message): RedirectResponse
    {
        $url = $id === null ? site_url('website-news/new') : site_url('website-news/edit/' . $id);

        return redirect()->to($url)
            ->withInput()
            ->with('newsError', $message);
    }

    private function emptyArticle(): array
    {
        return [
            'id' => '',
            'title' => '',
            'slug' => '',
            'category' => '',
            'tags_input' => '',
            'excerpt' => '',
            'content' => '',
            'image' => '',
            'status' => 'draft',
            'author' => '',
            'published_at_input' => '',
            'featured' => 0,
        ];
    }

    private function slugFromTitle(string $title): string
    {
        $title = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', trim($title)) ?: trim($title);
        $slug = strtolower($title);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';

        return trim($slug, '-');
    }

    private function isSafeImageSource(string $source): bool
    {
        if (str_starts_with($source, '/')) {
            return ! str_starts_with($source, '//');
        }

        $scheme = strtolower((string) parse_url($source, PHP_URL_SCHEME));

        return filter_var($source, FILTER_VALIDATE_URL) !== false
            && in_array($scheme, ['http', 'https'], true);
    }
}
