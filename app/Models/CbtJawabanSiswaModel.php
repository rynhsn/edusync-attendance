<?php

namespace App\Models;

use CodeIgniter\Model;

class CbtJawabanSiswaModel extends Model
{
    protected $table            = 'cbt_jawaban_siswa';
    protected $primaryKey       = 'jawaban_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'peserta_ujian_id',
        'soal_id',
        'jawaban_pg',
        'jawaban_essay',
        'is_correct',
        'nilai_didapat',
        'catatan_guru',
        'ragu_ragu',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAnswersByPeserta(int $pesertaUjianId)
    {
        return $this->select('cbt_jawaban_siswa.*, cbt_soal.pertanyaan, cbt_soal.tipe_soal, cbt_soal.opsi_a, cbt_soal.opsi_b, cbt_soal.opsi_c, cbt_soal.opsi_d, cbt_soal.opsi_e, cbt_soal.kunci_jawaban, cbt_soal.bobot_nilai, cbt_soal.urutan')
            ->join('cbt_soal', 'cbt_soal.soal_id = cbt_jawaban_siswa.soal_id')
            ->where('cbt_jawaban_siswa.peserta_ujian_id', $pesertaUjianId)
            ->orderBy('cbt_soal.urutan', 'ASC')
            ->orderBy('cbt_soal.soal_id', 'ASC')
            ->findAll();
    }
}
