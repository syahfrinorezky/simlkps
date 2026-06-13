<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoles extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "BIGINT", "constraint" => 20, "unsigned" => true, "auto_increment" => true],
            "nama" => ["type" => "VARCHAR", "constraint" => 50, "unique" => true],
            "deskripsi" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("roles");
    
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}
