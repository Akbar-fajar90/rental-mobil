<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table = 't_pelanggan';
    protected $primaryKey = 'id_pelanggan';
    protected $useAutoIncrement = true;
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nama', 'nik', 'no_telp', 'alamat', 'email', 'password', 'no_sim', 'foto_identitas', 'provider', 'provider_id', 'avatar', 'last_login'
    ];

    protected $useTimestamps = false;
    
    protected $validationRules = [
        'nama' => 'required|min_length[3]|max_length[100]',
        'nik' => 'permit_empty|min_length[16]|max_length[20]',
        'no_telp' => 'permit_empty|min_length[10]|max_length[15]',
        'alamat' => 'permit_empty|min_length[5]|max_length[100]',
        'email' => 'required|valid_email|max_length[100]',
        'no_sim' => 'permit_empty|max_length[20]'
    ];

    /**
     * Get rental history for a specific customer
     */
    public function getHistoryByCustomer($id_pelanggan, $limit = 10, $offset = 0, $filter = 'semua')
    {
        $builder = $this->db->table('t_sewa');
        $builder->select('t_sewa.*, t_mobil.merk, t_mobil.plat_nomor, t_mobil.foto_mobil, t_pembayaran.status_bayar');
        $builder->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil');
        $builder->join('t_pembayaran', 't_pembayaran.id_sewa = t_sewa.id_sewa', 'left');
        $builder->where('t_sewa.id_pelanggan', $id_pelanggan);

        if ($filter !== 'semua') {
            if ($filter === 'menunggu') $builder->where('t_sewa.status_pengajuan', 'mengajukan');
            elseif ($filter === 'disetujui') $builder->where('t_sewa.status_pengajuan', 'disetujui');
            elseif ($filter === 'ditolak') $builder->where('t_sewa.status_pengajuan', 'ditolak');
            elseif ($filter === 'selesai') $builder->where('t_sewa.status', 'selesai');
        }

        $builder->orderBy('t_sewa.tgl_pengajuan', 'DESC');
        
        $data = $builder->get($limit, $offset)->getResultArray();
        $count = $builder->countAllResults(false);

        return [
            'data' => $data,
            'total' => $count
        ];
    }

    /**
     * Get detailed rental info, ensuring it belongs to the logged-in customer
     */
    public function getSewaDetail($id_sewa, $id_pelanggan)
    {
        return $this->db->table('t_sewa')
            ->select('t_sewa.*, t_mobil.merk, t_mobil.plat_nomor, t_mobil.foto_mobil, t_mobil.tarif_per_hari,
                      t_pelanggan.nama, t_pelanggan.email, t_pelanggan.no_telp,
                      t_pembayaran.status_bayar, t_pembayaran.metode_bayar, t_pembayaran.jumlah_bayar,
                      t_pengembalian.tgl_kembali, (t_pengembalian.denda_terlambat + t_pengembalian.denda_kerusakan) as denda, t_pengembalian.kondisi_mobil')
            ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
            ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
            ->join('t_pembayaran', 't_pembayaran.id_sewa = t_sewa.id_sewa', 'left')
            ->join('t_pengembalian', 't_pengembalian.id_sewa = t_sewa.id_sewa', 'left')
            ->where('t_sewa.id_sewa', $id_sewa)
            ->where('t_sewa.id_pelanggan', $id_pelanggan)
            ->get()
            ->getRowArray();
    }
}