<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpmbApplications extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('spmb_applications')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'registration_code' => ['type' => 'VARCHAR', 'constraint' => 32],
            'student_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'student_nik' => ['type' => 'CHAR', 'constraint' => 16],
            'student_nisn' => ['type' => 'CHAR', 'constraint' => 10],
            'contact_email' => ['type' => 'VARCHAR', 'constraint' => 254],
            'application_data' => ['type' => 'LONGTEXT'],
            'documents' => ['type' => 'LONGTEXT'],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'submitted'],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('registration_code', 'uq_spmb_registration_code');
        $this->forge->addKey('student_nik', false, false, 'idx_spmb_student_nik');
        $this->forge->addKey('contact_email', false, false, 'idx_spmb_contact_email');
        $this->forge->addKey('status', false, false, 'idx_spmb_status');
        $this->forge->createTable('spmb_applications');
    }

    public function down()
    {
        $this->forge->dropTable('spmb_applications', true);
    }
}
