<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGraduateStudyDurations extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36],
            "tahun_masuk" => ["type" => "YEAR"],
            "jumlah_diterima" => ["type" => "INT", "constraint" => 11],
            "lulus_tepat_waktu" => ["type" => "INT", "constraint" => 11],
            "rata_rata_masa_studi" => ["type" => "DECIMAL", "constraint" => "5,2"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("graduate_study_durations");
    
    }

    public function down()
    {
        $this->forge->dropTable('graduate_study_durations');
    }
}
