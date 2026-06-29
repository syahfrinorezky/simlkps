<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeLecturerFieldsNullable extends Migration
{
    public function up()
    {
        // 1. lecturers
        if ($this->db->tableExists('lecturers')) {
            $this->forge->modifyColumn('lecturers', [
                'gelar'               => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'jabatan_akademik'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'pendidikan_terakhir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'bidang_keahlian'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            ]);
        }

        // 2. external_lecturers
        if ($this->db->tableExists('external_lecturers')) {
            $this->forge->modifyColumn('external_lecturers', [
                'pendidikan_pascasarjana' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'bidang_keahlian'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'jabatan_akademik'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            ]);
        }

        // 3. industrial_lecturers
        if ($this->db->tableExists('industrial_lecturers')) {
            $this->forge->modifyColumn('industrial_lecturers', [
                'perusahaan'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'pendidikan_tertinggi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'bidang_keahlian'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            ]);
        }

        // 4. researches
        if ($this->db->tableExists('researches')) {
            $this->forge->modifyColumn('researches', [
                'tahun'       => ['type' => 'YEAR', 'null' => true],
                'jumlah_dana' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            ]);
        }

        // 5. community_services
        if ($this->db->tableExists('community_services')) {
            $this->forge->modifyColumn('community_services', [
                'tahun' => ['type' => 'YEAR', 'null' => true],
            ]);
        }

        // 6. publications
        if ($this->db->tableExists('publications')) {
            // Note: kategori_publikasi might be enum and can't be modified easily without defining all enums again
            // So we just allow null for tahun
            $this->forge->modifyColumn('publications', [
                'tahun' => ['type' => 'YEAR', 'null' => true],
            ]);
        }

        // 7. citations
        if ($this->db->tableExists('citations')) {
            $this->forge->modifyColumn('citations', [
                'jumlah_sitasi' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'null' => true],
            ]);
        }

        // 8. products
        if ($this->db->tableExists('products')) {
            $this->forge->modifyColumn('products', [
                'tahun' => ['type' => 'YEAR', 'null' => true],
            ]);
        }
        
        // 9. lecturer_recognitions
        if ($this->db->tableExists('lecturer_recognitions')) {
            $this->forge->modifyColumn('lecturer_recognitions', [
                'tahun' => ['type' => 'YEAR', 'null' => true],
                'nama_rekognisi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            ]);
        }
    }

    public function down()
    {
        // Reverting this is complex and potentially destructive (data loss for NULLs)
        // Usually down() for modifying column to NULL is omitted or left as is
    }
}
