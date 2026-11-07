<?php

namespace App\Models;

use CodeIgniter\Model;

class CbtSoalModel extends Model
{
    protected $table            = 'cbt_soal';
    protected $primaryKey       = 'soal_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ujian_id',
        'tipe_soal',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'bobot_nilai',
        'urutan',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getByUjian(int $ujianId, bool $random = false)
    {
        $builder = $this->where('ujian_id', $ujianId);
        if ($random) {
            $builder->orderBy('RAND()');
        } else {
            $builder->orderBy('urutan', 'ASC')->orderBy('soal_id', 'ASC');
        }

        return $builder->findAll();
    }
}
