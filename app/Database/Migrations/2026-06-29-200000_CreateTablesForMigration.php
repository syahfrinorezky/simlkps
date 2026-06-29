<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTablesForMigration extends Migration
{
    public function up()
    {
        // 1. student_satisfactions (Tabel 5.c)
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "aspek" => ["type" => "VARCHAR", "constraint" => 255],
            "sangat_baik" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "baik" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "cukup" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "kurang" => ["type" => "DECIMAL", "constraint" => "5,2", "default" => "0.00"],
            "rencana_tindak_lanjut" => ["type" => "TEXT", "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("student_satisfactions");

        // 2. student_researches (Tabel 6.a)
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "nama_dosen" => ["type" => "VARCHAR", "constraint" => 255],
            "tema_roadmap" => ["type" => "TEXT"],
            "nama_mahasiswa" => ["type" => "VARCHAR", "constraint" => 255],
            "judul_kegiatan" => ["type" => "TEXT"],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("student_researches");

        // 3. thesis_references (Tabel 6.b)
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "nama_dosen" => ["type" => "VARCHAR", "constraint" => 255],
            "tema_roadmap" => ["type" => "TEXT"],
            "nama_mahasiswa" => ["type" => "VARCHAR", "constraint" => 255],
            "judul_tesis_disertasi" => ["type" => "TEXT"],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("thesis_references");

        // 4. student_community_services (Tabel 7)
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "nama_dosen" => ["type" => "VARCHAR", "constraint" => 255],
            "tema_roadmap" => ["type" => "TEXT"],
            "nama_mahasiswa" => ["type" => "VARCHAR", "constraint" => 255],
            "judul_kegiatan" => ["type" => "TEXT"],
            "tahun" => ["type" => "YEAR"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("student_community_services");
    }

    public function down()
    {
        $this->forge->dropTable("student_community_services");
        $this->forge->dropTable("thesis_references");
        $this->forge->dropTable("student_researches");
        $this->forge->dropTable("student_satisfactions");
    }
}
