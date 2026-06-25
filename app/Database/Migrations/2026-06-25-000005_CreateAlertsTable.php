<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAlertsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'group_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'elder_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['missed_checkin', 'help', 'emergency'],
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'is_resolved' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'resolved_by' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('group_id');
        $this->forge->addKey('elder_id');
        $this->forge->addKey('type');
        $this->forge->addKey('is_resolved');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('group_id', 'family_groups', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('elder_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('resolved_by', 'users', 'id', 'SET NULL', 'SET NULL');

        $this->forge->createTable('alerts', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('alerts', true);
    }
}
