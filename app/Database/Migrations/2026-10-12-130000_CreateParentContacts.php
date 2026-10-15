<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParentContacts extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('master_orang_tua')) {
            $this->forge->addField([
                'orang_tua_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'nama_orang_tua' => ['type' => 'VARCHAR', 'constraint' => 100],
                'no_whatsapp' => ['type' => 'VARCHAR', 'constraint' => 30],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('orang_tua_id', true);
            $this->forge->addKey('no_whatsapp');
            $this->forge->createTable('master_orang_tua');
        }

        if (! $this->db->fieldExists('orang_tua_id', 'master_siswa')) {
            $this->forge->addColumn('master_siswa', [
                'orang_tua_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'nama_siswa',
                ],
            ]);
        }

        $hasLegacyName = $this->db->fieldExists('nama_orang_tua', 'master_siswa');
        $hasLegacyPhone = $this->db->fieldExists('no_whatsapp_orang_tua', 'master_siswa');
        if ($hasLegacyName && $hasLegacyPhone) {
            $legacyContacts = $this->db->table('master_siswa')
                ->select('siswa_id, nama_orang_tua, no_whatsapp_orang_tua')
                ->where('orang_tua_id', null)
                ->groupStart()
                    ->where('nama_orang_tua !=', '')
                    ->orWhere('no_whatsapp_orang_tua !=', '')
                ->groupEnd()
                ->get()
                ->getResultArray();

            foreach ($legacyContacts as $contact) {
                if (trim((string) $contact['no_whatsapp_orang_tua']) === '') {
                    continue;
                }
                $this->db->table('master_orang_tua')->insert([
                    'nama_orang_tua' => trim((string) $contact['nama_orang_tua']) ?: 'Orang Tua/Wali',
                    'no_whatsapp' => trim((string) $contact['no_whatsapp_orang_tua']),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $this->db->table('master_siswa')
                    ->where('siswa_id', $contact['siswa_id'])
                    ->update(['orang_tua_id' => $this->db->insertID()]);
            }
        }

        foreach (['no_whatsapp_orang_tua', 'nama_orang_tua'] as $legacyField) {
            if ($this->db->fieldExists($legacyField, 'master_siswa')) {
                $this->forge->dropColumn('master_siswa', $legacyField);
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('orang_tua_id', 'master_siswa')) {
            $this->forge->dropColumn('master_siswa', 'orang_tua_id');
        }
        $this->forge->dropTable('master_orang_tua', true);
    }
}
