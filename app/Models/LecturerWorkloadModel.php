<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerWorkloadModel extends Model
{
    protected $table            = 'lecturer_workloads';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id',
        'sks_pengajaran', 'sks_ps_lain_dalam_pt', 'sks_ps_luar_pt', 
        'sks_penelitian', 'sks_pkm', 'sks_penunjang', 'total_sks'
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('lecturer_workloads.*, lecturers.nama, lecturers.nidn, lecturers.jabatan_akademik, lecturers.is_dtps')
            ->join('lecturers', 'lecturers.id = lecturer_workloads.lecturer_id')
            ->where('lecturer_workloads.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('lecturers.nama', $filters['search'])
                ->orLike('lecturers.nidn', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('lecturers.nama', 'ASC');
    }

    public function getStats(int $periodId): array
    {
        $data = $this->selectAvg('total_sks', 'avg_sks')
            ->where('period_id', $periodId)->first();

        $totalDosen = $this->where('period_id', $periodId)->countAllResults();
        $ideal      = $this->where('period_id', $periodId)
            ->where('total_sks >=', 12)->where('total_sks <=', 16)
            ->countAllResults();

        return [
            'total_dosen' => $totalDosen,
            'avg_sks'     => round($data['avg_sks'] ?? 0, 2),
            'avg_rata'    => round($data['avg_sks'] ?? 0, 2),
            'ideal'       => $ideal,
        ];
    }
}
