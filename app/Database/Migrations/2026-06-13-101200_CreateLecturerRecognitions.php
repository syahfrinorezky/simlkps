<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturerRecognitions extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36],
            "nama_rekognisi" => ["type" => "VARCHAR", "constraint" => 255],
            "tingkat" => ["type" => "ENUM", "constraint" => ["lokal", "nasional", "internasional"]],
            "penyelenggara" => ["type" => "VARCHAR", "constraint" => 255],
            "tahun" => ["type" => "YEAR"],
            "dokumen_bukti" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("lecturer_recognitions");
    
    }

    public function down()
    {
        $this->forge->dropTable('lecturer_recognitions');
    }
}
