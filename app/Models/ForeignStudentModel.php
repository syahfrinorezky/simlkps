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
        'mahasiswa_asing_penuh_waktu',
        'mahasiswa_asing_paruh_waktu',
        'negara_asal',
        'jenjang'
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
                ->orLike('foreign_students.negara_asal', $search)
                ->orLike('foreign_students.jenjang', $search)
                ->groupEnd();
        }

        return $builder->orderBy('foreign_students.created_at', 'DESC');
    }
}
