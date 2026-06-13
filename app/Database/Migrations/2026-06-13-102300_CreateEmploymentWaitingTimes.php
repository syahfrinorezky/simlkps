<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmploymentWaitingTimes extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "study_program_id" => ["type" => "CHAR", "constraint" => 36],
            "tahun_lulus" => ["type" => "YEAR"],
            "jumlah_lulusan" => ["type" => "INT", "constraint" => 11],
            "jumlah_terlacak" => ["type" => "INT", "constraint" => 11],
            "waktu_tunggu_kurang_6_bulan" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "waktu_tunggu_6_sampai_18_bulan" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "waktu_tunggu_lebih_18_bulan" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("employment_waiting_times");
    
    }

    public function down()
    {
        $this->forge->dropTable('employment_waiting_times');
    }
}
