<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePartners extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "nama_mitra" => ["type" => "VARCHAR", "constraint" => 255],
            "jenis_mitra" => ["type" => "VARCHAR", "constraint" => 100],
            "negara" => ["type" => "VARCHAR", "constraint" => 100],
            "alamat" => ["type" => "TEXT", "null" => true],
            "kontak" => ["type" => "VARCHAR", "constraint" => 100, "null" => true],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("partners");
    
    }

    public function down()
    {
        $this->forge->dropTable('partners');
    }
}
