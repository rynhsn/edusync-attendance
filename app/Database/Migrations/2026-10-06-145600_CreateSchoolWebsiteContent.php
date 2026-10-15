<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolWebsiteContent extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('school_website_settings')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'setting_key' => ['type' => 'VARCHAR', 'constraint' => 100],
                'setting_value' => ['type' => 'LONGTEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('setting_key', 'uq_school_website_setting_key');
            $this->forge->createTable('school_website_settings');
        }

        if (! $this->db->tableExists('school_website_programs')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                'description' => ['type' => 'TEXT', 'null' => true],
                'icon' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'fas fa-book'],
                'link_url' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'link_text' => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Selengkapnya'],
                'sort_order' => ['type' => 'TINYINT', 'constraint' => 3, 'unsigned' => true, 'default' => 0],
                'show' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('school_website_programs');
        }
    }

    public function down()
    {
        $this->forge->dropTable('school_website_programs', true);
        $this->forge->dropTable('school_website_settings', true);
    }
}
