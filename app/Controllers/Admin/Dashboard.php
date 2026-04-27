<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // 1. Ambil data dari view v_dashboard_summary
        $summary = $db->query("SELECT * FROM v_dashboard_summary")->getRow();
        
        // 2. Ambil total pelanggan
        $totalPelanggan = $db->query("SELECT COUNT(*) as total FROM t_pelanggan")->getRow()->total;
        
        // 3. Ambil mobil populer (top performer)
        $mobilPopuler = $db->query("
            SELECT 
                m.id_mobil,
                m.merk,
                m.tahun,
                m.tarif_per_hari,
                m.foto_mobil,
                COUNT(s.id_sewa) AS total_disewa
            FROM t_mobil m
            LEFT JOIN t_sewa s ON m.id_mobil = s.id_mobil
            WHERE s.status = 'selesai' OR s.status IS NULL
            GROUP BY m.id_mobil
            ORDER BY total_disewa DESC
            LIMIT 3
        ")->getResult();
        
        // 4. Ambil armada aktif (sedang disewa) - tanpa GPS
        $armadaAktif = $db->query("
            SELECT 
                m.merk,
                m.plat_nomor,
                s.tgl_sewa,
                s.tgl_kembali_rencana
            FROM t_sewa s
            JOIN t_mobil m ON s.id_mobil = m.id_mobil
            WHERE s.status = 'Berlangsung' AND s.status_pengajuan = 'disetujui'
            LIMIT 5
        ")->getResult();
        
        // 5. Ambil armada aktif dengan data GPS (untuk peta)
        $armadaAktiflive = $db->table('t_sewa s')  
            ->select('s.*, m.merk, m.plat_nomor, g.latitude, g.longitude, g.waktu as last_update')
            ->join('t_mobil m', 'm.id_mobil = s.id_mobil')  
            ->join('(SELECT device_id, latitude, longitude, waktu, 
                           ROW_NUMBER() OVER (PARTITION BY device_id ORDER BY waktu DESC) as rn 
                    FROM t_gps) g', 'g.device_id = m.device_id AND g.rn = 1', 'left')  
            ->where('s.status', 'Berlangsung')  
            ->where('s.status_pengajuan', 'disetujui')
            ->get()
            ->getResult();
        
        $data = [
            'title' => 'Dashboard',
            'page_title' => 'Dashboard',
            'active_menu' => 'dashboard',
            'total_pelanggan' => $totalPelanggan,
            'sewa_berlangsung' => $summary->sewa_berlangsung ?? 0,
            'mobil_tersedia' => $summary->mobil_tersedia ?? 0,
            'mobil_disewa' => $summary->mobil_disewa ?? 0,
            'pendapatan_bulan_ini' => $summary->pendapatan_bulan_ini ?? 0,
            'mobil_populer' => $mobilPopuler,
            'armada_aktif' => $armadaAktif,

            'armada_aktif_live' => $armadaAktiflive
        ];



        return view('admin/dashboard', $data);
    }
}