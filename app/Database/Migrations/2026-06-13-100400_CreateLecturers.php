<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturers extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "nidn" => ["type" => "VARCHAR", "constraint" => 20],
            "nama" => ["type" => "VARCHAR", "constraint" => 200],
            "gelar" => ["type" => "VARCHAR", "constraint" => 100],
            "jabatan_akademik" => ["type" => "VARCHAR", "constraint" => 100],
            "pendidikan_terakhir" => ["type" => "VARCHAR", "constraint" => 50],
            "bidang_keahlian" => ["type" => "VARCHAR", "constraint" => 255],
            "sertifikat_pendidik" => ["type" => "BOOLEAN", "default" => false],
            "praktisi_industri" => ["type" => "BOOLEAN", "default" => false],
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
