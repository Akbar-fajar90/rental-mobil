<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MobilModel;
use App\Models\PelangganModel;
use App\Models\PenyewaanModel;
use App\Models\PesanModel;

class Page extends BaseController
{
    protected $mobilModel;
    protected $pelangganModel;
    protected $penyewaanModel;
    protected $pesanModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->mobilModel = new MobilModel();
        $this->pelangganModel = new PelangganModel();
        $this->penyewaanModel = new PenyewaanModel();
        $this->pesanModel = new PesanModel();
        helper(['form', 'url', 'asset']);
    }

    public function about()
    {
        $data = [
            'title' => 'Tentang Kami - Rental Mobil',
            'stats' => [
                'total_mobil' => $this->mobilModel->countAll(),
                'total_pelanggan' => $this->pelangganModel->countAll(),
                'tahun_pengalaman' => date('Y') - 2015, // Asumsi berdiri sejak 2015
                'total_sewa' => $this->penyewaanModel->countAll()
            ]
        ];
        return view('pelanggan/about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'Kontak Kami - Rental Mobil'
        ];
        return view('pelanggan/contact', $data);
    }

    public function send_message()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/contact');
        }

        $rules = [
            'nama' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'pesan' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $save = $this->pesanModel->save([
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'no_telp' => $this->request->getPost('no_telp'),
            'pesan' => $this->request->getPost('pesan'),
            'status' => 'belum_dibaca'
        ]);

        if ($save) {
            return redirect()->back()->with('success', 'Pesan Anda telah berhasil dikirim. Kami akan segera menghubungi Anda.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengirim pesan. Silakan coba lagi.');
        }
    }
}
