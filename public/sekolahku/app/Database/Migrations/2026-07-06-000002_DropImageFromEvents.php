<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropImageFromEvents extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('image', 'events')) {
            $this->forge->dropColumn('events', 'image');
        }
    }

    public function down()
    {
        $this->forge->addColumn('events', [
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'event_time',
            ],
        ]);
    }
}
