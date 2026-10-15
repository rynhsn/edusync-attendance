<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolWebsiteMessages extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('school_website_messages')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'email' => ['type' => 'VARCHAR', 'constraint' => 254],
            'subject' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => true],
            'message' => ['type' => 'TEXT'],
            'is_read' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['is_read', 'created_at'], false, false, 'idx_school_website_messages_read');
        $this->forge->createTable('school_website_messages');
    }

    public function down()
    {
        $this->forge->dropTable('school_website_messages', true);
    }
}
