<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserCodeAndPlan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'user_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'email',
            ],
            'plan' => [
                'type'       => 'ENUM',
                'constraint' => ['free', 'paid'],
                'default'    => 'free',
                'after'      => 'role',
            ],
            'last_code_change' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'plan',
            ],
        ]);

        // Add unique index on user_code
        $this->db->query('ALTER TABLE users ADD UNIQUE INDEX idx_user_code (user_code)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE users DROP INDEX idx_user_code');

        $this->forge->dropColumn('users', ['user_code', 'plan', 'last_code_change']);
    }
}
