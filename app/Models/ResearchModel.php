<?php

namespace App\Models;

use CodeIgniter\Model;

class ResearchModel extends Model
{
    protected $table            = 'researches';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'judul_penelitian', 'tema_roadmap',
        'sumber_dana', 'jumlah_dana', 'tahun',
    ];

    public function getWithMembers(int $periodId, array $filters = [])
    {
        $builder = $this->select('researches.*, GROUP_CONCAT(lecturers.nama SEPARATOR ", ") as anggota')
            ->join('research_members', 'research_members.research_id = researches.id', 'left')
            ->join('lecturers', 'lecturers.id = research_members.lecturer_id', 'left')
            ->where('researches.period_id', $periodId)
            ->groupBy('researches.id');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('researches.judul_penelitian', $filters['search'])
                ->orLike('researches.tema_roadmap', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['sumber_dana'])) {
            $builder->where('researches.sumber_dana', $filters['sumber_dana']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('researches.tahun', $filters['tahun']);
        }

        return $builder->orderBy('researches.tahun', 'DESC');
    }

    public function getSummaryByFunding(int $periodId): array
    {
        return $this->select('sumber_dana, COUNT(*) as jumlah, SUM(jumlah_dana) as total_dana')
            ->where('period_id', $periodId)
            ->groupBy('sumber_dana')
            ->findAll();
    }

    public function getStats(int $periodId): array
    {
        $data     = $this->selectSum('jumlah_dana', 'total_dana')->where('period_id', $periodId)->first();
        $total    = $this->where('period_id', $periodId)->countAllResults();
        $pt       = $this->where('period_id', $periodId)->where('sumber_dana', 'pt_mandiri')->countAllResults();
        $luar_neg = $this->where('period_id', $periodId)->where('sumber_dana', 'lembaga_luar_negeri')->countAllResults();

        return [
            'total'      => $total,
            'total_dana' => $data['total_dana'] ?? 0,
            'pt_mandiri' => $pt,
            'luar_negeri' => $luar_neg,
        ];
    }
}
