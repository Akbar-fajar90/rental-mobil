<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Mobil extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Ambil semua data mobil
        $mobil_list = $db->query("
            SELECT * FROM t_mobil ORDER BY id_mobil DESC
        ")->getResult();
        
        // Hitung statistik
        $total_armada = $db->query("SELECT COUNT(*) as total FROM t_mobil")->getRow()->total;
        $total_tersedia = $db->query("SELECT COUNT(*) as total FROM t_mobil WHERE status = 'tersedia'")->getRow()->total;
        $total_disewa = $db->query("SELECT COUNT(*) as total FROM t_mobil WHERE status = 'disewa'")->getRow()->total;
        
        $data = [
            'title' => 'Daftar Mobil',
            'page_title' => 'Manajemen Mobil',
            'active_menu' => 'mobil',
            'mobil_list' => $mobil_list,
            'total_armada' => $total_armada,
            'total_tersedia' => $total_tersedia,
            'total_disewa' => $total_disewa
        ];
        
        return view('admin/mobil', $data);
    }
    
    // ========== GET DATA MOBIL UNTUK EDIT (AJAX) ==========
    public function getMobil($id)
    {
        $db = \Config\Database::connect();
        $mobil = $db->query("SELECT * FROM t_mobil WHERE id_mobil = ?", [$id])->getRow();
        
        if ($mobil) {
            return $this->response->setJSON($mobil);
        }
        
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
    }
    
    // ========== SIMPAN DATA BARU ==========
    public function simpan()
    {
        $db = \Config\Database::connect();
        
        // Upload file ke folder assets/img
        $foto = $this->request->getFile('foto_mobil');
        $nama_foto = '';
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $nama_foto = $foto->getRandomName();
            $foto->move(FCPATH . 'assets/img', $nama_foto);
        }
        
        // Simpan ke database
        $db->query("
            INSERT INTO t_mobil (plat_nomor, merk, tahun, foto_mobil, tarif_per_hari, denda_per_hari, status, device_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $this->request->getPost('plat_nomor'),
            $this->request->getPost('merk'),
            $this->request->getPost('tahun'),
            $nama_foto,
            $this->request->getPost('tarif_per_hari'),
            $this->request->getPost('denda_per_hari'),
            $this->request->getPost('status'),
            $this->request->getPost('device_id')
        ]);
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil ditambahkan');
    }
    
    // ========== UPDATE DATA ==========
    public function update($id)
    {
        $db = \Config\Database::connect();
        
        // Upload file baru jika ada
        $foto = $this->request->getFile('foto_mobil');
        $nama_foto = $this->request->getPost('foto_mobil_lama');
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama
            if ($nama_foto && file_exists(FCPATH . 'assets/img/' . $nama_foto)) {
                unlink(FCPATH . 'assets/img/' . $nama_foto);
            }
            $nama_foto = $foto->getRandomName();
            $foto->move(FCPATH . 'assets/img', $nama_foto);
        }
        
        // Update database
        $db->query("
            UPDATE t_mobil 
            SET plat_nomor = ?, merk = ?, tahun = ?, foto_mobil = ?, tarif_per_hari = ?, denda_per_hari = ?, status = ?, device_id = ?
            WHERE id_mobil = ?
        ", [
            $this->request->getPost('plat_nomor'),
            $this->request->getPost('merk'),
            $this->request->getPost('tahun'),
            $nama_foto,
            $this->request->getPost('tarif_per_hari'),
            $this->request->getPost('denda_per_hari'),
            $this->request->getPost('status'),
            $this->request->getPost('device_id'),
            $id
        ]);
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil diupdate');
    }
    
    // ========== HAPUS DATA ==========
    public function hapus($id)
    {
        $db = \Config\Database::connect();
        
        // Ambil nama foto untuk dihapus
        $mobil = $db->query("SELECT foto_mobil FROM t_mobil WHERE id_mobil = ?", [$id])->getRow();
        if ($mobil && !empty($mobil->foto_mobil)) {
            $safe_filename = basename($mobil->foto_mobil);
            $filepath = FCPATH . 'assets/img/' . $safe_filename;
            
            if (file_exists($filepath) && is_file($filepath)) {
                unlink($filepath);
            }
        }
        
        $db->query("DELETE FROM t_mobil WHERE id_mobil = ?", [$id]);
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil dihapus');
    }
}