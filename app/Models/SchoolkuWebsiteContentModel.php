<?php

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Database\Exceptions\DatabaseException;

class SchoolkuWebsiteContentModel
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function getSettings(array $defaults): array
    {
        $rows = $this->db->table('school_website_settings')
            ->select('setting_key, setting_value')
            ->whereIn('setting_key', array_keys($defaults))
            ->get()
            ->getResultArray();

        $settings = $defaults;
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return $settings;
    }

    public function saveSettings(array $settings): void
    {
        $this->db->transBegin();

        try {
            foreach ($settings as $key => $value) {
                $existing = $this->db->table('school_website_settings')
                    ->select('id')
                    ->where('setting_key', $key)
                    ->get()
                    ->getRowArray();
                $updatedAt = date('Y-m-d H:i:s');

                $saved = $existing
                    ? $this->db->table('school_website_settings')
                        ->where('id', $existing['id'])
                        ->update(['setting_value' => $value, 'updated_at' => $updatedAt])
                    : $this->db->table('school_website_settings')
                        ->insert(['setting_key' => $key, 'setting_value' => $value, 'updated_at' => $updatedAt]);

                if (! $saved) {
                    throw new DatabaseException('Could not save website setting: ' . $key);
                }
            }

            if ($this->db->transStatus() === false) {
                throw new DatabaseException('Website settings transaction failed.');
            }

            $this->db->transCommit();
        } catch (DatabaseException $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }

    public function getPrograms(bool $visibleOnly = false): array
    {
        $builder = $this->db->table('school_website_programs');
        if ($visibleOnly) {
            $builder->where('show', 1);
        }

        return $builder->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function saveProgram(array $program, ?int $id = null): void
    {
        $program['updated_at'] = date('Y-m-d H:i:s');

        if ($id !== null) {
            if ($this->db->table('school_website_programs')->where('id', $id)->countAllResults() === 0) {
                throw new DatabaseException('Website program does not exist.');
            }
            if (! $this->db->table('school_website_programs')->where('id', $id)->update($program)) {
                throw new DatabaseException('Could not update website program.');
            }

            return;
        }

        $program['created_at'] = date('Y-m-d H:i:s');
        if (! $this->db->table('school_website_programs')->insert($program)) {
            throw new DatabaseException('Could not create website program.');
        }
    }

    public function deleteProgram(int $id): void
    {
        if ($this->db->table('school_website_programs')->where('id', $id)->countAllResults() === 0) {
            throw new DatabaseException('Website program does not exist.');
        }
        if (! $this->db->table('school_website_programs')->where('id', $id)->delete()) {
            throw new DatabaseException('Could not delete website program.');
        }
    }
}
