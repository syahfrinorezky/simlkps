<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentSatisfactionModel extends Model
{
    protected $table            = 'student_satisfactions';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'aspek', 'sangat_baik', 'baik', 'cukup', 'kurang', 'rencana_tindak_lanjut'
    ];
}
