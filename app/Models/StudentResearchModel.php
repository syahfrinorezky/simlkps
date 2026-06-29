<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentResearchModel extends Model
{
    protected $table            = 'student_researches';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'nama_dosen', 'tema_roadmap', 'nama_mahasiswa', 'judul_kegiatan', 'tahun'
    ];
}
