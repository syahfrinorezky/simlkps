<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePasswordResets extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "email" => ["type" => "VARCHAR", "constraint" => 150],
            "token" => ["type" => "VARCHAR", "constraint" => 255],
            "created_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("email");
        $this->forge->createTable("password_resets");
    
    }

    public function down()
    {
        $this->forge->dropTable('password_resets');
    }
}
