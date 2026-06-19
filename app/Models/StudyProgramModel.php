<?php

namespace App\Models;

use CodeIgniter\Model;

class StudyProgramModel extends Model
{
    protected $table         = 'study_programs';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['id', 'kode_prodi', 'nama_prodi', 'jenjang', 'akreditasi', 'status_aktif'];
    protected $useTimestamps = true;
}
