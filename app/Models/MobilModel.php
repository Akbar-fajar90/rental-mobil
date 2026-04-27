<?php

namespace App\Models;

use CodeIgniter\Model;

class MobilModel extends Model
{
    protected $table            = 't_mobil';
    protected $primaryKey       = 'id_mobil';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['plat_nomor', 'merk', 'tahun', 'foto_mobil', 'tarif_per_hari', 'denda_per_hari', 'status', 'device_id'];

    // Ambil data dari view v_katalog_mobil
    public function getKatalogMobil()
    {
        $db = \Config\Database::connect();
        return $db->table('v_katalog_mobil')->get()->getResultArray();
    }
}