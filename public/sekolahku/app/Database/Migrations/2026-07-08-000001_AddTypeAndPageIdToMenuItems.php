<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeAndPageIdToMenuItems extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('type', 'menu_items')) {
            $this->forge->addColumn('menu_items', [
                'type'    => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'custom', 'after' => 'status'],
                'page_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'after' => 'type'],
            ]);
        }

        if (! $this->db->fieldExists('page_id', 'menu_items')) {
            $this->forge->addForeignKey('page_id', 'pages', 'id', 'SET NULL', 'CASCADE');
        }
    }

    public function down()
    {
        $this->forge->dropForeignKey('menu_items', 'menu_items_page_id_foreign');
        $this->forge->dropColumn('menu_items', 'type');
        $this->forge->dropColumn('menu_items', 'page_id');
    }
}
