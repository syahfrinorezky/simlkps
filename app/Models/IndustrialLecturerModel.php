<?php

namespace App\Models;

use CodeIgniter\Model;

class IndustrialLecturerModel extends Model
{
    protected $table            = 'industrial_lecturers';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'nidk', 'nama', 'perusahaan', 'pendidikan_tertinggi',
        'bidang_keahlian', 'sertifikat_profesi', 'mata_kuliah_diampu', 'bobot_sks',
    ];

    public function getByPeriod(int $periodId, array $filters = [])
    {
        $builder = $this->where('period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('nama', $filters['search'])
                ->orLike('nidk', $filters['search'])
                ->orLike('perusahaan', $filters['search'])
                ->orLike('bidang_keahlian', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('nama', 'ASC');
    }

    public function getStats(int $periodId): array
    {
        $data  = $this->selectSum('bobot_sks')->where('period_id', $periodId)->first();
        $total = $this->where('period_id', $periodId)->countAllResults();

        return [
            'total'     => $total,
            'total_sks' => $data['bobot_sks'] ?? 0,
        ];
    }
}
