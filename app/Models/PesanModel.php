<?php

namespace App\Models;

use CodeIgniter\Model;

class PesanModel extends Model
{
    protected $table            = 't_pesan';
    protected $primaryKey       = 'id_pesan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['nama', 'email', 'no_telp', 'pesan', 'status'];

    protected $useTimestamps = false;
}
