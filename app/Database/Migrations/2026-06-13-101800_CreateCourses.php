<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourses extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "semester" => ["type" => "INT", "constraint" => 11],
            "kode_mk" => ["type" => "VARCHAR", "constraint" => 50],
            "nama_mk" => ["type" => "VARCHAR", "constraint" => 255],
            "mk_kompetensi" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            "sks_kuliah" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "sks_seminar" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "sks_praktikum" => ["type" => "INT", "constraint" => 11, "default" => 0],
            "konversi_jam" => ["type" => "DECIMAL", "constraint" => "6,2", "default" => "0.00"],
            "cpl_sikap" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            "cpl_pengetahuan" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            "cpl_keterampilan_umum" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            "cpl_keterampilan_khusus" => ["type" => "TINYINT", "constraint" => 1, "default" => 0],
            "dokumen_rencana_pembelajaran" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "unit_penyelenggara" => ["type" => "VARCHAR", "constraint" => 255],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("courses");
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
