<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodModel extends Model
{
    protected $table = 'reporting_periods';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_periode', 'tahun_akademik', 'status'];
    protected $useTimestamps = true;
}
