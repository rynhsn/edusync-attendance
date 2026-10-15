<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table         = 'settings';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['key', 'value'];
    protected $useTimestamps = true;

    /**
     * Retrieve a single setting value by its key.
     *
     * @param string $key The setting key.
     *
     * @return string|null The setting value, or null if not found.
     */
    public function get(string $key): ?string
    {
        $row = $this->where('key', $key)->first();
        return $row ? $row['value'] : null;
    }

    /**
     * Create or update a setting value by its key.
     *
     * @param string      $key   The setting key.
     * @param string|null $value The setting value.
     *
     * @return void
     */
    public function setSetting(string $key, ?string $value): void
    {
        $existing = $this->where('key', $key)->first();
        if ($existing) {
            $this->update($existing['id'], ['value' => $value]);
        } else {
            $this->insert(['key' => $key, 'value' => $value]);
        }
    }

    /**
     * Retrieve a JSON-decoded setting value.
     *
     * @param string     $key     The setting key.
     * @param mixed      $default Default value if the key is not found or decoding fails.
     *
     * @return mixed The decoded JSON value, or the default.
     */
    public function getJson(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key);
        return $value ? json_decode($value, true) : $default;
    }

    /**
     * Retrieve multiple settings by their keys.
     *
     * @param array $keys Array of setting keys.
     *
     * @return array Associative array of key => value pairs.
     */
    public function getMany(array $keys): array
    {
        $rows = $this->whereIn('key', $keys)->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }
}
