<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenjangToForeignStudents extends Migration
{
    public function up()
    {
        $fields = [
            'jenjang' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'negara_asal',
            ],
        ];
        $this->forge->addColumn('foreign_students', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('foreign_students', ['jenjang']);
    }
}
