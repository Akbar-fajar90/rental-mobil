<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 't_pegawai';
    protected $primaryKey = 'id_pegawai';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    
    protected $allowedFields = [
        'nama', 'jabatan', 'no_telp', 'username', 'password', 'status'
    ];
    
    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'username' => 'required|min_length[3]|is_unique[t_pegawai.username,id_pegawai,{id_pegawai}]',
        'password' => 'required|min_length[4]',
        'jabatan' => 'required|in_list[Owner,Admin,Staff]',
        'status' => 'required|in_list[aktif,nonaktif]'
    ];
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    
    // Get all admins with role mapping
    public function getAllAdmins()
    {
        return $this->orderBy('id_pegawai', 'ASC')->findAll();
    }
    
    // Get admin by ID
    public function getAdminById($id)
    {
        return $this->find($id);
    }
    
    // Get stats
    public function getStats()
    {
        $total = $this->countAll();
        $aktif = $this->where('status', 'aktif')->countAllResults();
        $nonaktif = $this->where('status', 'nonaktif')->countAllResults();
        
        return (object)[
            'total' => $total,
            'aktif' => $aktif,
            'nonaktif' => $nonaktif
        ];
    }

}