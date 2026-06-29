<?php

namespace App\Models;

use CodeIgniter\Model;

class ThesisSupervisionModel extends Model
{
    protected $table            = 'thesis_supervisions';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'tahun_periode', 'lecturer_id',
        'bimbingan_ps_ts2', 'bimbingan_ps_ts1', 'bimbingan_ps_ts',
        'bimbingan_ps_lain_ts2', 'bimbingan_ps_lain_ts1', 'bimbingan_ps_lain_ts',
        'study_program_id', 'is_prodi_sendiri',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('thesis_supervisions.*, lecturers.nama, lecturers.nidn, lecturers.jabatan_akademik')
            ->join('lecturers', 'lecturers.id = thesis_supervisions.lecturer_id')
            ->where('thesis_supervisions.period_id', $periodId);

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
        $data = $this->selectSum('bimbingan_ps_ts2')->selectSum('bimbingan_ps_ts1')
            ->selectSum('bimbingan_ps_ts')->selectSum('bimbingan_ps_lain_ts2')
            ->selectSum('bimbingan_ps_lain_ts1')->selectSum('bimbingan_ps_lain_ts')
            ->where('period_id', $periodId)->first();

        $totalDosen = $this->where('period_id', $periodId)->countAllResults();

        return [
            'total_dosen'   => $totalDosen,
            'total_ps_ts2'  => $data['bimbingan_ps_ts2'] ?? 0,
            'total_ps_ts1'  => $data['bimbingan_ps_ts1'] ?? 0,
            'total_ps_ts'   => $data['bimbingan_ps_ts'] ?? 0,
        ];
    }
}
