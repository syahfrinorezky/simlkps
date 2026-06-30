<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToIntellectualProperties extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $fields = [];
        
        if (!$db->fieldExists('kategori', 'intellectual_properties')) {
            $fields['kategori'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'lecturer_id'
            ];
        }
        if (!$db->fieldExists('nomor_hki', 'intellectual_properties')) {
            $fields['nomor_hki'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'nomor_pendaftaran'
            ];
        }
        if (!$db->fieldExists('penerbit', 'intellectual_properties')) {
            $fields['penerbit'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'nomor_hki'
            ];
        }
        if (!$db->fieldExists('isbn', 'intellectual_properties')) {
            $fields['isbn'] = [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'penerbit'
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('intellectual_properties', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $fields = [];
        if ($db->fieldExists('kategori', 'intellectual_properties')) $fields[] = 'kategori';
        if ($db->fieldExists('nomor_hki', 'intellectual_properties')) $fields[] = 'nomor_hki';
        if ($db->fieldExists('penerbit', 'intellectual_properties')) $fields[] = 'penerbit';
        if ($db->fieldExists('isbn', 'intellectual_properties')) $fields[] = 'isbn';
        
        if (!empty($fields)) {
            $this->forge->dropColumn('intellectual_properties', $fields);
        }
    }
}
