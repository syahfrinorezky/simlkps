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
        'id', 'period_id', 'kategori_publikasi', 'jumlah_ts2', 'jumlah_ts1', 'jumlah_ts'
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

    public function getSummary(int $periodId): array
    {
        $rows = $this->where('period_id', $periodId)->findAll();
        $summary = [];
        foreach ($rows as $r) {
            $summary[$r['kategori_publikasi']] = [
                'ts2' => (int) $r['jumlah_ts2'],
                'ts1' => (int) $r['jumlah_ts1'],
                'ts'  => (int) $r['jumlah_ts'],
            ];
        }
        return $summary;
    }
}
