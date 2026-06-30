<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveKodeMkFromCourses extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('courses', 'kode_mk');
    }

    public function down()
    {
        $this->forge->addColumn('courses', [
            'kode_mk' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'semester'
            ]
        ]);
    }
}
