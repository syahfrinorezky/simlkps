<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToCooperations extends Migration
{
    public function up()
    {
        $fields = [
            'jenis_kerjasama' => [
                'type'       => 'ENUM',
                'constraint' => ['pendidikan', 'penelitian', 'pengabdian'],
                'after'      => 'period_id',
            ],
            'bukti_kerjasama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'after'      => 'manfaat',
            ],
            'waktu_durasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'after'      => 'bukti_kerjasama',
            ],
            'tahun_berakhir' => [
                'type'       => 'INT',
                'constraint' => 4,
                'after'      => 'waktu_durasi',
            ],
        ];
        $this->forge->addColumn('cooperations', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('cooperations', ['jenis_kerjasama', 'bukti_kerjasama', 'waktu_durasi', 'tahun_berakhir']);
    }
}
