<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MobilModel;
use App\Models\PelangganModel;
use App\Models\PenyewaanModel;

class Pelanggan extends BaseController
{
    protected $mobilModel;
    protected $pelangganModel;
    protected $penyewaanModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->mobilModel = new MobilModel();
        $this->pelangganModel = new PelangganModel();
        $this->penyewaanModel = new PenyewaanModel();
        helper(['form', 'url', 'asset', 'wiki', 'date']);
    }

    public function index()
    {
        // 1. Mobil Unggulan (Top 3 mobil terpopuler berdasarkan jumlah penyewaan)
        $popularCars = $this->penyewaanModel->asArray()->select('t_mobil.*, COUNT(t_sewa.id_mobil) as total_sewa')
            ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
            ->groupBy('t_sewa.id_mobil')
            ->orderBy('total_sewa', 'DESC')
            ->limit(3)
            ->findAll();

        if (empty($popularCars)) {
            $popularCars = $this->mobilModel->where('status', 'tersedia')->limit(3)->findAll();
        }

        $stats = [
            'total_mobil' => $this->mobilModel->countAll(),
            'total_pelanggan' => $this->pelangganModel->countAll(),
            'total_sewa' => $this->penyewaanModel->countAll(),
            'jarak_tempuh' => '150.000+'
        ];

        $data = [
            'title' => 'Beranda - Rental Mobil',
            'popularCars' => $popularCars,
            'stats' => $stats
        ];

        return view('pelanggan/dashboard', $data);
    }

    public function mobil()
    {
        $merk = $this->request->getVar('merk');
        $tahun = $this->request->getVar('tahun');
        $harga_min = $this->request->getVar('harga_min');
        $harga_max = $this->request->getVar('harga_max');

        $query = $this->mobilModel->where('status', 'tersedia');

        if ($merk) $query->like('merk', $merk);
        if ($tahun) $query->where('tahun', $tahun);
        if ($harga_min) $query->where('tarif_per_hari >=', $harga_min);
        if ($harga_max) $query->where('tarif_per_hari <=', $harga_max);

        $data = [
            'title' => 'Daftar Mobil - Rental Mobil',
            'mobil' => $query->paginate(9, 'mobil'),
            'pager' => $this->mobilModel->pager,
            'filters' => [
                'merk' => $merk,
                'tahun' => $tahun,
                'harga_min' => $harga_min,
                'harga_max' => $harga_max
            ]
        ];

        return view('pelanggan/mobil', $data);
    }

    public function detail($id)
    {
        $mobil = $this->mobilModel->find($id);

        if (!$mobil) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Mobil tidak ditemukan");
        }

        $wikiInfo = get_car_info($mobil['merk']);
        $otherCars = $this->mobilModel->where('id_mobil !=', $id)
            ->where('status', 'tersedia')
            ->limit(3)
            ->findAll();

        $data = [
            'title' => 'Detail ' . $mobil['merk'] . ' - Rental Mobil',
            'mobil' => $mobil,
            'wiki' => $wikiInfo,
            'otherCars' => $otherCars
        ];

        return view('pelanggan/detail', $data);
    }

    // ==========================================
    // RENTAL PROCESS (Logged in Customers only)
    // ==========================================

    public function sewa_form($id_mobil = null)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $user = $this->pelangganModel->find($id_pelanggan);

        $mobil_tersedia = $this->mobilModel->where('status', 'tersedia')->findAll();
        $mobil_selected = null;
        if ($id_mobil) {
            $mobil_selected = $this->mobilModel->find($id_mobil);
        }

        $data = [
            'title' => 'Form Pengajuan Sewa',
            'mobil' => $mobil_tersedia,
            'selected_mobil' => $mobil_selected,
            'user' => $user
        ];

        return view('pelanggan/sewa_form', $data);
    }

    public function proses_sewa()
    {
        $rules = [
            'id_mobil' => 'required',
            'tgl_sewa' => 'required|valid_date',
            'tgl_kembali_rencana' => 'required|valid_date',
            'nik' => 'required|min_length[16]',
            'no_telp' => 'required',
            'alamat' => 'required',
            'no_sim' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $id_pelanggan = session()->get('id_pelanggan');
        $id_mobil = $this->request->getPost('id_mobil');
        $mobil = $this->mobilModel->find($id_mobil);
        
        $tgl_sewa = $this->request->getPost('tgl_sewa');
        $tgl_kembali = $this->request->getPost('tgl_kembali_rencana');
        
        $diff = date_diff(date_create($tgl_sewa), date_create($tgl_kembali));
        $durasi = $diff->days ?: 1;
        $total = $durasi * $mobil['tarif_per_hari'];

        $fileKtp = $this->request->getFile('dokumen_ktp');
        $fileSim = $this->request->getFile('dokumen_sim');
        
        $ktpName = null;
        $simName = null;

        if (!is_dir('uploads/identitas/')) mkdir('uploads/identitas/', 0777, true);
        if (!is_dir('uploads/dokumen/')) mkdir('uploads/dokumen/', 0777, true);

        if ($fileKtp && $fileKtp->isValid() && !$fileKtp->hasMoved()) {
            $ktpName = $fileKtp->getRandomName();
            $fileKtp->move('uploads/identitas/', $ktpName);
        }
        if ($fileSim && $fileSim->isValid() && !$fileSim->hasMoved()) {
            $simName = $fileSim->getRandomName();
            $fileSim->move('uploads/dokumen/', $simName);
        }

        $this->pelangganModel->skipValidation(true)->update($id_pelanggan, [
            'nik' => $this->request->getPost('nik'),
            'no_telp' => $this->request->getPost('no_telp'),
            'no_sim' => $this->request->getPost('no_sim'),
            'alamat' => $this->request->getPost('alamat'),
            'foto_identitas' => $ktpName ?: $this->pelangganModel->find($id_pelanggan)->foto_identitas
        ]);

        $dataSewa = [
            'id_pelanggan' => $id_pelanggan,
            'id_mobil' => $id_mobil,
            'id_pegawai' => 1, 
            'tgl_sewa' => $tgl_sewa,
            'tgl_kembali_rencana' => $tgl_kembali,
            'total_hari' => $durasi,
            'sub_total' => $total,
            'status_pengajuan' => 'mengajukan',
            'status' => 'Berlangsung',
            'dokumen_ktp' => $ktpName, 
            'dokumen_sim' => $simName,
            'catatan_pelanggan' => $this->request->getPost('catatan_pelanggan'),
            'tgl_pengajuan' => date('Y-m-d H:i:s')
        ];

        if ($this->penyewaanModel->save($dataSewa)) {
            return redirect()->to('/riwayat')->with('success', 'Pengajuan sewa berhasil dikirim. Mohon tunggu konfirmasi admin.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal memproses pengajuan.');
    }

    public function riwayat()
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $filter = $this->request->getVar('status') ?: 'semua';
        $page = $this->request->getVar('page_riwayat') ?: 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $history = $this->pelangganModel->getHistoryByCustomer($id_pelanggan, $limit, $offset, $filter);

        $pager = service('pager');
        $pager_links = $pager->makeLinks($page, $limit, $history['total'], 'bootstrap_pagination', 0, 'riwayat');

        $data = [
            'title' => 'Riwayat Sewa Saya',
            'history' => $history['data'],
            'pager' => $pager_links,
            'filter' => $filter
        ];

        return view('pelanggan/riwayat', $data);
    }

    public function detail_sewa($id)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id, $id_pelanggan);

        if (!$sewa) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Data sewa tidak ditemukan.");
        }

        $wikiInfo = get_car_info($sewa['merk']);

        $data = [
            'title' => 'Detail Sewa #' . $id,
            'sewa' => $sewa,
            'wiki' => $wikiInfo
        ];

        return view('pelanggan/detail_sewa', $data);
    }

    public function cetak_invoice($id)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id, $id_pelanggan);

        if (!$sewa || $sewa['status_pengajuan'] !== 'disetujui') {
            return redirect()->back()->with('error', 'Invoice belum tersedia.');
        }

        $data = [
            'sewa' => $sewa
        ];

        return view('pelanggan/invoice_print', $data);
    }

    // ==========================================
    // PAYMENT PROCESS
    // ==========================================

    public function pembayaran($id_sewa)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id_sewa, $id_pelanggan);

        if (!$sewa || $sewa['status_pengajuan'] !== 'disetujui') {
            return redirect()->to('/riwayat')->with('error', 'Penyewaan tidak dapat dibayar saat ini.');
        }

        if ($sewa['status_bayar'] == 'lunas') {
            return redirect()->to('/riwayat/' . $id_sewa)->with('success', 'Penyewaan ini sudah lunas.');
        }

        $data = [
            'title' => 'Pembayaran Sewa #' . $id_sewa,
            'sewa' => $sewa
        ];

        return view('pelanggan/pembayaran', $data);
    }

    public function proses_pembayaran()
    {
        $id_sewa = $this->request->getPost('id_sewa');
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id_sewa, $id_pelanggan);

        if (!$sewa) return redirect()->to('/riwayat');

        $metode = $this->request->getPost('metode_bayar');
        $ewallet = $this->request->getPost('jenis_ewallet') ?: '';
        $bank = $this->request->getPost('jenis_bank') ?: '';

        $db = \Config\Database::connect();
        $db->table('t_pembayaran')->insert([
            'id_sewa' => $id_sewa,
            'tgl_bayar' => date('Y-m-d H:i:s'),
            'jumlah_bayar' => $sewa['sub_total'],
            'metode_bayar' => $metode,
            'status_bayar' => 'lunas',
            'jenis_ewallet' => $ewallet,
            'jenis_bank' => $bank
        ]);

        return redirect()->to('/riwayat/detail/' . $id_sewa)->with('success', 'Pembayaran berhasil dikonfirmasi. Terima kasih!');
    }
}
