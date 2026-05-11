<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table            = 't_pegawai';
    protected $primaryKey       = 'id_pegawai';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ['nama', 'jabatan', 'no_telp', 'username', 'password', 'status'];

    protected $validationRules = [
        'username' => 'required|is_unique[t_pegawai.username]',
        'password' => 'required|min_length[6]',
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            // Cek apakah teks belum berformat Hash BCRYPT / ARGON2
            $info = password_get_info($data['data']['password']);
            if ($info['algoName'] === 'unknown') {
                $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            }
        }
        return $data;
    }

    public function login($username, $password)
    {
        $admin = $this->where('username', $username)->first();
        
        if ($admin && password_verify($password, $admin->password)) {
            return $admin;
        }
        
        return false;
    }
}