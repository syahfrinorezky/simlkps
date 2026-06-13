<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReportingPeriods extends Migration
{
    public function up()
    {

        $this->forge->addField([
            "id" => ["type" => "INT", "constraint" => 10, "unsigned" => true, "auto_increment" => true],
            "nama_periode" => ["type" => "VARCHAR", "constraint" => 100],
            "tahun_akademik" => ["type" => "VARCHAR", "constraint" => 9],
            "status" => ["type" => "ENUM", "constraint" => ["active", "inactive"]],
            "created_at" => ["type" => "DATETIME", "null" => true],
            "updated_at" => ["type" => "DATETIME", "null" => true],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("reporting_periods");
    
    }

    public function down()
    {
        $this->forge->dropTable('reporting_periods');
    }
}
