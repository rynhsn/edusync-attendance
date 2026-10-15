<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleActionSettings extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('role_action_settings')) {
            $this->forge->addField([
                'role' => ['type' => 'VARCHAR', 'constraint' => 32],
                'action_key' => ['type' => 'VARCHAR', 'constraint' => 80],
                'is_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey(['role', 'action_key'], true, true);
            $this->forge->createTable('role_action_settings');
        }

        if (! $this->db->tableExists('user_action_settings')) {
            $this->forge->addField([
                'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'action_key' => ['type' => 'VARCHAR', 'constraint' => 80],
                'is_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey(['user_id', 'action_key'], true, true);
            $this->forge->createTable('user_action_settings');
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_action_settings', true);
        $this->forge->dropTable('role_action_settings', true);
    }
}
