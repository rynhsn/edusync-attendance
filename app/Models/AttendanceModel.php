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
        'guru_id',
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
        // berdasarkan tanggal dan join dengan tabel siswa/guru dan kelas
        $builder = $this->db->table($this->table);
        $builder->select('absensi_absensi_sekolah.*, COALESCE(ms.nama_siswa, mg.guru_nama) as nama_siswa, COALESCE(mk.kelas_nama, "Guru") as kelas_nama, ask.status_kehadiran, ask.status_kehadiran_warna, asam.status_absensi as status_masuk, asam.status_absensi_warna as status_masuk_warna, asak.status_absensi as status_pulang, asak.status_absensi_warna as status_pulang_warna, mg.guru_nama, mg.foto as guru_foto')
            ->join('master_siswa ms', 'ms.siswa_id = absensi_absensi_sekolah.siswa_id', 'left')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left')
            ->join('master_guru mg', 'mg.guru_id = absensi_absensi_sekolah.guru_id', 'left')
            ->join('absensi_status_kehadiran ask', 'ask.status_kehadiran_id = absensi_absensi_sekolah.status_kehadiran_id', 'left')
            ->join('absensi_status_absensi asam', 'asam.status_absensi_id = absensi_absensi_sekolah.status_absensi_masuk_id', 'left')
            ->join('absensi_status_absensi asak', 'asak.status_absensi_id = absensi_absensi_sekolah.status_absensi_pulang_id', 'left');
        if ($condition != null) {
            $builder->where($condition);
        }

        return $builder->get()->getResultArray();
    }

    public function getAttendanceReport(?string $dateFrom = null, ?string $dateTo = null, ?string $studentName = null): array
    {
        $builder = $this->db->table($this->table . ' ase')
            ->select('ase.*, COALESCE(ms.nama_siswa, mg.guru_nama) as nama_siswa, COALESCE(mk.kelas_nama, "Guru") as kelas_nama, ask.status_kehadiran, ask.status_kehadiran_warna, asam.status_absensi as status_masuk, asam.status_absensi_warna as status_masuk_warna, asak.status_absensi as status_pulang, asak.status_absensi_warna as status_pulang_warna')
            ->join('master_siswa ms', 'ms.siswa_id = ase.siswa_id', 'left')
            ->join('master_kelas mk', 'mk.kelas_id = ms.kelas_id', 'left')
            ->join('master_guru mg', 'mg.guru_id = ase.guru_id', 'left')
            ->join('absensi_status_kehadiran ask', 'ask.status_kehadiran_id = ase.status_kehadiran_id', 'left')
            ->join('absensi_status_absensi asam', 'asam.status_absensi_id = ase.status_absensi_masuk_id', 'left')
            ->join('absensi_status_absensi asak', 'asak.status_absensi_id = ase.status_absensi_pulang_id', 'left');

        if ($dateFrom !== null && $dateFrom !== '') {
            $builder->where('ase.tanggal >=', $dateFrom);
        }

        if ($dateTo !== null && $dateTo !== '') {
            $builder->where('ase.tanggal <=', $dateTo);
        }

        if ($studentName !== null && $studentName !== '') {
            $builder->groupStart()
                ->like('ms.nama_siswa', $studentName)
                ->orLike('mg.guru_nama', $studentName)
                ->groupEnd();
        }

        return $builder
            ->orderBy('ase.tanggal', 'DESC')
            ->get()
            ->getResultArray();
    }
}
