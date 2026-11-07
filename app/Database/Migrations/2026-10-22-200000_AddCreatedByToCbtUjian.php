<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedByToCbtUjian extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('cbt_ujian')) {
            if (! $this->db->fieldExists('created_by', 'cbt_ujian')) {
                $this->forge->addColumn('cbt_ujian', [
                    'created_by' => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'default'    => null,
                        'after'      => 'guru_id',
                    ],
                ]);

                // Backfill existing exams
                $this->db->query("
                    UPDATE cbt_ujian cu
                    LEFT JOIN auth_users au ON au.guru_id = cu.guru_id
                    SET cu.created_by = COALESCE(au.user_id, 1)
                    WHERE cu.created_by IS NULL
                ");
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('cbt_ujian') && $this->db->fieldExists('created_by', 'cbt_ujian')) {
            $this->forge->dropColumn('cbt_ujian', 'created_by');
        }
    }
}
