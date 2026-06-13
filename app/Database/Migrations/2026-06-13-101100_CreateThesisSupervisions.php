<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThesisSupervisions extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36],
            "nama_mahasiswa" => ["type" => "VARCHAR", "constraint" => 200],
            "nim" => ["type" => "VARCHAR", "constraint" => 30],
            "judul_tugas_akhir" => ["type" => "TEXT"],
            "tahun_akademik" => ["type" => "VARCHAR", "constraint" => 9],
            "peran" => ["type" => "ENUM", "constraint" => ["utama", "pendamping"]],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("thesis_supervisions");
    
    }

    public function down()
    {
        $this->forge->dropTable('thesis_supervisions');
    }
}
