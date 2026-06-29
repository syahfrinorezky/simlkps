<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropStatusFromReportingPeriods extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('reporting_periods', 'status');
    }

    public function down()
    {
        $this->forge->addColumn('reporting_periods', [
            'status' => ["type" => "ENUM", "constraint" => ["active", "inactive"], "default" => "inactive"],
        ]);
    }
}
