<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveSpmbAdmissionPath extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('spmb_applications')
            && $this->db->fieldExists('admission_path', 'spmb_applications')) {
            $this->forge->dropColumn('spmb_applications', 'admission_path');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('spmb_applications')
            && ! $this->db->fieldExists('admission_path', 'spmb_applications')) {
            $this->forge->addColumn('spmb_applications', [
                'admission_path' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            ]);
        }
    }
}
