<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCheckInSettingsTable extends Migration
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
            ],
            'frequency_hours' => [
                'type'    => 'INT',
                'default' => 24,
            ],
            'reminder_minutes' => [
                'type'    => 'INT',
                'default' => 30,
            ],
            'alert_delay_minutes' => [
                'type'    => 'INT',
                'default' => 60,
            ],
            'quiet_hours_start' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'quiet_hours_end' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('check_in_settings', false, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('check_in_settings', true);
    }
}
