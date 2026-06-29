<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumns extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('lecturers')) {
            $fields = $this->db->getFieldNames('lecturers');
            if (!in_array('is_dtps', $fields)) {
                $this->forge->addColumn('lecturers', [
                    'is_dtps' => [
                        'type' => 'BOOLEAN',
                        'default' => false,
                        'after' => 'status_dosen'
                    ]
                ]);
            }
        }

        if ($this->db->tableExists('community_services')) {
            $fields = $this->db->getFieldNames('community_services');
            if (!in_array('sumber_dana', $fields)) {
                $this->forge->addColumn('community_services', [
                    'sumber_dana' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'after' => 'tema_roadmap'
                    ]
                ]);
            }
            if (!in_array('jumlah_dana', $fields)) {
                $this->forge->addColumn('community_services', [
                    'jumlah_dana' => [
                        'type' => 'DECIMAL',
                        'constraint' => '18,2',
                        'default' => 0,
                        'after' => 'sumber_dana'
                    ]
                ]);
            }
        }

        if ($this->db->tableExists('publications')) {
            $fields = $this->db->getFieldNames('publications');
            if (!in_array('kategori_publikasi', $fields)) {
                $this->forge->addColumn('publications', [
                    'kategori_publikasi' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                        'after' => 'judul'
                    ]
                ]);
            }
            if (!in_array('penerbit', $fields)) {
                $this->forge->addColumn('publications', [
                    'penerbit' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'after' => 'kategori_publikasi'
                    ]
                ]);
            }
            if (!in_array('nomor_issn_isbn', $fields)) {
                $this->forge->addColumn('publications', [
                    'nomor_issn_isbn' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                        'after' => 'penerbit'
                    ]
                ]);
            }
        }

        if ($this->db->tableExists('intellectual_properties')) {
            $fields = $this->db->getFieldNames('intellectual_properties');
            if (!in_array('kategori', $fields)) {
                $this->forge->addColumn('intellectual_properties', [
                    'kategori' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                        'null' => true,
                        'after' => 'judul_hki'
                    ]
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('lecturers')) {
            $fields = $this->db->getFieldNames('lecturers');
            if (in_array('is_dtps', $fields)) {
                $this->forge->dropColumn('lecturers', 'is_dtps');
            }
        }

        if ($this->db->tableExists('community_services')) {
            $fields = $this->db->getFieldNames('community_services');
            if (in_array('sumber_dana', $fields)) {
                $this->forge->dropColumn('community_services', 'sumber_dana');
            }
            if (in_array('jumlah_dana', $fields)) {
                $this->forge->dropColumn('community_services', 'jumlah_dana');
            }
        }

        if ($this->db->tableExists('publications')) {
            $fields = $this->db->getFieldNames('publications');
            if (in_array('kategori_publikasi', $fields)) {
                $this->forge->dropColumn('publications', 'kategori_publikasi');
            }
            if (in_array('penerbit', $fields)) {
                $this->forge->dropColumn('publications', 'penerbit');
            }
            if (in_array('nomor_issn_isbn', $fields)) {
                $this->forge->dropColumn('publications', 'nomor_issn_isbn');
            }
        }

        if ($this->db->tableExists('intellectual_properties')) {
            $fields = $this->db->getFieldNames('intellectual_properties');
            if (in_array('kategori', $fields)) {
                $this->forge->dropColumn('intellectual_properties', 'kategori');
            }
        }
    }
}
