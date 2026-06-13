<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGraduateGPAs extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36],
            "tahun_lulus" => ["type" => "YEAR"],
            "jumlah_lulusan" => ["type" => "INT", "constraint" => 11],
            "ipk_min" => ["type" => "DECIMAL", "constraint" => "4,2"],
            "ipk_rata_rata" => ["type" => "DECIMAL", "constraint" => "4,2"],
            "ipk_max" => ["type" => "DECIMAL", "constraint" => "4,2"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("graduate_gpas");
    
    }

    public function down()
    {
        $this->forge->dropTable('graduate_gpas');
    }
}
