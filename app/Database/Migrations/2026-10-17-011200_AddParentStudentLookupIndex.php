<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentStudentLookupIndex extends Migration
{
    private const INDEX_NAME = 'idx_master_siswa_orang_tua_id';

    public function up()
    {
        if (! $this->db->fieldExists('orang_tua_id', 'master_siswa')) {
            throw new \RuntimeException('Kolom master_siswa.orang_tua_id belum tersedia.');
        }

        foreach ($this->db->getIndexData('master_siswa') as $index) {
            if (($index->fields[0] ?? null) === 'orang_tua_id') {
                return;
            }
        }

        $this->forge->addKey('orang_tua_id', false, false, self::INDEX_NAME);
        $this->forge->processIndexes('master_siswa');
    }

    public function down()
    {
        foreach ($this->db->getIndexData('master_siswa') as $name => $index) {
            if ($name === self::INDEX_NAME && in_array('orang_tua_id', $index->fields, true)) {
                $this->forge->dropKey('master_siswa', self::INDEX_NAME, false);
                break;
            }
        }
    }
}
