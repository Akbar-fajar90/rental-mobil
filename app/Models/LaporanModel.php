<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table = 't_sewa';
    protected $primaryKey = 'id_sewa';
    protected $returnType = 'object';
    
    public function getSummary()
{
    $db = \Config\Database::connect();
    
    // Total pendapatan dari pembayaran yang lunas
    $totalPendapatan = $db->query("
        SELECT COALESCE(SUM(jumlah_bayar), 0) as total 
        FROM t_pembayaran 
        WHERE status_bayar = 'lunas'
    ")->getRow()->total ?? 0;
    
    // Total penyewaan yang selesai
    $totalPenyewaan = $this->where('status', 'selesai')->countAllResults();
    
    // Total denda dari pengembalian
    $totalDenda = $db->query("
        SELECT COALESCE(SUM(denda_terlambat + denda_kerusakan), 0) as total 
        FROM t_pengembalian
    ")->getRow()->total ?? 0;
    
    // Utilisasi armada
    $totalMobil = $db->query("SELECT COUNT(*) as total FROM t_mobil")->getRow()->total ?? 1;
    $mobilDisewa = $db->query("SELECT COUNT(*) as total FROM t_mobil WHERE status = 'disewa'")->getRow()->total ?? 0;
    $utilisasi = $totalMobil > 0 ? round(($mobilDisewa / $totalMobil) * 100) : 0;
    
    return (object)[
        'pendapatan' => $totalPendapatan,
        'penyewaan' => $totalPenyewaan,
        'denda' => $totalDenda,
        'utilisasi' => $utilisasi
    ];
}
    
    public function getLaporan($limit = 10, $offset = 0, $filters = [])
{
    $db = \Config\Database::connect();
    
    $sql = "
        SELECT 
            t_sewa.id_sewa,
            t_sewa.tgl_sewa,
            t_sewa.status,
            t_pelanggan.nama as nama_pelanggan,
            t_mobil.merk as mobil_merk,
            t_mobil.plat_nomor,
            COALESCE((
                SELECT SUM(jumlah_bayar) 
                FROM t_pembayaran 
                WHERE t_pembayaran.id_sewa = t_sewa.id_sewa 
                AND t_pembayaran.status_bayar = 'lunas'
            ), 0) as pendapatan,
            COALESCE((
                SELECT SUM(denda_terlambat + denda_kerusakan) 
                FROM t_pengembalian 
                WHERE t_pengembalian.id_sewa = t_sewa.id_sewa
            ), 0) as total_denda
        FROM t_sewa
        JOIN t_pelanggan ON t_pelanggan.id_pelanggan = t_sewa.id_pelanggan
        JOIN t_mobil ON t_mobil.id_mobil = t_sewa.id_mobil
        WHERE 1=1
    ";
    
    $params = [];
    
    // Apply filters
    if (!empty($filters['start_date'])) {
        $sql .= " AND t_sewa.tgl_sewa >= ?";
        $params[] = $filters['start_date'];
    }
    if (!empty($filters['end_date'])) {
        $sql .= " AND t_sewa.tgl_sewa <= ?";
        $params[] = $filters['end_date'] . ' 23:59:59';
    }
    if (!empty($filters['status'])) {
        $sql .= " AND t_sewa.status = ?";
        $params[] = $filters['status'];
    }
    if (!empty($filters['search'])) {
        $sql .= " AND (t_pelanggan.nama LIKE ? OR t_mobil.merk LIKE ? OR t_sewa.id_sewa LIKE ?)";
        $params[] = "%{$filters['search']}%";
        $params[] = "%{$filters['search']}%";
        $params[] = "%{$filters['search']}%";
    }
    
    $sql .= " ORDER BY t_sewa.tgl_sewa DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    return $db->query($sql, $params)->getResult();
}

// Get total laporan count
public function getLaporanCount($filters = [])
{
    $db = \Config\Database::connect();
    
    $sql = "
        SELECT COUNT(*) as total
        FROM t_sewa
        JOIN t_pelanggan ON t_pelanggan.id_pelanggan = t_sewa.id_pelanggan
        JOIN t_mobil ON t_mobil.id_mobil = t_sewa.id_mobil
        WHERE 1=1
    ";
    
    $params = [];
    
    if (!empty($filters['start_date'])) {
        $sql .= " AND t_sewa.tgl_sewa >= ?";
        $params[] = $filters['start_date'];
    }
    if (!empty($filters['end_date'])) {
        $sql .= " AND t_sewa.tgl_sewa <= ?";
        $params[] = $filters['end_date'] . ' 23:59:59';
    }
    if (!empty($filters['status'])) {
        $sql .= " AND t_sewa.status = ?";
        $params[] = $filters['status'];
    }
    if (!empty($filters['search'])) {
        $sql .= " AND (t_pelanggan.nama LIKE ? OR t_mobil.merk LIKE ? OR t_sewa.id_sewa LIKE ?)";
        $params[] = "%{$filters['search']}%";
        $params[] = "%{$filters['search']}%";
        $params[] = "%{$filters['search']}%";
    }
    
    $result = $db->query($sql, $params)->getRow();
    return $result->total ?? 0;
}
    
    // Get chart data (weekly revenue)
public function getChartData()
{
    $db = \Config\Database::connect();
    
    $result = $db->query("
        SELECT 
            DAYOFWEEK(tgl_bayar) as day_num,
            DAYNAME(tgl_bayar) as day_name,
            COALESCE(SUM(jumlah_bayar), 0) as total
        FROM t_pembayaran
        WHERE status_bayar = 'lunas'
            AND tgl_bayar >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DAYOFWEEK(tgl_bayar), DAYNAME(tgl_bayar)
        ORDER BY day_num
    ")->getResult();
    
    $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
    $chartData = array_fill(0, 7, 0);
    
    foreach ($result as $row) {
        // Convert MySQL DAYOFWEEK (1=Sunday, 2=Monday, ...) to index (0=Monday)
        $dayIndex = ($row->day_num - 2 + 7) % 7;
        $chartData[$dayIndex] = round($row->total / 1000000, 1);
    }
    
    return $chartData;
}
    
    // Get monthly revenue report
    public function getMonthlyRevenue($year = null)
    {
        $year = $year ?? date('Y');
        $db = \Config\Database::connect();
        
        return $db->query("
            SELECT 
                MONTH(tgl_bayar) as bulan,
                SUM(jumlah_bayar) as total
            FROM t_pembayaran
            WHERE status_bayar = 'lunas'
                AND YEAR(tgl_bayar) = ?
            GROUP BY MONTH(tgl_bayar)
            ORDER BY bulan
        ", [$year])->getResult();
    }

}