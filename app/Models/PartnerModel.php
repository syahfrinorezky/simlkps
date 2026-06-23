<?php

namespace App\Models;

use CodeIgniter\Model;

class PartnerModel extends Model
{
    protected $table = 'partners';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $useAutoIncrement = false;
    protected $allowedFields = ['id', 'nama_mitra', 'jenis_mitra', 'negara', 'alamat', 'kontak'];
    protected $useTimestamps = true;
}
