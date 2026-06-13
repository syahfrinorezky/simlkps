<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePublications extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36],
            "judul" => ["type" => "TEXT"],
            "jenis_publikasi" => ["type" => "VARCHAR", "constraint" => 100],
            "tingkat" => ["type" => "ENUM", "constraint" => ["lokal", "nasional", "internasional"]],
            "tahun" => ["type" => "YEAR"],
            "tautan" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("publications");
    
    }

    public function down()
    {
        $this->forge->dropTable('publications');
    }
}
