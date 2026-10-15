<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeSettingModel extends Model
{
    protected $table            = 'absensi_konfigurasi_waktu';
    protected $primaryKey       = 'konfigurasi_waktu_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kelas_id',
        'hari',
        'jam_masuk',
        'jam_pulang',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Method to join with kelas table and get class name
    public function getTimeSettingsWithClassName()
    {
        // data yang disajikan group by kelas
        return $this->select('absensi_konfigurasi_waktu.*, master_kelas.kelas_nama')
            ->join('master_kelas', 'master_kelas.kelas_id = absensi_konfigurasi_waktu.kelas_id')
            ->orderBy('absensi_konfigurasi_waktu.kelas_id', 'ASC')
            ->findAll();
        // return $this->select('absensi_konfigurasi_waktu.*, master_kelas.kelas_nama')
        //     ->join('master_kelas', 'master_kelas.kelas_id = absensi_konfigurasi_waktu.kelas_id')
        //     ->findAll();
    }
}
