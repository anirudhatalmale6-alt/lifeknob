<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'email'      => 'admin@lifeknob.com',
            'password'   => password_hash('admin123', PASSWORD_DEFAULT),
            'name'       => 'Admin',
            'role'       => 'admin',
            'timezone'   => 'UTC',
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('users')->insert($data);
    }
}
