<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nama', 'deskripsi'];
    protected $useTimestamps = true;
}
