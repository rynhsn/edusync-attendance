<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthUsers extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('auth_users')) {
            $this->forge->addField([
                'user_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'email' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 254,
                ],
                'display_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => '',
                ],
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'user',
                ],
                'is_verified' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 0,
                ],
                'otp_hash' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'otp_expires_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'otp_sent_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'otp_attempts' => [
                    'type'       => 'TINYINT',
                    'constraint' => 3,
                    'default'    => 0,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('user_id', true);
            $this->forge->addUniqueKey('email');
            $this->forge->createTable('auth_users');
        }

        $superAdminEmail = strtolower(trim((string) env('auth.super_admin_email')));
        if (filter_var($superAdminEmail, FILTER_VALIDATE_EMAIL) !== false) {
            $users = $this->db->table('auth_users');
            $user = $users->where('email', $superAdminEmail)->get()->getRowArray();

            if ($user === null) {
                $users->insert([
                    'email'        => $superAdminEmail,
                    'display_name' => 'Super Admin',
                    'role'         => 'super_admin',
                    'is_verified'  => 0,
                ]);
            } else {
                $users->where('user_id', $user['user_id'])->update(['role' => 'super_admin']);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('auth_users', true);
    }
}
