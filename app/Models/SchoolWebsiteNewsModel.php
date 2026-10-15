<?php

namespace App\Models;

use CodeIgniter\Model;

class SchoolWebsiteNewsModel extends Model
{
    protected $table = 'school_website_news';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'title',
        'slug',
        'category',
        'tags',
        'excerpt',
        'content',
        'image',
        'status',
        'author',
        'published_at',
        'view_count',
        'featured',
    ];
    protected $useTimestamps = true;

    public function getPublished(?string $search, ?string $category, ?string $month, int $perPage = 6): array
    {
        $this->where('status', 'published');

        if ($search !== null && $search !== '') {
            $this->groupStart()
                ->like('title', $search)
                ->orLike('content', $search)
                ->groupEnd();
        }

        if ($category !== null && $category !== '') {
            $this->where('FIND_IN_SET(' . $this->db->escape($category) . ', category) > 0', null, false);
        }

        if ($month !== null && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month) === 1) {
            $this->where("DATE_FORMAT(published_at, '%Y-%m')", $month);
        }

        return $this->orderBy('published_at', 'DESC')->paginate($perPage);
    }

    public function getPublishedBySlug(string $slug): ?array
    {
        $post = $this->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        return is_array($post) ? $post : null;
    }

    public function getRecent(int $limit = 5): array
    {
        return $this->where('status', 'published')
            ->orderBy('published_at', 'DESC')
            ->findAll($limit);
    }

    public function searchByQuery(string $query, int $limit = 10): array
    {
        return $this->select('title, slug, excerpt, image')
            ->where('status', 'published')
            ->groupStart()
                ->like('title', $query)
                ->orLike('content', $query)
            ->groupEnd()
            ->orderBy('published_at', 'DESC')
            ->findAll($limit);
    }

    public function getCategories(): array
    {
        $counts = [];
        foreach ($this->select('category')->where('status', 'published')->findAll() as $row) {
            foreach (array_map('trim', explode(',', (string) $row['category'])) as $name) {
                if ($name !== '') {
                    $counts[$name] = ($counts[$name] ?? 0) + 1;
                }
            }
        }

        ksort($counts);

        $categories = [];
        foreach ($counts as $name => $count) {
            $categories[] = ['name' => $name, 'slug' => $name, 'count' => $count];
        }

        return $categories;
    }

    public function getTags(): array
    {
        $counts = [];
        foreach ($this->select('tags')->where('status', 'published')->findAll() as $row) {
            $tags = json_decode((string) $row['tags'], true);
            if (! is_array($tags)) {
                continue;
            }
            foreach ($tags as $tag) {
                $tag = trim((string) $tag);
                if ($tag !== '') {
                    $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                }
            }
        }

        $tags = [];
        foreach ($counts as $name => $count) {
            $tags[] = ['name' => $name, 'slug' => $name, 'count' => $count];
        }

        return $tags;
    }

    public function getArchive(): array
    {
        return $this->db->table($this->table)
            ->select("DATE_FORMAT(published_at, '%Y-%m') AS month, DATE_FORMAT(published_at, '%M %Y') AS label, COUNT(*) AS count", false)
            ->where('status', 'published')
            ->groupBy(['month', 'label'])
            ->orderBy('month', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getRelated(array $post, int $limit = 3): array
    {
        $categories = array_values(array_filter(array_map('trim', explode(',', (string) ($post['category'] ?? '')))));
        $this->where('status', 'published')
            ->where('id !=', $post['id']);

        if ($categories !== []) {
            $this->groupStart();
            foreach ($categories as $index => $category) {
                $condition = 'FIND_IN_SET(' . $this->db->escape($category) . ', category)';
                $index === 0
                    ? $this->where($condition, null, false)
                    : $this->orWhere($condition, null, false);
            }
            $this->groupEnd();
        }

        return $this->orderBy('published_at', 'DESC')->findAll($limit);
    }

    public function getLatest(int $limit = 100): array
    {
        return $this->orderBy('updated_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll($limit);
    }

    public function incrementViews(int $id): void
    {
        $this->builder()
            ->set('view_count', 'view_count + 1', false)
            ->where('id', $id)
            ->update();
    }

    public function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('slug', $slug);
        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }
}
