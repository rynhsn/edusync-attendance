<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagesAndMenuItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'title'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'content'          => ['type' => 'LONGTEXT', 'null' => true],
            'excerpt'          => ['type' => 'TEXT', 'null' => true],
            'image'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'           => ['type' => 'ENUM', 'constraint' => ['draft', 'published'], 'default' => 'draft'],
            'meta_title'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'meta_description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('pages', true);

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'parent_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'title'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'url'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'target'      => ['type' => 'ENUM', 'constraint' => ['_self', '_blank'], 'default' => '_self'],
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'section_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'status'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('parent_id', 'menu_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('menu_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('menu_items', true);
        $this->forge->dropTable('pages', true);
    }
}
