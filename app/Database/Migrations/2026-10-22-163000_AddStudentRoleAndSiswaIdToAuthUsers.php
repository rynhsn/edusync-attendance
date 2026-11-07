<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentRoleAndSiswaIdToAuthUsers extends Migration
{
    public function up()
    {
        $fields = [
            'siswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'guru_id',
            ],
        ];

        if (! $this->db->fieldExists('siswa_id', 'auth_users')) {
            $this->forge->addColumn('auth_users', $fields);

            // Add index
            $this->db->query('ALTER TABLE `auth_users` ADD INDEX `idx_auth_users_siswa_id` (`siswa_id`)');

            // Add foreign key constraint to master_siswa(siswa_id)
            $this->db->query('ALTER TABLE `auth_users` ADD CONSTRAINT `fk_auth_users_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `master_siswa` (`siswa_id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('siswa_id', 'auth_users')) {
            // Drop foreign key if exists
            try {
                $this->db->query('ALTER TABLE `auth_users` DROP FOREIGN KEY `fk_auth_users_siswa`');
            } catch (\Throwable $e) {
                // Ignore if constraint doesn't exist
            }

            try {
                $this->db->query('ALTER TABLE `auth_users` DROP INDEX `idx_auth_users_siswa_id`');
            } catch (\Throwable $e) {
                // Ignore if index doesn't exist
            }

            $this->forge->dropColumn('auth_users', 'siswa_id');
        }
    }
}
