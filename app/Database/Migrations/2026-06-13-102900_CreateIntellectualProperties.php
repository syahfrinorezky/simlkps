<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIntellectualProperties extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "lecturer_id" => ["type" => "CHAR", "constraint" => 36],
            "judul_hki" => ["type" => "VARCHAR", "constraint" => 255],
            "jenis_hki" => ["type" => "VARCHAR", "constraint" => 100],
            "nomor_pendaftaran" => ["type" => "VARCHAR", "constraint" => 100, "null" => true],
            "status" => ["type" => "VARCHAR", "constraint" => 50],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("lecturer_id", "lecturers", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("intellectual_properties");
    
    }

    public function down()
    {
        $this->forge->dropTable('intellectual_properties');
    }
}
