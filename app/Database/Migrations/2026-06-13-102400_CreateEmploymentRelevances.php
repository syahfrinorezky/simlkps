<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmploymentRelevances extends Migration
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
            "tingkat_relevansi_tinggi" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "tingkat_relevansi_sedang" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "tingkat_relevansi_rendah" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("study_program_id", "study_programs", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("employment_relevances");
    
    }

    public function down()
    {
        $this->forge->dropTable('employment_relevances');
    }
}
