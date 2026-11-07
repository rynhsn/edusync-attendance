<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTahunAjaranToCbtUjian extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('cbt_ujian')) {
            if (! $this->db->fieldExists('tahun_ajaran_id', 'cbt_ujian')) {
                $this->forge->addColumn('cbt_ujian', [
                    'tahun_ajaran_id' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'default'    => null,
                        'after'      => 'mapel_id',
                    ],
                ]);

                // Backfill existing exams dengan tahun ajaran yang sedang aktif
                $activeYear = $this->db->table('master_tahun_ajaran')
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();

                if (! $activeYear) {
                    $activeYear = $this->db->table('master_tahun_ajaran')
                        ->orderBy('tahun_ajaran_id', 'DESC')
                        ->get()
                        ->getRowArray();
                }

                if ($activeYear) {
                    $yearId = (int) $activeYear['tahun_ajaran_id'];
                    $this->db->query("UPDATE cbt_ujian SET tahun_ajaran_id = {$yearId} WHERE tahun_ajaran_id IS NULL");
                }

                // Tambahkan index foreign key
                if ($this->db->tableExists('master_tahun_ajaran')) {
                    $this->db->query("
                        ALTER TABLE cbt_ujian 
                        ADD CONSTRAINT fk_cbt_ujian_tahun_ajaran 
                        FOREIGN KEY (tahun_ajaran_id) REFERENCES master_tahun_ajaran(tahun_ajaran_id) 
                        ON DELETE SET NULL ON UPDATE CASCADE
                    ");
                }
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('cbt_ujian') && $this->db->fieldExists('tahun_ajaran_id', 'cbt_ujian')) {
            try {
                $this->db->query("ALTER TABLE cbt_ujian DROP FOREIGN KEY fk_cbt_ujian_tahun_ajaran");
            } catch (\Throwable $e) {
                // Ignore if fk already dropped
            }
            $this->forge->dropColumn('cbt_ujian', 'tahun_ajaran_id');
        }
    }
}
