<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudyPrograms extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "kode_prodi" => ["type" => "VARCHAR", "constraint" => 20],
            "nama_prodi" => ["type" => "VARCHAR", "constraint" => 150],
            "jenjang" => ["type" => "VARCHAR", "constraint" => 10],
            "akreditasi" => ["type" => "VARCHAR", "constraint" => 10, "null" => true],
            "status_aktif" => ["type" => "BOOLEAN", "default" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("study_programs");
    
    }

    public function down()
    {
        $this->forge->dropTable('study_programs');
    }
}
