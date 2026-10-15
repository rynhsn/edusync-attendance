<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table         = 'events';
    protected $primaryKey    = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['title', 'slug', 'description', 'location', 'event_date', 'event_time', 'sort_order', 'status', 'show'];
    protected $useTimestamps = true;

    /**
     * Retrieve upcoming or ongoing events.
     *
     * @param int $limit Maximum number of results.
     *
     * @return array
     */
    public function getUpcoming(int $limit = 4)
    {
        return $this->where('event_date >=', date('Y-m-d'))
                    ->orWhere('status', 'ongoing')
                    ->orderBy('event_date', 'ASC')
                    ->findAll($limit);
    }

    /**
     * Retrieve event data for XML sitemap generation.
     *
     * @return array
     */
    public function getForSitemap(): array
    {
        return $this->where('show', 1)
                    ->select('slug, updated_at, event_date')
                    ->findAll();
    }
}
