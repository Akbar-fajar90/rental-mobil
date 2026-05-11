<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PegawaiModel;

class Auth extends BaseController
{
    protected $pegawaiModel;

    public function __construct()
    {
        $this->pegawaiModel = new PegawaiModel();
        helper('form');
    }
    
    public function login()
    {
        // Cek jika sudah login, redirect ke dashboard
        if (session()->get('isAdminLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        
        $data['title'] = 'Login Admin - Rental Mobil';
        
        return view('admin/login', $data);
    }

    
    public function doLogin()
    {
        // Validasi input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required',
            'password' => 'required'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Cek ke database
        $pegawai = $this->pegawaiModel->where('username', $username)->first();
        
        if ($pegawai && password_verify($password, $pegawai->password)) {
            session()->set([
                'isAdminLoggedIn' => true,
                'adminId' => $pegawai->id_pegawai,
                'adminUsername' => $pegawai->username,
                'adminRole' => $pegawai->jabatan,
                'adminNama' => $pegawai->nama
            ]);
            return redirect()->to('/admin/dashboard');
        } else {
            return redirect()->back()->with('error', 'Username atau password salah!');
        }
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}