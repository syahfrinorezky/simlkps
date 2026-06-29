<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFundUsages extends Migration
{
    public function up()
    {
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
    }

    public function down()
    {
        $this->forge->dropTable('fund_usages');
    }
}
