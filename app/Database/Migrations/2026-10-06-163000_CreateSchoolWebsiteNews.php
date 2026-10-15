<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolWebsiteNews extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('school_website_news')) {
            return;
        }

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'slug' => ['type' => 'VARCHAR', 'constraint' => 220],
            'category' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'tags' => ['type' => 'TEXT', 'null' => true],
            'excerpt' => ['type' => 'TEXT', 'null' => true],
            'content' => ['type' => 'LONGTEXT'],
            'image' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'draft'],
            'author' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'published_at' => ['type' => 'DATETIME', 'null' => true],
            'view_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'featured' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug', 'uq_school_website_news_slug');
        $this->forge->addKey(['status', 'published_at'], false, false, 'idx_school_website_news_publication');
        $this->forge->createTable('school_website_news');
    }

    public function down()
    {
        $this->forge->dropTable('school_website_news', true);
    }
}
