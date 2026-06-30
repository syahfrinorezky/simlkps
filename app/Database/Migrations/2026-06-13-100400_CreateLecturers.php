<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturers extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "nidn" => ["type" => "VARCHAR", "constraint" => 20, "null" => true],
            "nidk" => ["type" => "VARCHAR", "constraint" => 20, "null" => true],
            "nama" => ["type" => "VARCHAR", "constraint" => 200],
            "gelar" => ["type" => "VARCHAR", "constraint" => 100, "null" => true],
            "gelar_depan" => ["type" => "VARCHAR", "constraint" => 50, "null" => true],
            "gelar_belakang" => ["type" => "VARCHAR", "constraint" => 100, "null" => true],
            "jabatan_akademik" => ["type" => "VARCHAR", "constraint" => 100, "null" => true],
            "pendidikan_terakhir" => ["type" => "VARCHAR", "constraint" => 50, "null" => true],
            "pendidikan_magister" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "pendidikan_doktor" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "bidang_keahlian" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "kesesuaian_kompetensi" => ["type" => "VARCHAR", "constraint" => 50, "null" => true],
            "kesesuaian_kompetensi_inti" => ["type" => "VARCHAR", "constraint" => 50, "null" => true],
            "email" => ["type" => "VARCHAR", "constraint" => 100, "null" => true],
            "telepon" => ["type" => "VARCHAR", "constraint" => 20, "null" => true],
            "foto" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "sertifikat_pendidik" => ["type" => "BOOLEAN", "default" => false],
            "sertifikat_kompetensi" => ["type" => "VARCHAR", "constraint" => 255, "null" => true],
            "mata_kuliah_diampu" => ["type" => "TEXT", "null" => true],
            "kesesuaian_bidang_mk" => ["type" => "VARCHAR", "constraint" => 50, "null" => true],
            "praktisi_industri" => ["type" => "BOOLEAN", "default" => false],
            "is_dtps" => ["type" => "BOOLEAN", "default" => false],
            "status_dosen" => ["type" => "ENUM", "constraint" => ["tetap", "tidak_tetap"]],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("lecturers");
    
    }

    public function down()
    {
        $this->forge->dropTable('lecturers');
    }
}
