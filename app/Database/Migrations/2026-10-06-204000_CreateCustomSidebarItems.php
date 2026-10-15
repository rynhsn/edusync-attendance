<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomSidebarItems extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('custom_sidebar_items')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'parent_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'parent_key' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'label' => ['type' => 'VARCHAR', 'constraint' => 80],
            'url' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
            'icon' => ['type' => 'VARCHAR', 'constraint' => 64, 'default' => 'bi-folder'],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('parent_id');
        $this->forge->createTable('custom_sidebar_items');
    }

    public function down()
    {
        $this->forge->dropTable('custom_sidebar_items', true);
    }
}
