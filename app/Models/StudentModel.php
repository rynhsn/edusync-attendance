<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'master_siswa';
    protected $primaryKey       = 'siswa_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "kelas_id",
        "nama_siswa",
        "nis",
        "nisn",
        "jk",
        "tempat_lahir",
        "tanggal_lahir",
        "alamat",
        "rfid_tag",
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

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

    //get semua data siswa dengan relasi kelas
    public function getAllStudent()
    {
        return $this->select('master_siswa.*, master_kelas.kelas_nama as kelas')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id')
            ->findAll();
    }

    //get data siswa berdasarkan kondisi
    public function getStudent($condition)
    {
        return $this->select('master_siswa.*, master_kelas.kelas_nama as kelas')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id')
            ->where($condition)
            ->first();
    }
}
