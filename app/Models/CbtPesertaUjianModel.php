<?php

namespace App\Models;

use CodeIgniter\Model;

class CbtPesertaUjianModel extends Model
{
    protected $table            = 'cbt_peserta_ujian';
    protected $primaryKey       = 'peserta_ujian_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ujian_id',
        'siswa_id',
        'waktu_mulai',
        'waktu_selesai',
        'sisa_detik',
        'jumlah_benar',
        'jumlah_salah',
        'jumlah_tidak_dijawab',
        'nilai_pg',
        'nilai_essay',
        'nilai_total',
        'status_pengerjaan',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPesertaList(int $ujianId)
    {
        return $this->select('cbt_peserta_ujian.*, master_siswa.nama_siswa, master_siswa.nis, master_siswa.nisn, master_siswa.no_peserta, master_kelas.kelas_nama')
            ->join('master_siswa', 'master_siswa.siswa_id = cbt_peserta_ujian.siswa_id', 'left')
            ->join('master_kelas', 'master_kelas.kelas_id = master_siswa.kelas_id', 'left')
            ->where('cbt_peserta_ujian.ujian_id', $ujianId)
            ->orderBy('cbt_peserta_ujian.nilai_total', 'DESC')
            ->orderBy('master_siswa.nama_siswa', 'ASC')
            ->findAll();
    }
}
