<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table            = 'courses';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'semester', 'kode_mk', 'nama_mk', 'mk_kompetensi',
        'sks_kuliah', 'sks_seminar', 'sks_praktikum', 'konversi_jam',
        'cpl_sikap', 'cpl_pengetahuan', 'cpl_keterampilan_umum', 'cpl_keterampilan_khusus',
        'dokumen_rencana_pembelajaran', 'unit_penyelenggara'
    ];
}
