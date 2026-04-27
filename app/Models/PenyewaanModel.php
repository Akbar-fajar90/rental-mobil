<?php

namespace App\Models;

use CodeIgniter\Model;

class PenyewaanModel extends Model
{
    protected $table = 't_sewa';
    protected $primaryKey = 'id_sewa';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'id_pelanggan',
        'id_mobil',
        'id_pegawai',
        'tgl_sewa',
        'tgl_kembali_rencana',
        'tgl_kembali_aktual',
        'total_hari',
        'sub_total',
        'status',
        'status_pengajuan',
        'catatan_admin',
        'tgl_pengajuan'
    ];
    
    protected $useTimestamps = false;
    
    protected $validationRules = [
        'id_pelanggan' => 'required|integer|is_not_unique[t_pelanggan.id_pelanggan]',
        'id_mobil' => 'required|integer|is_not_unique[t_mobil.id_mobil]',
        'id_pegawai' => 'required|integer',
        'tgl_sewa' => 'required|valid_date',
        'tgl_kembali_rencana' => 'required|valid_date',
        'total_hari' => 'required|integer|greater_than[0]',
        'sub_total' => 'required|decimal|greater_than[0]',
        'status_pengajuan' => 'required|in_list[mengajukan,disetujui,ditolak]',
        'status' => 'required|in_list[Berlangsung,selesai,batal]'
    ];
    
    // ========== QUERY UNTUK DASHBOARD ==========
    
    /**
     * Get all pending rental requests (menunggu konfirmasi)
     * 
     * @return array<int, object>
     */
    public function getPendingRequests(): array
    {
        return $this->select('t_sewa.*, 
                              t_pelanggan.nama as nama_pelanggan, 
                              t_pelanggan.no_telp, 
                              t_pelanggan.email, 
                              t_pelanggan.foto_identitas,
                              t_mobil.merk, 
                              t_mobil.foto_mobil, 
                              t_mobil.tarif_per_hari,
                              t_pegawai.nama as nama_pegawai')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->join('t_pegawai', 't_pegawai.id_pegawai = t_sewa.id_pegawai', 'left')
                    ->where('t_sewa.status_pengajuan', 'mengajukan')
                    ->orderBy('t_sewa.tgl_pengajuan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get approved rental requests
     * 
     * @return array<int, object>
     */
    public function getApprovedRequests(): array
    {
        return $this->select('t_sewa.*, 
                              t_pelanggan.nama as nama_pelanggan, 
                              t_mobil.merk')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->where('t_sewa.status_pengajuan', 'disetujui')
                    ->where('t_sewa.status', 'Berlangsung')
                    ->orderBy('t_sewa.tgl_sewa', 'DESC')
                    ->findAll();
    }
    
    /**
     * Get rental history (approved & rejected)
     * 
     * @param int $limit
     * @param int $offset
     * @return array<int, object>
     */
    public function getHistory(int $limit = 10, int $offset = 0): array
    {
        return $this->select('t_sewa.*, 
                              t_pelanggan.nama as nama_pelanggan, 
                              t_mobil.merk')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->whereIn('t_sewa.status_pengajuan', ['disetujui', 'ditolak'])
                    ->orderBy('t_sewa.tgl_pengajuan', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }
    
    /**
     * Get total history count
     * 
     * @return int
     */
    public function getHistoryCount(): int
    {
        return $this->whereIn('status_pengajuan', ['disetujui', 'ditolak'])->countAllResults();
    }
    
    /**
     * Get statistics for dashboard
     * 
     * @return object
     */
    public function getStats(): object
    {
        $menunggu = $this->where('status_pengajuan', 'mengajukan')->countAllResults();
        $disetujui = $this->where('status_pengajuan', 'disetujui')->countAllResults();
        $ditolak = $this->where('status_pengajuan', 'ditolak')->countAllResults();
        $total = $menunggu + $disetujui + $ditolak;
        
        return (object)[
            'menunggu' => $menunggu,
            'disetujui' => $disetujui,
            'ditolak' => $ditolak,
            'total' => $total
        ];
    }
    
    /**
     * Get rental by customer ID
     * 
     * @param int $id_pelanggan
     * @param int $limit
     * @return array<int, object>
     */
    public function getByCustomer(int $id_pelanggan, int $limit = 10): array
    {
        return $this->select('t_sewa.*, t_mobil.merk, t_mobil.foto_mobil')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->where('id_pelanggan', $id_pelanggan)
                    ->orderBy('tgl_pengajuan', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get rental by car ID
     * 
     * @param int $id_mobil
     * @param int $limit
     * @return array<int, object>
     */
    public function getByCar(int $id_mobil, int $limit = 10): array
    {
        return $this->select('t_sewa.*, t_pelanggan.nama as nama_pelanggan')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->where('id_mobil', $id_mobil)
                    ->orderBy('tgl_sewa', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get monthly revenue report
     * 
     * @param int|null $year
     * @param int|null $month
     * @return object|null
     */
    public function getMonthlyRevenue(?int $year = null, ?int $month = null): ?object
    {
        $year = $year ?? date('Y');
        $month = $month ?? date('m');
        
        return $this->select('SUM(sub_total) as total_pendapatan, COUNT(*) as jumlah_transaksi')
                    ->where('YEAR(tgl_sewa)', $year)
                    ->where('MONTH(tgl_sewa)', $month)
                    ->where('status_pengajuan', 'disetujui')
                    ->where('status', 'selesai')
                    ->first();
    }
    
    /**
     * Get yearly revenue report
     * 
     * @param int|null $year
     * @return array<int, object>
     */
    public function getYearlyRevenue(?int $year = null): array
    {
        $year = $year ?? date('Y');
        
        return $this->select('MONTH(tgl_sewa) as bulan, 
                              SUM(sub_total) as total_pendapatan, 
                              COUNT(*) as jumlah_transaksi')
                    ->where('YEAR(tgl_sewa)', $year)
                    ->where('status_pengajuan', 'disetujui')
                    ->where('status', 'selesai')
                    ->groupBy('MONTH(tgl_sewa)')
                    ->orderBy('bulan', 'ASC')
                    ->findAll();
    }
    
    /**
     * Approve rental request
     * 
     * @param int $id
     * @param string|null $catatan
     * @return bool
     */
    public function approve(int $id, ?string $catatan = null): bool
    {
        return $this->update($id, [
            'status_pengajuan' => 'disetujui',
            'status' => 'Berlangsung',
            'catatan_admin' => $catatan ?? 'Disetujui oleh admin'
        ]);
    }
    
    /**
     * Reject rental request
     * 
     * @param int $id
     * @param string $alasan
     * @return bool
     */
    public function reject(int $id, string $alasan): bool
    {
        return $this->update($id, [
            'status_pengajuan' => 'ditolak',
            'status' => 'batal',
            'catatan_admin' => $alasan
        ]);
    }
    
    /**
     * Complete rental (return car)
     * 
     * @param int $id
     * @param string $tgl_kembali_aktual
     * @param float $denda
     * @return bool
     */
    public function complete(int $id, string $tgl_kembali_aktual, float $denda = 0): bool
    {
        $sewa = $this->find($id);
        
        if (!$sewa) {
            return false;
        }
        
        // Hitung keterlambatan
        $telat = 0;
        if (strtotime($tgl_kembali_aktual) > strtotime($sewa->tgl_kembali_rencana)) {
            $telat = ceil((strtotime($tgl_kembali_aktual) - strtotime($sewa->tgl_kembali_rencana)) / (60 * 60 * 24));
        }
        
        return $this->update($id, [
            'tgl_kembali_aktual' => $tgl_kembali_aktual,
            'status' => 'selesai',
            'catatan_admin' => ($sewa->catatan_admin ?? '') . " | Selesai pada: $tgl_kembali_aktual, Telat: $telat hari, Denda: Rp " . number_format($denda, 0, ',', '.')
        ]);
    }
}