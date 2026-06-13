<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResearchMembers extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "research_id" => ["type" => "CHAR", "constraint" => 36],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36, "null" => true],
            "nama_mahasiswa" => ["type" => "VARCHAR", "constraint" => 200, "null" => true],
            "peran" => ["type" => "VARCHAR", "constraint" => 100],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("research_id", "researches", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("research_members");
    
    }

    public function down()
    {
        $this->forge->dropTable('research_members');
    }
}
