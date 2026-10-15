<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleSidebarSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('role_sidebar_settings')) {
            return;
        }

        $this->forge->addField([
            'role' => ['type' => 'VARCHAR', 'constraint' => 32],
            'menu_key' => ['type' => 'VARCHAR', 'constraint' => 50],
            'is_enabled' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey(['role', 'menu_key'], true, true);
        $this->forge->createTable('role_sidebar_settings');
    }

    public function down()
    {
        $this->forge->dropTable('role_sidebar_settings', true);
    }
}
