<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table            = 'absensi_absensi_sekolah';
    protected $primaryKey       = 'absensi_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'absensi_id',
        'siswa_id',
        'tanggal',
        'waktu_masuk',
        'waktu_pulang',
        'waktu_masuk_aktual',
        'waktu_pulang_aktual',
        'status_kehadiran_id',
        'status_absensi_masuk_id',
        'status_absensi_pulang_id',
        'keterangan',
        'created_at',
        'updated_at',
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

    public function getAttendance($condition = null)
    {
        // berdasarkan tanggal dan join dengan tabel siswa dan kelas
        $builder = $this->db->table($this->table);
        $builder->select('absensi_absensi_sekolah.*, ms.nama_siswa, mk.kelas_nama, ask.status_kehadiran, ask.status_kehadiran_warna, asam.status_absensi as status_masuk, asam.status_absensi_warna as status_masuk_warna, asak.status_absensi as status_pulang, asak.status_absensi_warna as status_pulang_warna')
            ->join('master_siswa ms', 'ms.siswa_id = absensi_absensi_sekolah.siswa_id')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id')
            ->join('absensi_status_kehadiran ask', 'ask.status_kehadiran_id = absensi_absensi_sekolah.status_kehadiran_id')
            ->join('absensi_status_absensi asam', 'asam.status_absensi_id = absensi_absensi_sekolah.status_absensi_masuk_id')
            ->join('absensi_status_absensi asak', 'asak.status_absensi_id = absensi_absensi_sekolah.status_absensi_pulang_id');
        if ($condition != null) {
            $builder->where($condition);
        }

        return $builder->get()->getResultArray();
    }
}
