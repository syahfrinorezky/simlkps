<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerModel extends Model
{
    protected $table            = 'lecturers';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'nidn', 'nidk', 'nama', 'gelar_depan', 'gelar_belakang', 'gelar',
        'jabatan_akademik', 'pendidikan_terakhir', 'pendidikan_magister', 'pendidikan_doktor',
        'bidang_keahlian', 'kesesuaian_kompetensi', 'kesesuaian_kompetensi_inti',
        'email', 'telepon', 'foto', 'sertifikat_pendidik', 'sertifikat_kompetensi',
        'mata_kuliah_diampu', 'kesesuaian_bidang_mk', 'praktisi_industri', 'status_dosen', 'is_dtps',
    ];

    public function getPermanent(array $filters = [])
    {
        $builder = $this->where('status_dosen', 'tetap');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('nama', $filters['search'])
                ->orLike('nidn', $filters['search'])
                ->orLike('bidang_keahlian', $filters['search'])
                ->orLike('jabatan_akademik', $filters['search'])
                ->groupEnd();
        }

        if (isset($filters['jabatan_akademik']) && $filters['jabatan_akademik'] !== '') {
            $builder->where('jabatan_akademik', $filters['jabatan_akademik']);
        }

        if (isset($filters['is_dtps']) && $filters['is_dtps'] !== '') {
            $builder->where('is_dtps', $filters['is_dtps']);
        }

        return $builder->orderBy('nama', 'ASC');
    }

    public function getStats(): array
    {
        $total   = $this->where('status_dosen', 'tetap')->countAllResults();
        $dtps    = $this->where('status_dosen', 'tetap')->where('is_dtps', 1)->countAllResults();
        $doktor  = $this->where('status_dosen', 'tetap')->where('pendidikan_doktor !=', '')->where('pendidikan_doktor IS NOT NULL', null, false)->countAllResults();
        $gb      = $this->where('status_dosen', 'tetap')->where('jabatan_akademik', 'guru_besar')->countAllResults();

        return compact('total', 'dtps', 'doktor', 'gb');
    }
}
