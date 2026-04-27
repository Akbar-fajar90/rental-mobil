<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table = 't_pelanggan';
    protected $primaryKey = 'id_pelanggan';
    protected $useAutoIncrement = true;
    

    protected $returnType = 'object';
    
    
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama',
        'nik',
        'no_telp',
        'alamat',
        'email',
        'no_sim',
        'foto_identitas'
    ];

    protected $useTimestamps = false;
    
    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'nik' => 'required|min_length[16]|max_length[20]|is_unique[t_pelanggan.nik,id_pelanggan,{id_pelanggan}]',
        'no_telp' => 'required|min_length[10]|max_length[15]',
        'alamat' => 'required|min_length[5]|max_length[100]',
        'email' => 'permit_empty|valid_email|max_length[100]',
        'no_sim' => 'permit_empty|max_length[20]'
    ];

    protected $validationMessages = [
        'nama' => [
            'required' => 'Nama lengkap wajib diisi',
            'min_length' => 'Nama minimal 3 karakter'
        ],
        'nik' => [
            'required' => 'NIK wajib diisi',
            'min_length' => 'NIK minimal 16 digit',
            'is_unique' => 'NIK sudah terdaftar'
        ],
        'no_telp' => [
            'required' => 'Nomor telepon wajib diisi',
            'min_length' => 'Nomor telepon minimal 10 digit'
        ],
        'alamat' => [
            'required' => 'Alamat wajib diisi'
        ],
        'email' => [
            'valid_email' => 'Email tidak valid'
        ]
    ];

    protected $skipValidation = false;
}