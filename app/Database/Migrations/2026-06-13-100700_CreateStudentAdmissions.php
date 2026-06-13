<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentAdmissions extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36],
            "tahun_akademik" => ["type" => "VARCHAR", "constraint" => 9],
            "daya_tampung" => ["type" => "INT", "constraint" => 11],
            "jumlah_pendaftar" => ["type" => "INT", "constraint" => 11],
            "jumlah_lulus_seleksi" => ["type" => "INT", "constraint" => 11],
            "mahasiswa_baru_reguler" => ["type" => "INT", "constraint" => 11],
            "mahasiswa_baru_transfer" => ["type" => "INT", "constraint" => 11],
            "mahasiswa_aktif" => ["type" => "INT", "constraint" => 11],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("student_admissions");
    
    }

    public function down()
    {
        $this->forge->dropTable('student_admissions');
    }
}
