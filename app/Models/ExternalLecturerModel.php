<?php

namespace App\Models;

use CodeIgniter\Model;

class ExternalLecturerModel extends Model
{
    protected $table            = 'external_lecturers';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'nidn', 'nama', 'pendidikan_pascasarjana',
        'bidang_keahlian', 'jabatan_akademik', 'sertifikat_pendidik',
        'sertifikat_kompetensi', 'mata_kuliah_diampu', 'kesesuaian_bidang',
    ];

    public function getByPeriod(int $periodId, array $filters = [])
    {
        $builder = $this->where('period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('nama', $filters['search'])
                ->orLike('nidn', $filters['search'])
                ->orLike('bidang_keahlian', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['jabatan_akademik'])) {
            $builder->where('jabatan_akademik', $filters['jabatan_akademik']);
        }

        return $builder->orderBy('nama', 'ASC');
    }

    public function getStats(int $periodId): array
    {
        $total    = $this->where('period_id', $periodId)->countAllResults();
        $bersertifikat = $this->where('period_id', $periodId)->where('sertifikat_pendidik', 1)->countAllResults();
        $sesuai   = $this->where('period_id', $periodId)->where('kesesuaian_bidang', 'sesuai')->countAllResults();

        return compact('total', 'bersertifikat', 'sesuai');
    }
}
