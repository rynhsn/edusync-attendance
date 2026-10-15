<?php

namespace App\Models;

use CodeIgniter\Model;

class PageModel extends Model
{
    protected $table         = 'pages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['title', 'slug', 'content', 'excerpt', 'image', 'status', 'meta_title', 'meta_description'];
    protected $useTimestamps = true;

    /**
     * Retrieve a single published page by its slug.
     *
     * @param string $slug The page slug.
     *
     * @return array|null
     */
    public function getBySlug(string $slug)
    {
        return $this->where('slug', $slug)
                    ->where('status', 'published')
                    ->first();
    }

    /**
     * Retrieve all published pages ordered by creation date descending.
     *
     * @return array
     */
    public function getPublishedAll()
    {
        return $this->where('status', 'published')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
