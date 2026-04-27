<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AdminController extends BaseController
{
    protected $adminModel;
    
    public function __construct()
    {
        $this->adminModel = new AdminModel();
        helper(['form', 'url', 'file']);
    }
    
    // ========== HALAMAN UTAMA ==========
    public function index()
    {
        $settings = [
            'app_name' => session()->get('app_name') ?? 'Rental Mobil',
            'dark_mode' => session()->get('dark_mode') ?? 'off',
            'email_notification' => session()->get('email_notification') ?? 'on',
            'logo_path' => session()->get('logo_path') ?? '/assets/img/logo.png'
        ];
        
        $data = [
            'title' => 'Pengaturan Admin',
            'page_title' => 'Pengaturan',
            'active_menu' => 'admin',
            'admins' => $this->adminModel->getAllAdmins(),
            'stats' => $this->adminModel->getStats(),
            'settings' => $settings
        ];
        
        return view('admin/admin', $data);
    }
    
    // ========== SIMPAN ADMIN BARU ==========
    public function save()
    {
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'username' => 'required|min_length[3]|is_unique[t_pegawai.username]',
            'password' => 'required|min_length[4]',
            'jabatan' => 'required',
            'no_telp' => 'permit_empty|min_length[10]|max_length[15]',
            'status' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }
        
        $this->adminModel->save([
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'password' => $this->request->getPost('password'),
            'jabatan' => $this->request->getPost('jabatan'),
            'no_telp' => $this->request->getPost('no_telp'),
            'status' => $this->request->getPost('status')
        ]);
        
        session()->setFlashdata('success', 'Admin berhasil ditambahkan!');
        return redirect()->to('/admin/admin');
    }
    
    // ========== UPDATE ADMIN ==========
    public function update($id)
    {
        $id = (int)$id;
        
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'username' => "required|min_length[3]|is_unique[t_pegawai.username,id_pegawai,{$id}]",
            'jabatan' => 'required',
            'no_telp' => 'permit_empty|min_length[10]|max_length[15]',
            'status' => 'required'
        ];
        
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[4]';
        }
        
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }
        
        $data = [
            'nama' => $this->request->getPost('nama'),
            'username' => $this->request->getPost('username'),
            'jabatan' => $this->request->getPost('jabatan'),
            'no_telp' => $this->request->getPost('no_telp'),
            'status' => $this->request->getPost('status')
        ];
        
        if (!empty($password)) {
            $data['password'] = $password;
        }
        
        $this->adminModel->update($id, $data);
        
        session()->setFlashdata('success', 'Admin berhasil diupdate!');
        return redirect()->to('/admin/admin');
    }
    
    // ========== HAPUS ADMIN ==========
    public function delete($id)
    {
        $id = (int)$id;
        
        /** @var object|null $admin */
        $admin = $this->adminModel->find($id);
        
        if (!$admin) {
            session()->setFlashdata('error', 'Admin tidak ditemukan');
            return redirect()->to('/admin/admin');
        }
        
        // Cegah menghapus diri sendiri
        if ($admin->username == session()->get('adminUsername')) {
            session()->setFlashdata('error', 'Tidak dapat menghapus akun sendiri!');
            return redirect()->to('/admin/admin');
        }
        
        $this->adminModel->delete($id);
        session()->setFlashdata('success', 'Admin berhasil dihapus!');
        return redirect()->to('/admin/admin');
    }
    
    // ========== GET ADMIN BY ID (AJAX) ==========
    public function getAdmin($id)
    {
        $id = (int)$id;
        
        /** @var object|null $admin */
        $admin = $this->adminModel->find($id);
        
        if ($admin) {
            return $this->response->setJSON($admin);
        }
        
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
    }
    
    // ========== UPDATE PENGATURAN SISTEM ==========
    public function updateSettings()
    {
        $rules = [
            'app_name' => 'required|min_length[3]|max_length[100]',
            'logo' => 'permit_empty|is_image[logo]|max_size[logo,2048]|ext_in[logo,png,jpg,jpeg,svg]'
        ];
        
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }
        
        $appName = $this->request->getPost('app_name');
        session()->set('app_name', $appName);
        
        $darkMode = $this->request->getPost('dark_mode') ? 'on' : 'off';
        session()->set('dark_mode', $darkMode);
        
        $emailNotif = $this->request->getPost('email_notif') ? 'on' : 'off';
        session()->set('email_notification', $emailNotif);
        
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $oldLogo = session()->get('logo_path');
            if ($oldLogo && $oldLogo != '/assets/img/logo.png' && file_exists(FCPATH . $oldLogo)) {
                unlink(FCPATH . $oldLogo);
            }
            
            $newName = 'logo_' . time() . '.' . $file->getExtension();
            $file->move(FCPATH . 'assets/img', $newName);
            $logoPath = '/assets/img/' . $newName;
            session()->set('logo_path', $logoPath);
        }
        
        session()->setFlashdata('success', 'Pengaturan berhasil disimpan!');
        return redirect()->to('/admin/admin');
    }
    
    // ========== UPLOAD LOGO AJAX ==========
    public function uploadLogo()
    {
        $file = $this->request->getFile('logo');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
        }
        
        if ($file->getSize() > 2048 * 1024) {
            return $this->response->setJSON(['success' => false, 'message' => 'File terlalu besar, maksimal 2MB']);
        }
        
        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Format harus PNG, JPG, JPEG, atau SVG']);
        }
        
        $oldLogo = session()->get('logo_path');
        if ($oldLogo && $oldLogo != '/assets/img/logo.png' && file_exists(FCPATH . $oldLogo)) {
            unlink(FCPATH . $oldLogo);
        }
        
        $newName = 'logo_' . time() . '.' . $file->getExtension();
        $file->move(FCPATH . 'assets/img', $newName);
        $logoPath = '/assets/img/' . $newName;
        session()->set('logo_path', $logoPath);
        
        return $this->response->setJSON([
            'success' => true,
            'logo_url' => base_url($logoPath),
            'message' => 'Logo berhasil diupload'
        ]);
    }
}