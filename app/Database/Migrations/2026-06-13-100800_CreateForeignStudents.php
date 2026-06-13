<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForeignStudents extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36],
            "tahun_akademik" => ["type" => "VARCHAR", "constraint" => 9],
            "mahasiswa_asing_penuh_waktu" => ["type" => "INT", "constraint" => 11],
            "mahasiswa_asing_paruh_waktu" => ["type" => "INT", "constraint" => 11],
            "negara_asal" => ["type" => "VARCHAR", "constraint" => 100],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("foreign_students");
    
    }

    public function down()
    {
        $this->forge->dropTable('foreign_students');
    }
}
