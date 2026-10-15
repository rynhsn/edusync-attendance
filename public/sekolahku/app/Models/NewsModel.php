<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsModel extends Model
{
    protected $table         = 'news';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['category', 'tags', 'title', 'slug', 'excerpt', 'content', 'image', 'author', 'published_at', 'status', 'is_featured', 'meta_title', 'meta_description', 'view_count'];
    protected $useTimestamps = true;

    /**
     * Retrieve paginated published news articles.
     *
     * @param int $perPage Number of items per page.
     *
     * @return array
     */
    public function getPublished(int $perPage = 6)
    {
        return $this->where('status', 'published')
                    ->orderBy('published_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Retrieve a single published news article by its slug.
     *
     * @param string $slug The news slug.
     *
     * @return array|null
     */
    public function getBySlug(string $slug)
    {
        return $this->where('status', 'published')
                    ->where('slug', $slug)
                    ->first();
    }

    /**
     * Retrieve featured news articles.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getFeatured(int $limit = 3)
    {
        return $this->where('status', 'published')
                    ->where('is_featured', 1)
                    ->orderBy('published_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Retrieve related news articles based on shared categories.
     *
     * @param array $news  The news article to find relatives for.
     * @param int   $limit Maximum number of results.
     *
     * @return array
     */
    public function getRelated($news, int $limit = 3)
    {
        $catParts = array_map('trim', explode(',', $news['category']));

        $builder = $this->where('status', 'published')
                    ->where('id !=', $news['id']);
        $builder->groupStart();
        foreach ($catParts as $i => $cp) {
            if ($cp === '') continue;
            $escaped = $this->db->escape($cp);
            if ($i > 0) {
                $builder->orWhere("FIND_IN_SET({$escaped}, category)");
            } else {
                $builder->where("FIND_IN_SET({$escaped}, category)");
            }
        }
        $builder->groupEnd();

        return $builder->orderBy('published_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Search published news articles by title or content.
     *
     * @param string $query  The search keyword.
     * @param int    $perPage Number of items per page.
     *
     * @return array
     */
    public function search(string $query, int $perPage = 10)
    {
        return $this->where('status', 'published')
                    ->groupStart()
                        ->like('title', $query)
                        ->orLike('content', $query)
                    ->groupEnd()
                    ->orderBy('published_at', 'DESC')
                    ->paginate($perPage);
    }

    /**
     * Increment the view count of a news article.
     *
     * @param int $id The news article ID.
     *
     * @return bool
     */
    public function incrementViews(int $id)
    {
        return $this->db->table($this->table)
                        ->set('view_count', 'view_count + 1', false)
                        ->where('id', $id)
                        ->update();
    }

    /**
     * Retrieve the previous published article relative to the given article.
     *
     * @param int    $id          The current article ID.
     * @param string $publishedAt The published date of the current article.
     *
     * @return array|null
     */
    public function getPrevPost(int $id, string $publishedAt)
    {
        return $this->where('status', 'published')
                    ->where('id !=', $id)
                    ->where('published_at <=', $publishedAt)
                    ->orderBy('published_at', 'DESC')
                    ->first();
    }

    /**
     * Retrieve the next published article relative to the given article.
     *
     * @param int    $id          The current article ID.
     * @param string $publishedAt The published date of the current article.
     *
     * @return array|null
     */
    public function getNextPost(int $id, string $publishedAt)
    {
        return $this->where('status', 'published')
                    ->where('id !=', $id)
                    ->where('published_at >=', $publishedAt)
                    ->orderBy('published_at', 'ASC')
                    ->first();
    }

    /**
     * Retrieve the most recent published news articles.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getRecent(int $limit = 5)
    {
        return $this->where('status', 'published')
                    ->orderBy('published_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Retrieve published news categories with article counts.
     *
     * @return array
     */
    public function getPublishedCategories(): array
    {
        $rows = $this->select('category')
                     ->where('status', 'published')
                     ->findAll();
        $catCounts = [];
        foreach ($rows as $cr) {
            $parts = array_map('trim', explode(',', $cr['category']));
            foreach ($parts as $c) {
                if ($c !== '') {
                    $catCounts[$c] = ($catCounts[$c] ?? 0) + 1;
                }
            }
        }
        ksort($catCounts);
        $categories = [];
        foreach ($catCounts as $name => $count) {
            $categories[] = [
                'name'  => $name,
                'slug'  => $name,
                'count' => $count,
            ];
        }
        return $categories;
    }

    /**
     * Retrieve all tags from published news articles with usage counts.
     *
     * @return array
     */
    public function getAllTags(): array
    {
        $rows = $this->select('tags')
                     ->where('status', 'published')
                     ->findAll();
        $tagCounts = [];
        foreach ($rows as $tr) {
            $t = json_decode($tr['tags'], true);
            if (is_array($t)) {
                foreach ($t as $tag) {
                    $tag = trim($tag);
                    if ($tag !== '') {
                        $tagCounts[$tag] = ($tagCounts[$tag] ?? 0) + 1;
                    }
                }
            }
        }
        $tags = [];
        foreach ($tagCounts as $name => $count) {
            $tags[] = ['name' => $name, 'slug' => $name, 'count' => $count];
        }
        return $tags;
    }

    /**
     * Retrieve monthly news archive with article counts.
     *
     * @return array
     */
    public function getArchive(): array
    {
        return $this->db->query(
            "SELECT DATE_FORMAT(published_at, '%Y-%m') as month, DATE_FORMAT(published_at, '%M %Y') as label, COUNT(*) as count FROM news WHERE status='published' GROUP BY month, label ORDER BY month DESC"
        )->getResultArray();
    }

    /**
     * Retrieve published news data for XML sitemap generation.
     *
     * @return array
     */
    public function getForSitemap(): array
    {
        return $this->where('status', 'published')
                    ->select('slug, updated_at, published_at')
                    ->findAll();
    }

    /**
     * Retrieve published news filtered by category and/or month.
     *
     * @param string|null $category The category name to filter by.
     * @param string|null $month    The month (YYYY-MM) to filter by.
     * @param int         $perPage  Number of items per page.
     *
     * @return array
     */
    public function getFiltered(?string $category, ?string $month, int $perPage = 6)
    {
        if ($category) {
            $escaped = $this->db->escape($category);
            $this->where("FIND_IN_SET({$escaped}, category)");
        }
        if ($month) {
            $this->where("DATE_FORMAT(published_at, '%Y-%m')", $month);
        }
        return $this->getPublished($perPage);
    }

    /**
     * Search published news articles by query string (AJAX endpoint).
     *
     * @param string $query The search keyword.
     * @param int    $limit Maximum number of results.
     *
     * @return array
     */
    public function searchByQuery(string $query, int $limit = 10)
    {
        return $this->where('status', 'published')
                    ->groupStart()
                        ->like('title', $query)
                        ->orLike('content', $query)
                    ->groupEnd()
                    ->orderBy('published_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Retrieve a news article by slug regardless of its status.
     *
     * @param string $slug The news slug.
     *
     * @return array|null
     */
    public function getBySlugAnyStatus(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }
}
