<?php

namespace App\Models;

use CodeIgniter\Model;

class CbtUjianModel extends Model
{
    protected $table            = 'cbt_ujian';
    protected $primaryKey       = 'ujian_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'judul',
        'deskripsi',
        'tahun_ajaran_id',
        'mapel_id',
        'kelas_id',
        'guru_id',
        'created_by',
        'durasi_menit',
        'waktu_mulai',
        'waktu_selesai',
        'token',
        'petunjuk',
        'acak_soal',
        'acak_opsi',
        'tampil_nilai',
        'tampil_pembahasan',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getWithRelations(?int $id = null, ?int $userId = null, ?int $guruId = null, bool $isAdmin = true, ?int $tahunAjaranId = null, bool $onlyActiveYear = false)
    {
        $builder = $this->select('cbt_ujian.*, master_mapel.mapel_nama, master_kelas.kelas_nama, master_guru.guru_nama, master_tahun_ajaran.tahun_ajaran_nama, master_tahun_ajaran.semester as tahun_ajaran_semester, master_tahun_ajaran.is_active as tahun_ajaran_is_active')
            ->join('master_mapel', 'master_mapel.mapel_id = cbt_ujian.mapel_id', 'left')
            ->join('master_kelas', 'master_kelas.kelas_id = cbt_ujian.kelas_id', 'left')
            ->join('master_guru', 'master_guru.guru_id = cbt_ujian.guru_id', 'left')
            ->join('master_tahun_ajaran', 'master_tahun_ajaran.tahun_ajaran_id = cbt_ujian.tahun_ajaran_id', 'left');

        if ($id !== null) {
            return $builder->where('cbt_ujian.ujian_id', $id)->first();
        }

        if ($onlyActiveYear) {
            $builder->where('master_tahun_ajaran.is_active', 1);
        } elseif ($tahunAjaranId !== null && $tahunAjaranId > 0) {
            $builder->where('cbt_ujian.tahun_ajaran_id', $tahunAjaranId);
        }

        if (! $isAdmin) {
            $builder->groupStart();
            $hasCondition = false;
            if ($userId !== null && $userId > 0) {
                $builder->where('cbt_ujian.created_by', $userId);
                $hasCondition = true;
            }
            if ($guruId !== null && $guruId > 0) {
                if ($hasCondition) {
                    $builder->orWhere('cbt_ujian.guru_id', $guruId);
                } else {
                    $builder->where('cbt_ujian.guru_id', $guruId);
                    $hasCondition = true;
                }
            }
            if (! $hasCondition) {
                $builder->where('1 = 0');
            }
            $builder->groupEnd();
        }

        return $builder->orderBy('cbt_ujian.ujian_id', 'DESC')->findAll();
    }
}
