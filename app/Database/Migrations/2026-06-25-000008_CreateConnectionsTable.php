<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConnectionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'comment'  => 'The connector (person who entered the code)',
            ],
            'connected_to' => [
                'type'     => 'INT',
                'unsigned' => true,
                'comment'  => 'The code owner (person whose code was entered)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'connected_to'], 'unique_connection');
        $this->forge->addKey('user_id', false, false, 'idx_conn_user');
        $this->forge->addKey('connected_to', false, false, 'idx_conn_target');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('connected_to', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('connections', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('connections', true);
    }
}
