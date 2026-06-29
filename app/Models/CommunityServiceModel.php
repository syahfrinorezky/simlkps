<?php

namespace App\Models;

use CodeIgniter\Model;

class CommunityServiceModel extends Model
{
    protected $table            = 'community_services';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id', 'judul_kegiatan',
        'tema_roadmap', 'sumber_dana', 'jumlah_dana', 'tahun',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('community_services.*, lecturers.nama, lecturers.nidn')
            ->join('lecturers', 'lecturers.id = community_services.lecturer_id')
            ->where('community_services.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('community_services.judul_kegiatan', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['sumber_dana'])) {
            $builder->where('community_services.sumber_dana', $filters['sumber_dana']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('community_services.tahun', $filters['tahun']);
        }

        return $builder->orderBy('community_services.tahun', 'DESC');
    }

    public function getStats(int $periodId): array
    {
        $data  = $this->selectSum('jumlah_dana', 'total_dana')->where('period_id', $periodId)->first();
        $total = $this->where('period_id', $periodId)->countAllResults();

        return [
            'total'      => $total,
            'total_dana' => $data['total_dana'] ?? 0,
        ];
    }
}
