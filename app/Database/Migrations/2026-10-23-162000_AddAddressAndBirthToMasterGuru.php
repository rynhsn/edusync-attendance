<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressAndBirthToMasterGuru extends Migration
{
    public function up()
    {
        $fieldsToAdd = [];

        if (! $this->db->fieldExists('tempat_lahir', 'master_guru')) {
            $fieldsToAdd['tempat_lahir'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'guru_telepon',
            ];
        }

        if (! $this->db->fieldExists('tanggal_lahir', 'master_guru')) {
            $fieldsToAdd['tanggal_lahir'] = [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'tempat_lahir',
            ];
        }

        if (! $this->db->fieldExists('alamat', 'master_guru')) {
            $fieldsToAdd['alamat'] = [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'tanggal_lahir',
            ];
        }

        if (! $this->db->fieldExists('rt', 'master_guru')) {
            $fieldsToAdd['rt'] = [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'alamat',
            ];
        }

        if (! $this->db->fieldExists('rw', 'master_guru')) {
            $fieldsToAdd['rw'] = [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
                'after'      => 'rt',
            ];
        }

        if (! empty($fieldsToAdd)) {
            $this->forge->addColumn('master_guru', $fieldsToAdd);
        }
    }

    public function down()
    {
        $columns = ['rw', 'rt', 'alamat', 'tanggal_lahir', 'tempat_lahir'];
        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'master_guru')) {
                $this->forge->dropColumn('master_guru', $column);
            }
        }
    }
}
