<?php

namespace App\Models;

class SchoolContactSettingsModel
{
    public const DEFAULTS = [
        'address' => 'Jl. Pendidikan No. 123, Kelurahan Cerdas, Kecamatan Unggul, Kota Pelajar 12345',
        'phone' => '(021) 1234-5678',
        'email' => 'info@sekolahku.sch.id',
        'hours' => 'Senin - Jumat: 07.00 - 16.00 WIB',
    ];

    private const SETTING_KEYS = [
        'address' => 'contact_address',
        'phone' => 'contact_phone',
        'email' => 'contact_email',
        'hours' => 'contact_hours',
    ];

    public function getSettings(): array
    {
        $defaults = [];
        foreach (self::SETTING_KEYS as $field => $key) {
            $defaults[$key] = self::DEFAULTS[$field];
        }
        $stored = (new SchoolkuWebsiteContentModel())->getSettings($defaults);

        $settings = [];
        foreach (self::SETTING_KEYS as $field => $key) {
            $settings[$field] = $stored[$key];
        }

        return $settings;
    }

    public function saveSettings(array $settings): bool
    {
        $values = [];
        foreach (self::SETTING_KEYS as $field => $key) {
            $values[$key] = $settings[$field];
        }

        (new SchoolkuWebsiteContentModel())->saveSettings($values);

        return true;
    }
}
