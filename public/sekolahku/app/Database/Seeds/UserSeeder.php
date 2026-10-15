<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->truncate();
        $this->db->table('users')->insert([
            'username'   => 'administrator',
            'email'      => 'admin@sekolahku.sch.id',
            'password'   => password_hash('12345', PASSWORD_BCRYPT),
            'full_name'  => 'Administrator',
            'role'       => 'superadmin',
            'is_active'  => 1,
        ]);
    }
}
