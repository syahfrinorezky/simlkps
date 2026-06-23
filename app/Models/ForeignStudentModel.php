<?php

namespace App\Models;

use CodeIgniter\Model;

class ForeignStudentModel extends Model
{
    protected $table = 'foreign_students';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'period_id',
        'study_program_id',
        'tahun_akademik',
        'mahasiswa_aktif_ts2',
        'mahasiswa_aktif_ts1',
        'mahasiswa_aktif_ts',
        'mahasiswa_asing_full_ts2',
        'mahasiswa_asing_full_ts1',
        'mahasiswa_asing_full_ts',
        'mahasiswa_asing_part_ts2',
        'mahasiswa_asing_part_ts1',
        'mahasiswa_asing_part_ts'
    ];
    protected $useTimestamps = true;

    public function getForeignStudents($periodId = null, $search = null)
    {
        $builder = $this->select('foreign_students.*, reporting_periods.nama_periode, reporting_periods.tahun_akademik as period_tahun, study_programs.nama_prodi')
            ->join('reporting_periods', 'reporting_periods.id = foreign_students.period_id')
            ->join('study_programs', 'study_programs.id = foreign_students.study_program_id');

        if ($periodId) {
            $builder->where('foreign_students.period_id', $periodId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('study_programs.nama_prodi', $search)
                ->groupEnd();
        }

        return $builder->orderBy('foreign_students.created_at', 'DESC');
    }
}
