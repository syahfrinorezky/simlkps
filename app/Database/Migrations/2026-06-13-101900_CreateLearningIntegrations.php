<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLearningIntegrations extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "course_id" => ["type" => "CHAR", "constraint" => 36],
            "jenis_integrasi" => ["type" => "ENUM", "constraint" => ["research", "community_service"]],
            "judul" => ["type" => "VARCHAR", "constraint" => 255],
            "deskripsi" => ["type" => "TEXT"],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("course_id", "courses", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("learning_integrations");
    
    }

    public function down()
    {
        $this->forge->dropTable('learning_integrations');
    }
}
