<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixLecturersAndTables extends Migration
{
    public function up()
    {
        $this->forge->addColumn('lecturers', [
            'pendidikan_magister'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'pendidikan_terakhir'],
            'pendidikan_doktor'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'pendidikan_magister'],
            'kesesuaian_kompetensi' => ['type' => 'ENUM', 'constraint' => ['sesuai', 'tidak_sesuai'], 'null' => true, 'after' => 'bidang_keahlian'],
            'sertifikat_kompetensi' => ['type' => 'TEXT', 'null' => true, 'after' => 'sertifikat_pendidik'],
            'mata_kuliah_diampu'    => ['type' => 'TEXT', 'null' => true, 'after' => 'sertifikat_kompetensi'],
            'kesesuaian_bidang_mk'  => ['type' => 'ENUM', 'constraint' => ['sesuai', 'tidak_sesuai'], 'null' => true, 'after' => 'mata_kuliah_diampu'],
        ]);

        $this->forge->addColumn('thesis_supervisions', [
            'bimbingan_ps_ts2'      => ['type' => 'INT', 'constraint' => 5, 'default' => 0, 'after' => 'lecturer_id'],
            'bimbingan_ps_ts1'      => ['type' => 'INT', 'constraint' => 5, 'default' => 0, 'after' => 'bimbingan_ps_ts2'],
            'bimbingan_ps_ts'       => ['type' => 'INT', 'constraint' => 5, 'default' => 0, 'after' => 'bimbingan_ps_ts1'],
            'bimbingan_ps_lain_ts2' => ['type' => 'INT', 'constraint' => 5, 'default' => 0, 'after' => 'bimbingan_ps_ts'],
            'bimbingan_ps_lain_ts1' => ['type' => 'INT', 'constraint' => 5, 'default' => 0, 'after' => 'bimbingan_ps_lain_ts2'],
            'bimbingan_ps_lain_ts'  => ['type' => 'INT', 'constraint' => 5, 'default' => 0, 'after' => 'bimbingan_ps_lain_ts1'],
        ]);

        if (!$this->db->tableExists('external_lecturers')) {
            $this->forge->addField([
                'id'                      => ['type' => 'CHAR', 'constraint' => 36],
                'period_id'               => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
                'nidn'                    => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'nama'                    => ['type' => 'VARCHAR', 'constraint' => 200],
                'pendidikan_pascasarjana' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'bidang_keahlian'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'jabatan_akademik'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'sertifikat_pendidik'     => ['type' => 'BOOLEAN', 'default' => false],
                'sertifikat_kompetensi'   => ['type' => 'TEXT', 'null' => true],
                'mata_kuliah_diampu'      => ['type' => 'TEXT', 'null' => true],
                'kesesuaian_bidang'       => ['type' => 'ENUM', 'constraint' => ['sesuai', 'tidak_sesuai'], 'null' => true],
                'created_at'              => ['type' => 'DATETIME', 'null' => true],
                'updated_at'              => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('period_id', 'reporting_periods', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('external_lecturers');
        }

        if (!$this->db->tableExists('industrial_lecturers')) {
            $this->forge->addField([
                'id'                  => ['type' => 'CHAR', 'constraint' => 36],
                'period_id'           => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
                'nidk'                => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'nama'                => ['type' => 'VARCHAR', 'constraint' => 200],
                'perusahaan'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'pendidikan_tertinggi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'bidang_keahlian'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'sertifikat_profesi'  => ['type' => 'TEXT', 'null' => true],
                'mata_kuliah_diampu'  => ['type' => 'TEXT', 'null' => true],
                'bobot_sks'           => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00'],
                'created_at'          => ['type' => 'DATETIME', 'null' => true],
                'updated_at'          => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('period_id', 'reporting_periods', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('industrial_lecturers');
        }
    }

    public function down()
    {
        $this->forge->dropColumn('lecturers', [
            'pendidikan_magister', 'pendidikan_doktor', 'kesesuaian_kompetensi',
            'sertifikat_kompetensi', 'mata_kuliah_diampu', 'kesesuaian_bidang_mk'
        ]);

        $this->forge->dropColumn('thesis_supervisions', [
            'bimbingan_ps_ts2', 'bimbingan_ps_ts1', 'bimbingan_ps_ts',
            'bimbingan_ps_lain_ts2', 'bimbingan_ps_lain_ts1', 'bimbingan_ps_lain_ts'
        ]);

        $this->forge->dropTable('external_lecturers', true);
        $this->forge->dropTable('industrial_lecturers', true);
    }
}
