<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSidebarBuiltInParentKey extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('custom_sidebar_items')
            && ! $this->db->fieldExists('parent_key', 'custom_sidebar_items')) {
            $this->forge->addColumn('custom_sidebar_items', [
                'parent_key' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'parent_id'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('custom_sidebar_items')
            && $this->db->fieldExists('parent_key', 'custom_sidebar_items')) {
            $this->forge->dropColumn('custom_sidebar_items', 'parent_key');
        }
    }
}
