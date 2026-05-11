<?php

namespace App\Models;

use CodeIgniter\Model;

class PengembalianModel extends Model
{
    protected $table = 't_pengembalian';
    protected $primaryKey = 'id_pengembalian';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    
    protected $allowedFields = [
        'id_sewa',
        'id_pegawai',
        'tgl_kembali',
        'kondisi_mobil',
        'denda_terlambat',
        'denda_kerusakan'
    ];
    
    protected $validationRules = [
        'id_sewa' => 'required|is_not_unique[t_sewa.id_sewa]',
        'id_pegawai' => 'required|is_not_unique[t_pegawai.id_pegawai]',
        'tgl_kembali' => 'required|valid_date',
        'kondisi_mobil' => 'required|in_list[baik,rusak-ringan,rusak-berat]'
    ];
    
    // Get all return history
    public function getHistory($limit = 10)
    {
        return $this->select('t_pengembalian.*, 
                              t_sewa.id_sewa as sewa_id,
                              t_sewa.tgl_sewa,
                              t_sewa.tgl_kembali_rencana,
                              t_sewa.sub_total,
                              t_pelanggan.nama as nama_pelanggan,
                              t_pelanggan.no_telp,
                              t_mobil.merk as mobil_merk,
                              t_mobil.plat_nomor,
                              t_pegawai.nama as nama_pegawai')
                    ->join('t_sewa', 't_sewa.id_sewa = t_pengembalian.id_sewa')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->join('t_pegawai', 't_pegawai.id_pegawai = t_pengembalian.id_pegawai', 'left')
                    ->orderBy('t_pengembalian.tgl_kembali', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    // Get rental by ID for return process
    public function getRentalById($id_sewa)
    {
        return $this->db->table('t_sewa')
                    ->select('t_sewa.*, 
                              t_pelanggan.nama as nama_pelanggan,
                              t_pelanggan.no_telp,
                              t_pelanggan.email,
                              t_pelanggan.alamat,
                              t_mobil.merk as mobil_merk,
                              t_mobil.plat_nomor,
                              t_mobil.tarif_per_hari,
                              t_mobil.denda_per_hari')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->where('t_sewa.id_sewa', $id_sewa)
                    ->where('t_sewa.status', 'Berlangsung')
                    ->get()
                    ->getRow();
    }
    
    // Process return
    public function processReturn($id_sewa, $data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Ambil data sewa SEBELUM update status
        $sewaModel = new \App\Models\PenyewaanModel();
        $sewa = $sewaModel->where('id_sewa', $id_sewa)->first();

        // Insert to pengembalian
        $this->insert($data);

        // Update sewa status
        $sewaModel->update($id_sewa, [
            'status'             => 'selesai',
            'tgl_kembali_aktual' => $data['tgl_kembali']
        ]);

        // Update mobil status to available
        if ($sewa) {
            $mobilModel = new \App\Models\MobilModel();
            $mobilModel->update($sewa->id_mobil, ['status' => 'tersedia']);
        }

        $db->transComplete();
        return $db->transStatus();
    }
    
    // Calculate late fee
    public function calculateLateFee($tgl_kembali_rencana, $tgl_kembali_aktual, $denda_per_hari)
    {
        $rencana = new \DateTime($tgl_kembali_rencana);
        $aktual = new \DateTime($tgl_kembali_aktual);
        
        if ($aktual <= $rencana) {
            return 0;
        }
        
        $diff = $rencana->diff($aktual);
        $daysLate = $diff->days;
        
        return $daysLate * $denda_per_hari;
    }
    /**
     * Calculate damage fee based on business rules
     * - Ringan: 20% of daily rate
     * - Berat: 50% of daily rate + manual service cost (if provided)
     */
    public function calculateDamageFee($daily_rate, $condition, $manual_cost = 0)
    {
        switch ($condition) {
            case 'rusak-ringan':
                return $daily_rate * 0.2;
            case 'rusak-berat':
                return ($daily_rate * 0.5) + (float)$manual_cost;
            case 'baik':
            default:
                return 0;
        }
    }
}