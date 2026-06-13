<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSupportingDocuments extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "CHAR", "constraint" => 36],
            "period_id" => ["type" => "INT", "constraint" => 10, "unsigned" => true],
            "kategori_dokumen" => ["type" => "VARCHAR", "constraint" => 100],
            "nama_dokumen" => ["type" => "VARCHAR", "constraint" => 255],
            "file_path" => ["type" => "VARCHAR", "constraint" => 255],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("period_id", "reporting_periods", "id", "CASCADE", "CASCADE");
        $this->forge->createTable("supporting_documents");
    
    }

    public function down()
    {
        $this->forge->dropTable('supporting_documents');
    }
}
