<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToLecturers extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('lecturers')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('is_dtps', 'lecturers')) {
            $fields['is_dtps'] = [
                'type' => 'BOOLEAN',
                'default' => false,
                'after' => 'praktisi_industri'
            ];
        }

        if (!$this->db->fieldExists('nidk', 'lecturers')) {
            $fields['nidk'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'nidn'
            ];
        }

        if (!$this->db->fieldExists('gelar_depan', 'lecturers')) {
            $fields['gelar_depan'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'nama'
            ];
        }

        if (!$this->db->fieldExists('gelar_belakang', 'lecturers')) {
            $fields['gelar_belakang'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'gelar_depan'
            ];
        }

        if (!$this->db->fieldExists('pendidikan_magister', 'lecturers')) {
            $fields['pendidikan_magister'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'pendidikan_terakhir'
            ];
        }

        if (!$this->db->fieldExists('pendidikan_doktor', 'lecturers')) {
            $fields['pendidikan_doktor'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'pendidikan_magister'
            ];
        }

        if (!$this->db->fieldExists('kesesuaian_kompetensi', 'lecturers')) {
            $fields['kesesuaian_kompetensi'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'bidang_keahlian'
            ];
        }

        if (!$this->db->fieldExists('kesesuaian_kompetensi_inti', 'lecturers')) {
            $fields['kesesuaian_kompetensi_inti'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'kesesuaian_kompetensi'
            ];
        }

        if (!$this->db->fieldExists('email', 'lecturers')) {
            $fields['email'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'kesesuaian_kompetensi_inti'
            ];
        }

        if (!$this->db->fieldExists('telepon', 'lecturers')) {
            $fields['telepon'] = [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'email'
            ];
        }

        if (!$this->db->fieldExists('foto', 'lecturers')) {
            $fields['foto'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'telepon'
            ];
        }

        if (!$this->db->fieldExists('sertifikat_kompetensi', 'lecturers')) {
            $fields['sertifikat_kompetensi'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'sertifikat_pendidik'
            ];
        }

        if (!$this->db->fieldExists('mata_kuliah_diampu', 'lecturers')) {
            $fields['mata_kuliah_diampu'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'sertifikat_kompetensi'
            ];
        }

        if (!$this->db->fieldExists('kesesuaian_bidang_mk', 'lecturers')) {
            $fields['kesesuaian_bidang_mk'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'mata_kuliah_diampu'
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('lecturers', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('lecturers')) {
            return;
        }

        $columns = [
            'is_dtps',
            'nidk',
            'gelar_depan',
            'gelar_belakang',
            'pendidikan_magister',
            'pendidikan_doktor',
            'kesesuaian_kompetensi',
            'kesesuaian_kompetensi_inti',
            'email',
            'telepon',
            'foto',
            'sertifikat_kompetensi',
            'mata_kuliah_diampu',
            'kesesuaian_bidang_mk'
        ];

        foreach ($columns as $column) {
            if ($this->db->fieldExists($column, 'lecturers')) {
                $this->forge->dropColumn('lecturers', $column);
            }
        }
    }
}
