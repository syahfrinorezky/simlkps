<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentCommunityServiceModel extends Model
{
    protected $table            = 'student_community_services';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'nama_dosen', 'tema_roadmap', 'nama_mahasiswa', 'judul_kegiatan', 'tahun'
    ];
}
