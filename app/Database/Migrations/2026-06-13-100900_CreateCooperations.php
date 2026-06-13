<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCooperations extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "partner_id" => ["type" => "CHAR", "constraint" => 36],
            "tingkat" => ["type" => "ENUM", "constraint" => ["lokal", "nasional", "internasional"]],
            "judul_kerjasama" => ["type" => "VARCHAR", "constraint" => 255],
            "manfaat" => ["type" => "TEXT", "null" => true],
            "tanggal_mulai" => ["type" => "DATE"],
            "tanggal_selesai" => ["type" => "DATE", "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("partner_id", "partners", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("cooperations");
    
    }

    public function down()
    {
        $this->forge->dropTable('cooperations');
    }
}
