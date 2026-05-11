<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PelangganModel;

class Pelanggan extends BaseController
{
    protected PelangganModel $pelangganModel;  // ✅ Tambah type hinting
    
    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        helper(['form', 'url']);
    }
    
    // ========== HALAMAN INDEX (TAMPIL SEMUA DATA) ==========
    public function index()
    {
        $data = [
            'title' => 'Manajemen Pelanggan',
            'page_title' => 'Pelanggan',
            'active_menu' => 'pelanggan',
            'pelanggan' => $this->pelangganModel->orderBy('id_pelanggan', 'DESC')->paginate(10),
            'pager' => $this->pelangganModel->pager,
            'keyword' => $this->request->getGet('keyword')
        ];
        
        // Jika ada pencarian
        if ($keyword = $this->request->getGet('keyword')) {
            $data['pelanggan'] = $this->pelangganModel
                ->groupStart()
                ->like('nama', $keyword)
                ->orLike('nik', $keyword)
                ->orLike('no_telp', $keyword)
                ->orLike('email', $keyword)
                ->groupEnd()
                ->orderBy('id_pelanggan', 'DESC')
                ->paginate(10);
        }
        
        return view('admin/pelanggan', $data);
    }
    
    // ========== SIMPAN DATA BARU (CREATE) ==========
    public function save()
    {
        // Validasi input
        if (!$this->validate([
            'nama' => 'required|min_length[3]|max_length[100]',
            'nik' => 'required|min_length[16]|max_length[20]|is_unique[t_pelanggan.nik]',
            'no_telp' => 'required|min_length[10]|max_length[15]|numeric',
            'alamat' => 'required|min_length[5]|max_length[255]',
            'email' => 'permit_empty|valid_email|is_unique[t_pelanggan.email]',
            'no_sim' => 'permit_empty|max_length[20]',
            'password' => 'required|min_length[8]',
            'foto_identitas' => 'permit_empty|is_image[foto_identitas]|max_size[foto_identitas,2048]|ext_in[foto_identitas,png,jpg,jpeg]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Upload foto identitas (opsional)
        $fotoIdentitas = '';
        $file = $this->request->getFile('foto_identitas');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/identitas', $newName);
            $fotoIdentitas = $newName;
        }
        
        // Simpan data
        $this->pelangganModel->save([
            'nama' => $this->request->getPost('nama'),
            'nik' => $this->request->getPost('nik'),
            'no_telp' => $this->request->getPost('no_telp'),
            'alamat' => $this->request->getPost('alamat'),
            'email' => $this->request->getPost('email'),
            'no_sim' => $this->request->getPost('no_sim'),
            'password' => password_hash((string)$this->request->getPost('password'), PASSWORD_DEFAULT),
            'provider' => 'email',
            'foto_identitas' => $fotoIdentitas
        ]);
        
        session()->setFlashdata('success', 'Data pelanggan berhasil ditambahkan!');
        return redirect()->to('/admin/pelanggan');
    }
    
    // ========== TAMPIL FORM EDIT (UPDATE) ==========
    public function edit($id)
    {
        // ✅ Tambahkan casting ke integer untuk keamanan
        $id = (int)$id;
        $pelanggan = $this->pelangganModel->find($id);
        
        if (!$pelanggan) {
            session()->setFlashdata('error', 'Data pelanggan tidak ditemukan!');
            return redirect()->to('/admin/pelanggan');
        }
        
        $data = [
            'title' => 'Edit Pelanggan',
            'page_title' => 'Edit Pelanggan',
            'active_menu' => 'pelanggan',
            'pelanggan' => $pelanggan
        ];
        
        return view('admin/pelanggan_edit', $data);
    }
    
    // ========== UPDATE DATA (UPDATE) ==========
    public function update($id)
    {
        $id = (int)$id;  // ✅ Casting ke integer
        
        // Validasi input (nik & email unique kecuali dirinya sendiri)
        $rules = [
            'nama' => 'required|min_length[3]|max_length[100]',
            'nik' => "required|min_length[16]|max_length[20]|is_unique[t_pelanggan.nik,id_pelanggan,{$id}]",
            'no_telp' => 'required|min_length[10]|max_length[15]|numeric',
            'alamat' => 'required|min_length[5]|max_length[255]',
            'email' => "permit_empty|valid_email|is_unique[t_pelanggan.email,id_pelanggan,{$id}]",
            'no_sim' => 'permit_empty|max_length[20]'
        ];

        $file = $this->request->getFile('foto_identitas');
        if ($file && $file->isValid()) {
            $rules['foto_identitas'] = 'is_image[foto_identitas]|max_size[foto_identitas,2048]|ext_in[foto_identitas,png,jpg,jpeg]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Upload foto identitas baru (jika ada)
        $pelanggan = $this->pelangganModel->find($id);
        $fotoIdentitas = $pelanggan->foto_identitas;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Hapus foto lama
            if ($fotoIdentitas && file_exists('uploads/identitas/' . $fotoIdentitas)) {
                unlink('uploads/identitas/' . $fotoIdentitas);
            }
            $newName = $file->getRandomName();
            $file->move('uploads/identitas', $newName);
            $fotoIdentitas = $newName;
        }
        
        // Update data
        $data = [
            'nama' => $this->request->getPost('nama'),
            'nik' => $this->request->getPost('nik'),
            'no_telp' => $this->request->getPost('no_telp'),
            'alamat' => $this->request->getPost('alamat'),
            'email' => $this->request->getPost('email'),
            'no_sim' => $this->request->getPost('no_sim'),
            'foto_identitas' => $fotoIdentitas
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash((string)$password, PASSWORD_DEFAULT);
        }

        $this->pelangganModel->update($id, $data);
        
        session()->setFlashdata('success', 'Data pelanggan berhasil diupdate!');
        return redirect()->to('/admin/pelanggan');
    }
    
    // ========== HAPUS DATA (DELETE) ==========
    public function delete($id)
    {
        $id = (int)$id;  // ✅ Casting ke integer
        $pelanggan = $this->pelangganModel->find($id);
        
        if (!$pelanggan) {
            session()->setFlashdata('error', 'Data pelanggan tidak ditemukan!');
            return redirect()->to('/admin/pelanggan');
        }
        
        // Hapus foto identitas jika ada
        if ($pelanggan->foto_identitas && file_exists('uploads/identitas/' . $pelanggan->foto_identitas)) {
            unlink('uploads/identitas/' . $pelanggan->foto_identitas);
        }
        
        $this->pelangganModel->delete($id);
        session()->setFlashdata('success', 'Data pelanggan berhasil dihapus!');
        return redirect()->to('/admin/pelanggan');
    }
    
    // ========== API: GET DATA PELANGGAN (UNTUK AJAX) ==========
    public function getData($id)
    {
        $id = (int)$id;  // ✅ Casting ke integer
        $pelanggan = $this->pelangganModel->find($id);
        
        if ($pelanggan) {
            // ✅ Pastikan return JSON dengan tipe yang benar
            return $this->response->setJSON($pelanggan);
        }
        
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
    }
    // ========== TAMPIL FORM EDIT DI SAMPING (AJAX) ==========
public function editForm($id)
{
    $id = (int)$id;
    $pelanggan = $this->pelangganModel->find($id);
    
    if (!$pelanggan) {
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
    }
    
    return $this->response->setJSON($pelanggan);
}
}