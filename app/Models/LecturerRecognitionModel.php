<?php

namespace App\Models;

use CodeIgniter\Model;

class LecturerRecognitionModel extends Model
{
    protected $table            = 'lecturer_recognitions';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id',
        'nama_rekognisi', 'tingkat', 'tahun', 'dokumen_bukti',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('lecturer_recognitions.*, lecturers.nama, lecturers.nidn, lecturers.bidang_keahlian')
            ->join('lecturers', 'lecturers.id = lecturer_recognitions.lecturer_id')
            ->where('lecturer_recognitions.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('lecturers.nama', $filters['search'])
                ->orLike('lecturer_recognitions.nama_rekognisi', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['tingkat'])) {
            $builder->where('lecturer_recognitions.tingkat', $filters['tingkat']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('lecturer_recognitions.tahun', $filters['tahun']);
        }

        return $builder->orderBy('lecturer_recognitions.tahun', 'DESC');
    }

    public function getStats(int $periodId): array
    {
        $total         = $this->where('period_id', $periodId)->countAllResults();
        $wilayah       = $this->where('period_id', $periodId)->where('tingkat', 'wilayah')->countAllResults();
        $nasional      = $this->where('period_id', $periodId)->where('tingkat', 'nasional')->countAllResults();
        $internasional = $this->where('period_id', $periodId)->where('tingkat', 'internasional')->countAllResults();

        return compact('total', 'wilayah', 'nasional', 'internasional');
    }
}
