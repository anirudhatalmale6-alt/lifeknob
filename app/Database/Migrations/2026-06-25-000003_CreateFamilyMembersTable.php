<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFamilyMembersTable extends Migration
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
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['elder', 'family'],
            ],
            'joined_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['group_id', 'user_id']);
        $this->forge->addKey('group_id');
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('group_id', 'family_groups', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('family_members', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('family_members', true);
    }
}
