<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturerWorkloads extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36],
            "semester" => ["type" => "ENUM", "constraint" => ["ganjil", "genap"]],
            "sks_pengajaran" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "sks_penelitian" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "sks_pkm" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "sks_penunjang" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "total_sks" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("lecturer_workloads");
    
    }

    public function down()
    {
        $this->forge->dropTable('lecturer_workloads');
    }
}
