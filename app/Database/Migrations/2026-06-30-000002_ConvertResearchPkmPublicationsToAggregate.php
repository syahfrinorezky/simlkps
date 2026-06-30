<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertResearchPkmPublicationsToAggregate extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();


        $this->forge->dropTable('research_members', true);
        $this->forge->dropTable('community_service_members', true);

        $this->forge->dropTable('researches', true);
        $this->forge->dropTable('community_services', true);
        $this->forge->dropTable('publications', true);

        $this->db->enableForeignKeyChecks();

        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'period_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'sumber_dana' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jumlah_ts2' => ['type' => 'INT', 'default' => 0],
            'jumlah_ts1' => ['type' => 'INT', 'default' => 0],
            'jumlah_ts' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('period_id', 'reporting_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('researches');

        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'period_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'sumber_dana' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jumlah_ts2' => ['type' => 'INT', 'default' => 0],
            'jumlah_ts1' => ['type' => 'INT', 'default' => 0],
            'jumlah_ts' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('period_id', 'reporting_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('community_services');

        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36],
            'period_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'kategori_publikasi' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jumlah_ts2' => ['type' => 'INT', 'default' => 0],
            'jumlah_ts1' => ['type' => 'INT', 'default' => 0],
            'jumlah_ts' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('period_id', 'reporting_periods', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('publications');
    }

    public function down()
    {
    }
}
