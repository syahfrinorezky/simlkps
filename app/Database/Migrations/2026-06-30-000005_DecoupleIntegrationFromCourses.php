<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DecoupleIntegrationFromCourses extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        if (!$db->fieldExists('nama_mk', 'learning_integrations')) {
            $this->forge->addColumn('learning_integrations', [
                'nama_mk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'nama_dosen',
                ],
            ]);
        }

        $db->query("
            UPDATE learning_integrations li
            LEFT JOIN courses c ON c.id = li.course_id
            SET li.nama_mk = IFNULL(c.nama_mk, '')
            WHERE li.nama_mk IS NULL OR li.nama_mk = ''
        ");

        $fkName = null;
        $fkResult = $db->query("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'learning_integrations'
              AND COLUMN_NAME = 'course_id'
              AND CONSTRAINT_SCHEMA = DATABASE()
              AND REFERENCED_TABLE_NAME = 'courses'
        ");
        if ($fkResult) {
            $rows = $fkResult->getResultArray();
            if (!empty($rows)) {
                $fkName = $rows[0]['CONSTRAINT_NAME'];
            }
        }

        if ($fkName) {
            $db->query("ALTER TABLE `learning_integrations` DROP FOREIGN KEY `{$fkName}`");
        }

        if ($db->fieldExists('course_id', 'learning_integrations')) {
            $this->forge->dropColumn('learning_integrations', 'course_id');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        if (!$db->fieldExists('course_id', 'learning_integrations')) {
            $this->forge->addColumn('learning_integrations', [
                'course_id' => [
                    'type'       => 'CHAR',
                    'constraint' => 36,
                    'null'       => true,
                    'after'      => 'nama_dosen',
                ],
            ]);
        }

        if ($db->fieldExists('nama_mk', 'learning_integrations')) {
            $this->forge->dropColumn('learning_integrations', 'nama_mk');
        }
    }
}
