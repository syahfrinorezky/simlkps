<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CleanUpUnusedFieldsAndColumns extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if ($db->fieldExists('semester', 'lecturer_workloads')) {
            $this->forge->dropColumn('lecturer_workloads', 'semester');
        }


        if ($db->fieldExists('penyelenggara', 'lecturer_recognitions')) {
            $this->forge->dropColumn('lecturer_recognitions', 'penyelenggara');
        }

        if ($db->fieldExists('sumber_sitasi', 'citations')) {
            $this->forge->dropColumn('citations', 'sumber_sitasi');
        }

        $ipColumns = ['jenis_hki', 'nomor_pendaftaran', 'nomor_hki', 'penerbit', 'isbn', 'status'];
        foreach ($ipColumns as $col) {
            if ($db->fieldExists($col, 'intellectual_properties')) {
                $this->forge->dropColumn('intellectual_properties', $col);
            }
        }

        if ($db->fieldExists('kategori', 'fund_usages')) {
            $this->forge->dropColumn('fund_usages', 'kategori');
        }
        if ($db->fieldExists('semester', 'courses')) {
            $this->forge->dropColumn('courses', 'semester');
        }
    }

    public function down()
    {
        $this->forge->addColumn('lecturer_workloads', [
            'semester' => ['type' => 'ENUM', 'constraint' => ['ganjil', 'genap'], 'default' => 'ganjil']
        ]);

        $this->forge->addColumn('lecturer_recognitions', [
            'penyelenggara' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true]
        ]);

        $this->forge->addColumn('citations', [
            'sumber_sitasi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]
        ]);

        $this->forge->addColumn('intellectual_properties', [
            'jenis_hki' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nomor_pendaftaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nomor_hki' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'penerbit' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'isbn' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);

        $this->forge->addColumn('fund_usages', [
            'kategori' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true]
        ]);

        $this->forge->addColumn('courses', [
            'semester' => ['type' => 'INT', 'constraint' => 11, 'default' => 1]
        ]);
    }
}
