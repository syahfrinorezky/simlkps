<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommunityServiceMembers extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "community_service_id" => ["type" => "CHAR", "constraint" => 36],
            "nama_mahasiswa" => ["type" => "VARCHAR", "constraint" => 200],
            "nim" => ["type" => "VARCHAR", "constraint" => 30],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("community_service_id", "community_services", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("community_service_members");
    
    }

    public function down()
    {
        $this->forge->dropTable('community_service_members');
    }
}
