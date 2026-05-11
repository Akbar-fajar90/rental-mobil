<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class ForgotPassword extends BaseController
{
    protected $adminModel;
    
    public function __construct()
    {
        $this->adminModel = new AdminModel();
        helper(['form', 'url']);
    }
    
    // ========== HALAMAN FORGOT PASSWORD ==========
    public function index()
    {
        $data = [
            'title' => 'Lupa Password',
            'validation' => \Config\Services::validation()
        ];
        
        return view('admin/forgot_password', $data);
    }
    
    // ========== PROSES RESET PASSWORD ==========
    public function reset()
    {
        // Validasi input
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]',
            'new_password' => 'required|min_length[4]',
            'confirm_password' => 'required|matches[new_password]'
        ];
        
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }
        
        $username = $this->request->getPost('username');
        $newPassword = $this->request->getPost('new_password');
        
        // Cek username di database
        $admin = $this->adminModel->where('username', $username)->first();
        
        if (!$admin) {
            session()->setFlashdata('error', 'Username tidak ditemukan!');
            return redirect()->back()->withInput();
        }
        
        // Update password
        $this->adminModel->update($admin->id_pegawai, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        session()->setFlashdata('success', 'Password berhasil direset! Silakan login dengan password baru.');
        return redirect()->to('/admin/login');
    }
}