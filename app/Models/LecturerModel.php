<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table         = 'lecturers';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['id', 'nidn', 'nama', 'gelar', 'jabatan_akademik', 'pendidikan_terakhir', 'bidang_keahlian', 'sertifikat_pendidik', 'praktisi_industri', 'status_dosen'];
    protected $useTimestamps = true;
}
