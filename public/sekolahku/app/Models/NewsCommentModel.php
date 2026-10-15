<?php

namespace App\Models;

use CodeIgniter\Model;

class NewsCommentModel extends Model
{
    protected $table         = 'news_comments';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['news_id', 'parent_id', 'name', 'email', 'avatar', 'comment', 'is_approved'];
    protected $useTimestamps = false;
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    /**
     * Retrieve approved comments for a specific news article.
     *
     * @param int $newsId The news article ID.
     *
     * @return array
     */
    public function getByNews(int $newsId)
    {
        return $this->where('news_id', $newsId)
                    ->where('is_approved', 1)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Count pending (unapproved) comments.
     *
     * @return int
     */
    public function getPendingCount(): int
    {
        return $this->where('is_approved', 0)
                    ->countAllResults();
    }

    /**
     * Retrieve the most recent pending comments.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getPendingRecent(int $limit = 5)
    {
        return $this->where('is_approved', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }
}
