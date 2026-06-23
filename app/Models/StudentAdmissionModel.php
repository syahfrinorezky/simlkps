<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAdmissionModel extends Model
{
    protected $table = 'student_admissions';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        'id',
        'period_id',
        'study_program_id',
        'tahun_akademik',
        'daya_tampung',
        'jumlah_pendaftar',
        'jumlah_lulus_seleksi',
        'mahasiswa_baru_reguler',
        'mahasiswa_baru_transfer',
        'mahasiswa_aktif'
    ];
    protected $useTimestamps = true;

    public function getAdmissions($periodId = null, $search = null)
    {
        $builder = $this->select('student_admissions.*, reporting_periods.nama_periode, reporting_periods.tahun_akademik as period_tahun, study_programs.nama_prodi')
            ->join('reporting_periods', 'reporting_periods.id = student_admissions.period_id')
            ->join('study_programs', 'study_programs.id = student_admissions.study_program_id');

        if ($periodId) {
            $builder->where('student_admissions.period_id', $periodId);
        }

        if ($search) {
            $builder->groupStart()
                ->like('study_programs.nama_prodi', $search)
                ->orLike('student_admissions.tahun_akademik', $search)
                ->groupEnd();
        }

        return $builder->orderBy('student_admissions.created_at', 'DESC');
    }
}
