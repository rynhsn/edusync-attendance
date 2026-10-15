<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterKokurikuler extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('master_kokurikuler')) {
            return;
        }

        $this->forge->addField([
            'kokurikuler_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kegiatan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('kokurikuler_id', true);
        $this->forge->addUniqueKey(['nama_kegiatan'], 'uq_master_kokurikuler_name');
        $this->forge->createTable('master_kokurikuler');
    }

    public function down()
    {
        $this->forge->dropTable('master_kokurikuler', true);
    }
}
