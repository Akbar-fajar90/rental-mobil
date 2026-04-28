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

    protected $validationRules = [
        'plat_nomor'     => 'required|is_unique[t_mobil.plat_nomor,id_mobil,{id_mobil}]|min_length[3]',
        'merk'           => 'required|min_length[3]',
        'tahun'          => 'required|numeric|exact_length[4]',
        'tarif_per_hari' => 'required|numeric',
        'denda_per_hari' => 'required|numeric',
        'status'         => 'required|in_list[tersedia,disewa,perbaikan]'
    ];

    protected $validationMessages = [
        'plat_nomor' => [
            'is_unique' => 'Plat nomor ini sudah terdaftar dalam sistem.'
        ]
    ];

    // Ambil data dari view v_katalog_mobil
    public function getKatalogMobil()
    {
        $db = \Config\Database::connect();
        return $db->table('v_katalog_mobil')->get()->getResultArray();
    }
}