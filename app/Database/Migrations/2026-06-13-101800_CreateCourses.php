<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourses extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "kode_mk" => ["type" => "VARCHAR", "constraint" => 20],
            "nama_mk" => ["type" => "VARCHAR", "constraint" => 255],
            "semester" => ["type" => "INT", "constraint" => 11],
            "sks_teori" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "sks_praktikum" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "sks_seminar" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("courses");
    
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
