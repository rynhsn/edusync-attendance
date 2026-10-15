<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSidebarMenuIconOverrides extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('sidebar_menu_icon_overrides')) {
            return;
        }

        $this->forge->addField([
            'menu_key' => ['type' => 'VARCHAR', 'constraint' => 80],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 64],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('menu_key', true);
        $this->forge->createTable('sidebar_menu_icon_overrides');
    }

    public function down()
    {
        $this->forge->dropTable('sidebar_menu_icon_overrides', true);
    }
}
