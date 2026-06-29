<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ResetAndRecreateOldTables extends Migration
{
    public function up()
    {
        // Drop existing foreign keys/tables in correct order to avoid FK errors
        $this->db->disableForeignKeyChecks();

        $this->forge->dropTable('learning_integrations', true);
        $this->forge->dropTable('courses', true);
        $this->forge->dropTable('fund_usages', true);

        $this->db->enableForeignKeyChecks();

        // 1. Recreate fund_usages
        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "jenis_penggunaan" => ["type" => "VARCHAR", "constraint" => 255],
            "kategori" => ["type" => "VARCHAR", "constraint" => 100],
            "upps_ts_2" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "upps_ts_1" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "upps_ts" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "upps_rata_rata" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "ps_ts_2" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "ps_ts_1" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "ps_ts" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "ps_rata_rata" => ["type" => "DECIMAL", "constraint" => "18,2", "default" => "0.00"],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("fund_usages");

        // 2. Recreate courses
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

        // 3. Recreate learning_integrations
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
        // Don't need to do anything as we drop them in up() if migrating again
    }
}
