<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToLecturerWorkloads extends Migration
{
    public function up()
    {
        $fields = [
            'sks_ps_lain_dalam_pt' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.00',
                'after'      => 'sks_pengajaran'
            ],
            'sks_ps_luar_pt' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '0.00',
                'after'      => 'sks_ps_lain_dalam_pt'
            ]
        ];
        $this->forge->addColumn('lecturer_workloads', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('lecturer_workloads', 'sks_ps_lain_dalam_pt');
        $this->forge->dropColumn('lecturer_workloads', 'sks_ps_luar_pt');
    }
}
