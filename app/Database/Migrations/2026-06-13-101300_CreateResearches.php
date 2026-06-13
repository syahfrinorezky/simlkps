<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResearches extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "judul_penelitian" => ["type" => "TEXT"],
            "tema_roadmap" => ["type" => "VARCHAR", "constraint" => 255],
            "sumber_dana" => ["type" => "VARCHAR", "constraint" => 255],
            "jumlah_dana" => ["type" => "DECIMAL", "constraint" => "18,2"],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("researches");
    
    }

    public function down()
    {
        $this->forge->dropTable('researches');
    }
}
