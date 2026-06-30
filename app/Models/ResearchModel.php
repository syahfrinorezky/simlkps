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
        'id', 'period_id', 'sumber_dana', 'jumlah_ts2', 'jumlah_ts1', 'jumlah_ts'
    ];

    public function getSummary(int $periodId): array
    {
        $rows = $this->where('period_id', $periodId)->findAll();
        $summary = [];
        foreach ($rows as $r) {
            $summary[$r['sumber_dana']] = [
                'ts2' => (int) $r['jumlah_ts2'],
                'ts1' => (int) $r['jumlah_ts1'],
                'ts'  => (int) $r['jumlah_ts'],
            ];
        }
        return $summary;
    }
}
