<?php

namespace App\Models;

use CodeIgniter\Model;

class LearningIntegrationModel extends Model
{
    protected $table            = 'learning_integrations';
    protected $primaryKey       = 'id';
    protected $keyType          = 'string';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id', 'period_id', 'judul_penelitian_pkm', 'nama_dosen',
        'nama_mk', 'bentuk_integrasi', 'tahun'
    ];
}
