<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PembayaranModel;
use App\Models\PenyewaanModel;
use App\Models\PelangganModel;
use App\Models\MobilModel;

class Pembayaran extends BaseController
{
    /** @var PembayaranModel */
    protected $pembayaranModel;
    /** @var PenyewaanModel */
    protected $penyewaanModel;
    
    public function __construct()
    {
        $this->pembayaranModel = new PembayaranModel();
        $this->penyewaanModel = new PenyewaanModel();
        helper(['form', 'url', 'asset', 'midtrans']);
    }
    
    // ========== HALAMAN UTAMA ==========
    public function index()
    {
        $payments = $this->pembayaranModel->getAllPayments();
        $stats = $this->pembayaranModel->getStats();
        $pendingPayments = $this->pembayaranModel->getPendingPayments();
        
        // Ambil sewa yang sudah disetujui untuk dropdown
        $approvedRentals = $this->penyewaanModel
            ->select('t_sewa.*, t_pelanggan.nama as nama_pelanggan, t_mobil.merk as mobil_merk')
            ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
            ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
            ->where('t_sewa.status_pengajuan', 'disetujui')
            ->where('t_sewa.status !=', 'batal')
            ->orderBy('t_sewa.tgl_sewa', 'DESC')
            ->findAll();

        // Ambil ID dari parameter URL jika ada
        $selectedId = $this->request->getGet('id');
        /** @var object|null $selectedPayment */
        $selectedPayment = null;
        
        if ($selectedId) {
            $selectedPayment = $this->pembayaranModel->getPaymentById($selectedId);
        }
        
        $data = [
            'title' => 'Pembayaran',
            'page_title' => 'Pembayaran',
            'active_menu' => 'pembayaran',
            'payments' => $payments,
            'stats' => $stats,
            'pending_payments' => $pendingPayments,
            'selected_payment' => $selectedPayment,
            'approved_rentals' => $approvedRentals,
            'midtrans_client_key' => env('midtrans.client_key', ''),
        ];
        
        return view('admin/pembayaran', $data);
    }
    
    // ========== PROSES PEMBAYARAN ==========
    public function process()
    {
        // Validasi input
        $rules = [
            'id_sewa'       => 'required|integer',
            'jumlah_bayar'  => 'required|string',
            'metode_bayar'  => 'required|in_list[tunai,transfer,ewallet]',
        ];

        // Conditional rules
        $metode = $this->request->getPost('metode_bayar');
        if ($metode === 'transfer') {
            $rules['jenis_bank'] = 'required';
        } elseif ($metode === 'ewallet') {
            $rules['jenis_ewallet'] = 'required';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id_sewa = $this->request->getPost('id_sewa');
        $jumlah_bayar = (float)str_replace(['Rp', '.', ','], '', $this->request->getPost('jumlah_bayar'));
        $metode_bayar = $this->request->getPost('metode_bayar');
        $jenis_bank = $this->request->getPost('jenis_bank');
        $jenis_ewallet = $this->request->getPost('jenis_ewallet');
        
        // Cek total tagihan dan sisa bayar
        /** @var object|null $sewa */
        $sewa = $this->penyewaanModel->find($id_sewa);
        if (!$sewa) {
            return redirect()->back()->with('error', 'Data sewa tidak ditemukan');
        }
        
        $totalDibayar = $this->pembayaranModel->getTotalPaidBySewa($id_sewa);
        $sisa = $sewa->sub_total - $totalDibayar;
        
        if ($jumlah_bayar <= 0) {
            return redirect()->back()->with('error', 'Jumlah bayar harus lebih dari 0');
        }

        // Tentukan status bayar
        $status_bayar = 'sebagian';
        if ($jumlah_bayar >= $sisa) {
            $status_bayar = 'lunas';
            // If user pays more than sisa, we only record up to sisa? 
            // Or record full amount but cap status at lunas.
        }
        
        $data = [
            'id_sewa'       => $id_sewa,
            'tgl_bayar'     => date('Y-m-d H:i:s'),
            'jumlah_bayar'  => $jumlah_bayar,
            'metode_bayar'  => $metode_bayar,
            'status_bayar'  => $status_bayar,
            'jenis_bank'    => $metode_bayar == 'transfer' ? $jenis_bank : null,
            'jenis_ewallet' => $metode_bayar == 'ewallet' ? $jenis_ewallet : null
        ];
        
        if ($this->pembayaranModel->save($data)) {
            return redirect()->to('/admin/pembayaran?id=' . $this->pembayaranModel->getInsertID())
                           ->with('success', 'Pembayaran berhasil diproses!');
        } else {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran');
        }
    }
    
    // ========== UPDATE STATUS PEMBAYARAN ==========
    public function updateStatus(int|string $id, string $status)
    {
        if ($this->pembayaranModel->update($id, ['status_bayar' => $status])) {
            return redirect()->to('/admin/pembayaran')->with('success', 'Status pembayaran berhasil diupdate');
        }
        return redirect()->back()->with('error', 'Gagal mengupdate status');
    }
    
    // ========== CETAK INVOICE ==========
    public function invoice(int|string $id)
    {
        /** @var object|null $payment */
        $payment = $this->pembayaranModel->getPaymentById($id);
        if (!$payment) {
            return redirect()->to('/admin/pembayaran')->with('error', 'Data tidak ditemukan');
        }
        
        $data['payment'] = $payment;
        return view('admin/invoice', $data);
    }
    
    // ========== GET SISA BAYAR (AJAX) ==========
    public function getSisaBayar(int|string $id_sewa)
    {
        /** @var object|null $sewa */
        $sewa = $this->penyewaanModel->find($id_sewa);
        if (!$sewa) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
        }
        
        $totalDibayar = $this->pembayaranModel->getTotalPaidBySewa($id_sewa);
        $sisa = $sewa->sub_total - $totalDibayar;
        
        return $this->response->setJSON([
            'total_tagihan' => $sewa->sub_total,
            'total_dibayar' => $totalDibayar,
            'sisa_bayar' => $sisa,
            'nama_pelanggan' => $sewa->nama_pelanggan ?? 'Unknown'
        ]);
    }
}