<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommunityServices extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36],
            "judul_kegiatan" => ["type" => "TEXT"],
            "tema_roadmap" => ["type" => "VARCHAR", "constraint" => 255],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("community_services");
    
    }

    public function down()
    {
        $this->forge->dropTable('community_services');
    }
}
