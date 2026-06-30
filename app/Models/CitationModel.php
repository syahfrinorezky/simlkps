<?php

namespace App\Models;

use CodeIgniter\Model;

class CitationModel extends Model
{
    protected $table            = 'citations';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id', 'judul_artikel', 'jumlah_sitasi',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('citations.*, lecturers.nama, lecturers.nidn')
            ->join('lecturers', 'lecturers.id = citations.lecturer_id')
            ->where('citations.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('citations.judul_artikel', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('citations.jumlah_sitasi', 'DESC');
    }

    public function getStats(int $periodId): array
    {
        $data  = $this->selectSum('jumlah_sitasi', 'total_sitasi')->selectAvg('jumlah_sitasi', 'avg_sitasi')->where('period_id', $periodId)->first();
        $total = $this->where('period_id', $periodId)->countAllResults();

        return [
            'total'       => $total,
            'total_sitasi' => $data['total_sitasi'] ?? 0,
            'avg_sitasi'  => round($data['avg_sitasi'] ?? 0, 1),
        ];
    }
}
