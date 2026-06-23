<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterStudentTables extends Migration
{
    public function up()
    {
        $this->forge->addColumn('student_admissions', [
            'mahasiswa_aktif_reguler' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_baru_transfer'
            ],
            'mahasiswa_aktif_transfer' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_aktif_reguler'
            ]
        ]);
        $this->forge->dropColumn('student_admissions', 'mahasiswa_aktif');

        $this->forge->addColumn('foreign_students', [
            'mahasiswa_aktif_ts2' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'tahun_akademik'
            ],
            'mahasiswa_aktif_ts1' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_aktif_ts2'
            ],
            'mahasiswa_aktif_ts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_aktif_ts1'
            ],
            'mahasiswa_asing_full_ts2' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_aktif_ts'
            ],
            'mahasiswa_asing_full_ts1' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_asing_full_ts2'
            ],
            'mahasiswa_asing_full_ts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_asing_full_ts1'
            ],
            'mahasiswa_asing_part_ts2' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_asing_full_ts'
            ],
            'mahasiswa_asing_part_ts1' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_asing_part_ts2'
            ],
            'mahasiswa_asing_part_ts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_asing_part_ts1'
            ]
        ]);
        $this->forge->dropColumn('foreign_students', [
            'mahasiswa_asing_penuh_waktu',
            'mahasiswa_asing_paruh_waktu',
            'negara_asal',
            'jenjang'
        ]);
    }

    public function down()
    {
        $this->forge->addColumn('student_admissions', [
            'mahasiswa_aktif' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_baru_transfer'
            ]
        ]);
        $this->forge->dropColumn('student_admissions', ['mahasiswa_aktif_reguler', 'mahasiswa_aktif_transfer']);

        $this->forge->addColumn('foreign_students', [
            'mahasiswa_asing_penuh_waktu' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'tahun_akademik'
            ],
            'mahasiswa_asing_paruh_waktu' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'mahasiswa_asing_penuh_waktu'
            ],
            'negara_asal' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'after' => 'mahasiswa_asing_paruh_waktu'
            ],
            'jenjang' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'negara_asal'
            ]
        ]);
        $this->forge->dropColumn('foreign_students', [
            'mahasiswa_aktif_ts2',
            'mahasiswa_aktif_ts1',
            'mahasiswa_aktif_ts',
            'mahasiswa_asing_full_ts2',
            'mahasiswa_asing_full_ts1',
            'mahasiswa_asing_full_ts',
            'mahasiswa_asing_part_ts2',
            'mahasiswa_asing_part_ts1',
            'mahasiswa_asing_part_ts'
        ]);
    }
}
