<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToGalleries extends Migration
{
    public function up()
    {
        $this->forge->addColumn('galleries', [
            'updated_at' => [
                'type'    => 'DATETIME',
                'default' => null,
            ],
        ]);
        $this->db->query('ALTER TABLE galleries MODIFY updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
    }

    public function down()
    {
        $this->forge->dropColumn('galleries', 'updated_at');
    }
}
