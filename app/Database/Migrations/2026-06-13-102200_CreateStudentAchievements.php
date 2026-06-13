<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentAchievements extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36],
            "nama_kegiatan" => ["type" => "VARCHAR", "constraint" => 255],
            "tingkat" => ["type" => "ENUM", "constraint" => ["lokal", "nasional", "internasional"]],
            "prestasi" => ["type" => "VARCHAR", "constraint" => 255],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("student_achievements");
    
    }

    public function down()
    {
        $this->forge->dropTable('student_achievements');
    }
}
