<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePersonalityAspects extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('academic_personality_aspects')) {
            $this->forge->addField([
                'aspek_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nama_aspek' => ['type' => 'VARCHAR', 'constraint' => 100],
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'urutan' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('aspek_id', true);
            $this->forge->addUniqueKey('nama_aspek', 'uq_personality_aspect_name');
            $this->forge->createTable('academic_personality_aspects');
        }

        if (! $this->db->tableExists('academic_student_personality_scores')) {
            $this->forge->addField([
                'nilai_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'aspek_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'nilai' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
                'dicatat_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('nilai_id', true);
            $this->forge->addUniqueKey(
                ['siswa_id', 'kelas_id', 'tahun_ajaran_id', 'aspek_id'],
                'uq_student_personality_aspect_score'
            );
            $this->forge->createTable('academic_student_personality_scores');
        }

        foreach (['Sikap', 'Kerajinan', 'Kebersihan dan Kerapian'] as $order => $name) {
            $existing = $this->db->table('academic_personality_aspects')
                ->where('nama_aspek', $name)
                ->get()
                ->getRowArray();
            if ($existing === null) {
                $this->db->table('academic_personality_aspects')->insert([
                    'nama_aspek' => $name,
                    'is_active' => 1,
                    'urutan' => $order + 1,
                    'dicatat_oleh' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        if ($this->db->tableExists('academic_student_personality')) {
            $legacyFields = [
                'nilai_sikap' => 'Sikap',
                'nilai_kerajinan' => 'Kerajinan',
                'nilai_kebersihan' => 'Kebersihan dan Kerapian',
            ];
            foreach ($legacyFields as $legacyField => $aspectName) {
                $this->db->query(
                    'INSERT IGNORE INTO academic_student_personality_scores '
                    . '(siswa_id, kelas_id, tahun_ajaran_id, aspek_id, nilai, dicatat_oleh, created_at, updated_at) '
                    . 'SELECT legacy.siswa_id, legacy.kelas_id, legacy.tahun_ajaran_id, aspect.aspek_id, legacy.'
                    . $legacyField . ', legacy.dicatat_oleh, legacy.created_at, legacy.updated_at '
                    . 'FROM academic_student_personality legacy '
                    . 'JOIN academic_personality_aspects aspect ON aspect.nama_aspek = ? '
                    . 'WHERE legacy.' . $legacyField . ' IS NOT NULL',
                    [$aspectName]
                );
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('academic_student_personality_scores', true);
        $this->forge->dropTable('academic_personality_aspects', true);
    }
}
