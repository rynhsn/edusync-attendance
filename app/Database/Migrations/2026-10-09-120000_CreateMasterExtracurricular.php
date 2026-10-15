<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterExtracurricular extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('master_ekstrakurikuler')) {
            return;
        }

        $this->forge->addField([
            'ekstrakurikuler_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kegiatan' => ['type' => 'VARCHAR', 'constraint' => 100],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('ekstrakurikuler_id', true);
        $this->forge->addUniqueKey(['nama_kegiatan'], 'uq_master_ekstrakurikuler_name');
        $this->forge->createTable('master_ekstrakurikuler');
    }

    public function down()
    {
        $this->forge->dropTable('master_ekstrakurikuler', true);
    }
}
