<?php

namespace App\Models;

use CodeIgniter\Model;

class PublicationModel extends Model
{
    protected $table            = 'publications';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'lecturer_id', 'judul', 'kategori_publikasi',
        'penerbit', 'nomor_issn_isbn', 'jenis_publikasi', 'tingkat', 'tahun', 'tautan',
    ];

    public const KATEGORI_LABELS = [
        'jurnal_tidak_terakreditasi'     => 'Jurnal Tidak Terakreditasi',
        'jurnal_nasional_terakreditasi'  => 'Jurnal Nasional Terakreditasi',
        'jurnal_internasional'           => 'Jurnal Internasional',
        'jurnal_internasional_bereputasi' => 'Jurnal Internasional Bereputasi',
        'seminar_wilayah'                => 'Seminar Wilayah',
        'seminar_nasional'               => 'Seminar Nasional',
        'seminar_internasional'          => 'Seminar Internasional',
        'pagelaran_wilayah'              => 'Pagelaran/Pameran Wilayah',
        'pagelaran_nasional'             => 'Pagelaran/Pameran Nasional',
        'pagelaran_internasional'        => 'Pagelaran/Pameran Internasional',
    ];

    public function getWithLecturer(int $periodId, array $filters = [])
    {
        $builder = $this->select('publications.*, lecturers.nama, lecturers.nidn')
            ->join('lecturers', 'lecturers.id = publications.lecturer_id')
            ->where('publications.period_id', $periodId);

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('publications.judul', $filters['search'])
                ->orLike('lecturers.nama', $filters['search'])
                ->orLike('publications.penerbit', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['kategori_publikasi'])) {
            $builder->where('publications.kategori_publikasi', $filters['kategori_publikasi']);
        }

        if (!empty($filters['tingkat'])) {
            $builder->where('publications.tingkat', $filters['tingkat']);
        }

        if (!empty($filters['tahun'])) {
            $builder->where('publications.tahun', $filters['tahun']);
        }

        return $builder->orderBy('publications.tahun', 'DESC');
    }

    public function getSummaryByJenis(int $periodId): array
    {
        return $this->select('kategori_publikasi, COUNT(*) as jumlah')
            ->where('period_id', $periodId)
            ->groupBy('kategori_publikasi')
            ->findAll();
    }

    public function getStats(int $periodId): array
    {
        $total         = $this->where('period_id', $periodId)->countAllResults();
        $internasional = $this->where('period_id', $periodId)->whereIn('kategori_publikasi', ['jurnal_internasional', 'jurnal_internasional_bereputasi'])->countAllResults();
        $nasional      = $this->where('period_id', $periodId)->whereIn('kategori_publikasi', ['jurnal_nasional_terakreditasi', 'seminar_nasional', 'pagelaran_nasional'])->countAllResults();
        $bereputasi    = $this->where('period_id', $periodId)->where('kategori_publikasi', 'jurnal_internasional_bereputasi')->countAllResults();

        return compact('total', 'internasional', 'nasional', 'bereputasi');
    }
}
