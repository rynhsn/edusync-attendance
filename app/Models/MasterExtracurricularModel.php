<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;

class MasterExtracurricularModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getActiveActivities(): array
    {
        if (! $this->db->tableExists('master_ekstrakurikuler')) {
            return [];
        }

        $activities = $this->db->table('master_ekstrakurikuler')
            ->select('nama_kegiatan')
            ->where('is_active', 1)
            ->where('nama_kegiatan !=', '')
            ->orderBy('nama_kegiatan', 'ASC')
            ->get()
            ->getResultArray();

        return array_values(array_filter(array_map(
            static function (array $activity): array {
                $activity['nama_kegiatan'] = trim((string) ($activity['nama_kegiatan'] ?? ''));

                return $activity;
            },
            $activities
        ), static fn (array $activity): bool => $activity['nama_kegiatan'] !== ''));
    }
}
