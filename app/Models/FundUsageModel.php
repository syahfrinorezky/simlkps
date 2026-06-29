<?php

namespace App\Models;

use CodeIgniter\Model;

class FundUsageModel extends Model
{
    protected $table            = 'fund_usages';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'jenis_penggunaan', 'kategori',
        'upps_ts_2', 'upps_ts_1', 'upps_ts', 'upps_rata_rata',
        'ps_ts_2', 'ps_ts_1', 'ps_ts', 'ps_rata_rata'
    ];
}
