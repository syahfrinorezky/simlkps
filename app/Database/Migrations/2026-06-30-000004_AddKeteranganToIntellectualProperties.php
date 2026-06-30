<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKeteranganToIntellectualProperties extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        if (!$db->fieldExists('keterangan', 'intellectual_properties')) {
            $this->forge->addColumn('intellectual_properties', [
                'keterangan' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'tahun'
                ]
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('keterangan', 'intellectual_properties')) {
            $this->forge->dropColumn('intellectual_properties', 'keterangan');
        }
    }
}
