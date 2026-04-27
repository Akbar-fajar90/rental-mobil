<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranModel extends Model
{
    protected $table = 't_pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    
    protected $allowedFields = [
        'id_sewa', 'tgl_bayar', 'jumlah_bayar', 
        'metode_bayar', 'status_bayar', 'jenis_ewallet', 'jenis_bank'
    ];
    
    protected $validationRules = [
        'id_sewa' => 'required|is_not_unique[t_sewa.id_sewa]',
        'jumlah_bayar' => 'required|numeric|greater_than[0]',
        'metode_bayar' => 'required|in_list[tunai,ewallet,transfer]'
    ];
    
    // Get all payments with join
    public function getAllPayments()
    {
        return $this->select('t_pembayaran.*, 
                              t_sewa.id_sewa as sewa_id,
                              t_sewa.sub_total as total_tagihan,
                              t_sewa.status_pengajuan,
                              t_pelanggan.nama as nama_pelanggan,
                              t_pelanggan.no_telp,
                              t_mobil.merk as mobil_merk')
                    ->join('t_sewa', 't_sewa.id_sewa = t_pembayaran.id_sewa')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->orderBy('t_pembayaran.tgl_bayar', 'DESC')
                    ->findAll();
    }
    
    // Get payment by ID
    public function getPaymentById($id)
    {
        return $this->select('t_pembayaran.*, 
                              t_sewa.id_sewa as sewa_id,
                              t_sewa.sub_total as total_tagihan,
                              t_sewa.status_pengajuan,
                              t_pelanggan.nama as nama_pelanggan,
                              t_pelanggan.no_telp,
                              t_pelanggan.email,
                              t_mobil.merk as mobil_merk,
                              t_mobil.plat_nomor')
                    ->join('t_sewa', 't_sewa.id_sewa = t_pembayaran.id_sewa')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->where('t_pembayaran.id_pembayaran', $id)
                    ->first();
    }
    
    // Get payments by rental ID
    public function getPaymentsBySewa($id_sewa)
    {
        return $this->where('id_sewa', $id_sewa)
                    ->orderBy('tgl_bayar', 'DESC')
                    ->findAll();
    }
    
    // Get total paid for a rental
    public function getTotalPaidBySewa($id_sewa)
    {
        return $this->selectSum('jumlah_bayar')
                    ->where('id_sewa', $id_sewa)
                    ->where('status_bayar', 'lunas')
                    ->get()
                    ->getRow()
                    ->jumlah_bayar ?? 0;
    }
    
    // Get pending payments (perlu konfirmasi)
    public function getPendingPayments()
    {
        return $this->select('t_pembayaran.*, 
                              t_sewa.sub_total as total_tagihan,
                              t_pelanggan.nama as nama_pelanggan,
                              t_mobil.merk as mobil_merk')
                    ->join('t_sewa', 't_sewa.id_sewa = t_pembayaran.id_sewa')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->where('t_pembayaran.status_bayar', 'belum')
                    ->orderBy('t_pembayaran.tgl_bayar', 'ASC')
                    ->findAll();
    }
    
    // Get statistics
    public function getStats()
    {
        $db = \Config\Database::connect();
        
        $totalPendapatan = $this->selectSum('jumlah_bayar')
                               ->where('status_bayar', 'lunas')
                               ->get()
                               ->getRow()
                               ->jumlah_bayar ?? 0;
        
        $totalTransaksi = $this->countAll();
        $pendingCount = $this->where('status_bayar', 'belum')->countAllResults();
        $lunasCount = $this->where('status_bayar', 'lunas')->countAllResults();
        
        return (object)[
            'total_pendapatan' => $totalPendapatan,
            'total_transaksi' => $totalTransaksi,
            'pending' => $pendingCount,
            'lunas' => $lunasCount
        ];
    }
}