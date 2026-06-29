<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLearningIntegrations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "judul_penelitian_pkm" => ["type" => "TEXT"],
            "nama_dosen" => ["type" => "VARCHAR", "constraint" => 255],
            "course_id" => ["type" => "CHAR", "constraint" => 36],
            "bentuk_integrasi" => ["type" => "TEXT"],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->addForeignKey("course_id", "courses", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("learning_integrations");
    }

    public function down()
    {
        $this->forge->dropTable('learning_integrations');
    }
}
