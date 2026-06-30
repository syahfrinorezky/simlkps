<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id', 'nama_produk', 'deskripsi',
        'status_komersialisasi', 'tahun',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('products.*, lecturers.nama, lecturers.nidn')
            ->join('lecturers', 'lecturers.id = products.lecturer_id')
            ->where('products.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('products.nama_produk', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['tahun'])) {
            $builder->where('products.tahun', $filters['tahun']);
        }

        if (isset($filters['status_komersialisasi']) && $filters['status_komersialisasi'] !== '') {
            $builder->where('products.status_komersialisasi', $filters['status_komersialisasi']);
        }

        return $builder->orderBy('products.tahun', 'DESC');
    }

    public function getStats(int $periodId): array
    {
        $total        = $this->where('period_id', $periodId)->countAllResults();
        $komersialisasi = $this->where('period_id', $periodId)->where('status_komersialisasi', 1)->countAllResults();

        return [
            'total'          => $total,
            'komersialisasi' => $komersialisasi,
        ];
    }
}
