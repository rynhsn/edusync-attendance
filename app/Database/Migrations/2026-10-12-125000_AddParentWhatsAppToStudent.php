<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentWhatsAppToStudent extends Migration
{
    public function up()
    {
        $fields = [];
        if (! $this->db->fieldExists('nama_orang_tua', 'master_siswa')) {
            $fields['nama_orang_tua'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'nama_siswa',
            ];
        }
        if (! $this->db->fieldExists('no_whatsapp_orang_tua', 'master_siswa')) {
            $fields['no_whatsapp_orang_tua'] = [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'nama_orang_tua',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('master_siswa', $fields);
        }
    }

    public function down()
    {
        foreach (['no_whatsapp_orang_tua', 'nama_orang_tua'] as $field) {
            if ($this->db->fieldExists($field, 'master_siswa')) {
                $this->forge->dropColumn('master_siswa', $field);
            }
        }
    }
}
