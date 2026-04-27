<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PenyewaanModel;
use App\Models\PelangganModel;
use App\Models\MobilModel;

class Penyewaan extends BaseController
{
    protected PenyewaanModel $penyewaanModel;
    protected PelangganModel $pelangganModel;
    protected MobilModel $mobilModel;
    
    public function __construct()
    {
        $this->penyewaanModel = new PenyewaanModel();
        $this->pelangganModel = new PelangganModel();
        $this->mobilModel = new MobilModel();
        helper(['form', 'url']);
    }
    
    // ========== HALAMAN UTAMA ==========
    public function index()
    {
        $data = [
            'title' => 'Konfirmasi Penyewaan',
            'page_title' => 'Penyewaan',
            'active_menu' => 'penyewaan',
            'pending_requests' => $this->penyewaanModel->getPendingRequests(),
            'history' => $this->penyewaanModel->getHistory(10),
            'stats' => $this->penyewaanModel->getStats()
        ];
        
        return view('admin/penyewaan', $data);
    }
    
    // ========== VALIDASI DOKUMEN PELANGGAN ==========
    public function validateDocuments($id_pelanggan)
    {
        $id_pelanggan = (int)$id_pelanggan;
        /** @var object|null $pelanggan */
        $pelanggan = $this->pelangganModel->find($id_pelanggan);
        
        if (!$pelanggan) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Data pelanggan tidak ditemukan'
            ]);
        }
        
        $valid = true;
        $missing = [];
        $documents = [];
        
        // Cek NIK
        if (empty($pelanggan->nik) || strlen($pelanggan->nik) < 16) {
            $valid = false;
            $missing[] = 'NIK (Nomor Induk Kependudukan)';
        } else {
            $documents[] = ['name' => 'NIK', 'status' => 'valid', 'value' => $pelanggan->nik];
        }
        
        // Cek No Telepon
        if (empty($pelanggan->no_telp) || strlen($pelanggan->no_telp) < 10) {
            $valid = false;
            $missing[] = 'Nomor Telepon';
        } else {
            $documents[] = ['name' => 'Nomor Telepon', 'status' => 'valid', 'value' => $pelanggan->no_telp];
        }
        
        // Cek Alamat
        if (empty($pelanggan->alamat)) {
            $valid = false;
            $missing[] = 'Alamat Lengkap';
        } else {
            $documents[] = ['name' => 'Alamat', 'status' => 'valid', 'value' => $pelanggan->alamat];
        }
        
        // Cek Email
        if (!empty($pelanggan->email)) {
            $documents[] = ['name' => 'Email', 'status' => 'valid', 'value' => $pelanggan->email];
        } else {
            $documents[] = ['name' => 'Email', 'status' => 'warning', 'value' => 'Tidak diisi'];
        }
        
        // Cek No SIM
        if (!empty($pelanggan->no_sim)) {
            $documents[] = ['name' => 'SIM', 'status' => 'valid', 'value' => $pelanggan->no_sim];
        } else {
            $documents[] = ['name' => 'SIM', 'status' => 'warning', 'value' => 'Tidak diisi (opsional)'];
        }
        
        // Cek Foto Identitas
        if (empty($pelanggan->foto_identitas) || !file_exists('uploads/identitas/' . $pelanggan->foto_identitas)) {
            $valid = false;
            $missing[] = 'Foto KTP/Identitas';
        } else {
            $documents[] = [
                'name' => 'Foto KTP', 
                'status' => 'valid', 
                'value' => $pelanggan->foto_identitas,
                'image_url' => base_url('uploads/identitas/' . $pelanggan->foto_identitas)
            ];
        }
        
        return $this->response->setJSON([
            'valid' => $valid,
            'message' => $valid ? 'Semua dokumen lengkap' : 'Dokumen tidak lengkap',
            'missing' => $missing,
            'documents' => $documents
        ]);
    }
    
    // ========== CEK KETERSEDIAAN MOBIL ==========
    public function checkAvailability($id_mobil, $tgl_sewa, $tgl_kembali)
    {
        $id_mobil = (int)$id_mobil;
        
        // Cek apakah mobil sudah disewa di tanggal yang sama
        /** @var object|null $existing */
        $existing = $this->penyewaanModel
            ->where('id_mobil', $id_mobil)
            ->where('status_pengajuan', 'disetujui')
            ->groupStart()
                ->where('tgl_sewa <=', $tgl_kembali)
                ->where('tgl_kembali_rencana >=', $tgl_sewa)
            ->groupEnd()
            ->first();
        
        /** @var object|null $mobil */
        $mobil = $this->mobilModel->find($id_mobil);
        
        if ($existing) {
            return $this->response->setJSON([
                'available' => false,
                'message' => 'Mobil ' . ($mobil->merk ?? '') . ' sudah disewa pada periode tersebut'
            ]);
        }
        
        if ($mobil && $mobil->status != 'tersedia') {
            return $this->response->setJSON([
                'available' => false,
                'message' => 'Mobil sedang tidak tersedia (status: ' . $mobil->status . ')'
            ]);
        }
        
        return $this->response->setJSON([
            'available' => true,
            'message' => 'Mobil tersedia'
        ]);
    }
    
    // ========== KONFIRMASI PENYEWAAN (APPROVE) ==========
    public function approve($id)
    {
        $id = (int)$id;
        /** @var object|null $sewa */
        $sewa = $this->penyewaanModel->find($id);
        
        if (!$sewa) {
            session()->setFlashdata('error', 'Data penyewaan tidak ditemukan!');
            return redirect()->to('/admin/penyewaan');
        }
        
        // Validasi dokumen pelanggan
        /** @var object|null $pelanggan */
        $pelanggan = $this->pelangganModel->find($sewa->id_pelanggan);
        
        if (!$pelanggan || empty($pelanggan->nik) || empty($pelanggan->foto_identitas)) {
            session()->setFlashdata('error', 'Dokumen pelanggan tidak lengkap! Silakan lengkapi data pelanggan terlebih dahulu.');
            return redirect()->to('/admin/penyewaan');
        }
        
        // Update status pengajuan
        $db = \Config\Database::connect();
        $db->transStart();
        // Update status pengajuan
        $this->penyewaanModel->update($id, [
            'status_pengajuan' => 'disetujui',
            'status' => 'Berlangsung',
            'catatan_admin' => $this->request->getPost('catatan') ?? 'Disetujui oleh admin'
        ]);
        
        // Update status mobil menjadi disewa
        $this->mobilModel->update($sewa->id_mobil, [
            'status' => 'disewa'
        ]);
        
        $db->transComplete();
        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyetujui penyewaan!');
        } else {
            session()->setFlashdata('success', 'Penyewaan berhasil disetujui!');
        }
        
        
        session()->setFlashdata('success', 'Penyewaan berhasil disetujui!');
        return redirect()->to('/admin/penyewaan');
    }
    
    // ========== TOLAK PENYEWAAN (REJECT) ==========
    public function reject($id)
    {
        $id = (int)$id;
        /** @var object|null $sewa */
        $sewa = $this->penyewaanModel->find($id);
        
        if (!$sewa) {
            session()->setFlashdata('error', 'Data penyewaan tidak ditemukan!');
            return redirect()->to('/admin/penyewaan');
        }
        
        $alasan = $this->request->getPost('alasan') ?? 'Ditolak oleh admin';
        
        // Update status pengajuan
        $this->penyewaanModel->update($id, [
            'status_pengajuan' => 'ditolak',
            'status' => 'batal',
            'catatan_admin' => $alasan
        ]);
        
        session()->setFlashdata('success', 'Penyewaan berhasil ditolak!');
        return redirect()->to('/admin/penyewaan');
    }
    
    // ========== DETAIL PENYEWAAN (UNTUK MODAL) ==========
    public function detail($id)
    {
        $id = (int)$id;
        /** @var object|null $sewa */
        $sewa = $this->penyewaanModel->select('t_sewa.*, t_pelanggan.nama as nama_pelanggan, t_pelanggan.no_telp, t_pelanggan.email, t_pelanggan.alamat, t_pelanggan.nik, t_pelanggan.foto_identitas, t_mobil.merk, t_mobil.plat_nomor, t_mobil.foto_mobil, t_mobil.tarif_per_hari')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->find($id);
        
        if (!$sewa) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
        }
        
        return $this->response->setJSON($sewa);
    }
    
    // ========== CETAK BUKTI ==========
    public function print($id)
    {
        $id = (int)$id;
        /** @var object|null $sewa */
        $sewa = $this->penyewaanModel->select('t_sewa.*, t_pelanggan.nama as nama_pelanggan, t_pelanggan.no_telp, t_pelanggan.email, t_pelanggan.alamat, t_mobil.merk, t_mobil.plat_nomor, t_mobil.tarif_per_hari')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->find($id);
        
        if (!$sewa) {
            return redirect()->to('/admin/penyewaan')->with('error', 'Data tidak ditemukan');
        }
        
        $data['sewa'] = $sewa;
        return view('admin/penyewaan_print', $data);
    }
}