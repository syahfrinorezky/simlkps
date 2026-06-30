<?php

namespace App\Models;

use CodeIgniter\Model;

class IntellectualPropertyModel extends Model
{
    protected $table            = 'intellectual_properties';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id', 'judul_hki', 'kategori', 'tahun', 'keterangan'
    ];

    public const KATEGORI_LABELS = [
        'paten'          => 'HKI Paten / Paten Sederhana',
        'hak_cipta'      => 'HKI Hak Cipta / Desain Produk',
        'teknologi'      => 'Teknologi Tepat Guna / Produk / Karya Seni',
        'buku'           => 'Buku ISBN / Book Chapter',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('intellectual_properties.*, lecturers.nama, lecturers.nidn')
            ->join('lecturers', 'lecturers.id = intellectual_properties.lecturer_id')
            ->where('intellectual_properties.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('intellectual_properties.judul_hki', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['kategori'])) {
            $builder->where('intellectual_properties.kategori', $filters['kategori']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('intellectual_properties.tahun', $filters['tahun']);
        }

        return $builder->orderBy('intellectual_properties.tahun', 'DESC');
    }

    public function getStats(int $periodId): array
    {
        $total    = $this->where('period_id', $periodId)->countAllResults();
        $paten    = $this->where('period_id', $periodId)->where('kategori', 'paten')->countAllResults();
        $hak_cipta = $this->where('period_id', $periodId)->where('kategori', 'hak_cipta')->countAllResults();
        $buku     = $this->where('period_id', $periodId)->where('kategori', 'buku')->countAllResults();

        return compact('total', 'paten', 'hak_cipta', 'buku');
    }
}
