<?php

namespace App\Models;

use CodeIgniter\Model;

class ThesisReferenceModel extends Model
{
    protected $table            = 'thesis_references';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'nama_dosen', 'tema_roadmap', 'nama_mahasiswa', 'judul_tesis_disertasi', 'tahun'
    ];
}
