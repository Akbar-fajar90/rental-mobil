<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PenyewaanModel;
use App\Models\PelangganModel;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentController extends BaseController
{
    protected $penyewaanModel;
    protected $pelangganModel;

    public function __construct()
    {
        $this->penyewaanModel = new PenyewaanModel();
        $this->pelangganModel = new PelangganModel();
        helper(['url', 'form', 'midtrans', 'asset']);
    }

    public function checkout($id_sewa)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id_sewa, $id_pelanggan);

        if (!$sewa || $sewa['status_pengajuan'] !== 'disetujui') {
            return redirect()->to('/riwayat')->with('error', 'Penyewaan tidak valid untuk pembayaran.');
        }

        if ($sewa['status_bayar'] == 'lunas') {
            return redirect()->to('/riwayat/detail/' . $id_sewa)->with('success', 'Penyewaan sudah lunas.');
        }

        $data = [
            'title' => 'Checkout Pembayaran #' . $id_sewa,
            'sewa' => $sewa,
            'client_key' => env('midtrans.client_key'),
            'is_production' => env('midtrans.is_production')
        ];

        return view('pelanggan/payment/checkout', $data);
    }

    public function processPayment($id_sewa)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id_sewa, $id_pelanggan);

        if (!$sewa) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan']);
        }

        $order_id = generateOrderId();
        
        // Simpan order_id ke database
        $db = \Config\Database::connect();
        $db->table('t_pembayaran')->insert([
            'id_sewa' => $id_sewa,
            'midtrans_order_id' => $order_id,
            'jumlah_bayar' => $sewa['sub_total'],
            'status_bayar' => 'pending',
            'tgl_bayar' => date('Y-m-d H:i:s')
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int)$sewa['sub_total'],
            ],
            'customer_details' => [
                'first_name' => $sewa['nama'],
                'email' => $sewa['email'],
                'phone' => $sewa['no_telp'],
            ],
            'item_details' => [
                [
                    'id' => $sewa['id_mobil'],
                    'price' => (int)$sewa['tarif_per_hari'],
                    'quantity' => (int)$sewa['total_hari'],
                    'name' => 'Sewa Mobil ' . $sewa['merk'] . ' (' . $sewa['plat_nomor'] . ')'
                ]
            ]
        ];

        $snapToken = createMidtransTransaction($params);

        return $this->response->setJSON([
            'status' => 'success',
            'snap_token' => $snapToken,
            'order_id' => $order_id
        ]);
    }

    public function notificationHandler()
    {
        initMidtrans();
        $notif = new Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $db = \Config\Database::connect();
        $payment = $db->table('t_pembayaran')->where('midtrans_order_id', $order_id)->get()->getRow();

        if (!$payment) return "Payment not found";

        $status_bayar = 'pending';

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $status_bayar = 'pending';
                } else {
                    $status_bayar = 'lunas';
                }
            }
        } else if ($transaction == 'settlement') {
            $status_bayar = 'lunas';
        } else if ($transaction == 'pending') {
            $status_bayar = 'pending';
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $status_bayar = 'gagal';
        }

        $db->table('t_pembayaran')->where('midtrans_order_id', $order_id)->update([
            'status_bayar' => $status_bayar,
            'payment_type' => $type,
            'transaction_time' => $notif->transaction_time,
            'fraud_status' => $fraud,
            'payment_method_detail' => $notif->payment_type
        ]);

        return "OK";
    }

    public function processManual()
    {
        $id_sewa = $this->request->getPost('id_sewa');
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id_sewa, $id_pelanggan);

        if (!$sewa) return $this->response->setStatusCode(404);

        $metode = $this->request->getPost('metode_bayar');

        $db = \Config\Database::connect();
        $db->table('t_pembayaran')->insert([
            'id_sewa' => $id_sewa,
            'tgl_bayar' => date('Y-m-d H:i:s'),
            'jumlah_bayar' => $sewa['sub_total'],
            'metode_bayar' => $metode,
            'status_bayar' => 'pending', 
            'jenis_ewallet' => '',
            'jenis_bank' => ''
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function simulate($id_sewa)
    {
        $id_pelanggan = session()->get('id_pelanggan');
        $sewa = $this->pelangganModel->getSewaDetail($id_sewa, $id_pelanggan);

        if (!$sewa) return redirect()->to('/riwayat');

        $data = [
            'title' => 'Simulasi Pembayaran Gateway',
            'sewa' => $sewa
        ];

        return view('pelanggan/payment/simulation', $data);
    }

    public function process_simulation()
    {
        $id_sewa = $this->request->getPost('id_sewa');
        $metode = $this->request->getPost('metode');

        $db = \Config\Database::connect();
        
        // Simpan data pembayaran
        $db->table('t_pembayaran')->insert([
            'id_sewa' => $id_sewa,
            'tgl_bayar' => date('Y-m-d H:i:s'),
            'jumlah_bayar' => $this->request->getPost('total'),
            'metode_bayar' => $metode,
            'status_bayar' => 'lunas',
            'midtrans_order_id' => 'SIM-' . time(),
            'payment_type' => $metode,
            'transaction_time' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function finish()
    {
        return redirect()->to('/riwayat')->with('success', 'Pembayaran berhasil diproses. Silakan cek detail riwayat.');
    }

    public function unfinish()
    {
        return redirect()->to('/riwayat')->with('error', 'Pembayaran belum selesai. Segera selesaikan pembayaran Anda.');
    }

    public function error()
    {
        return redirect()->to('/riwayat')->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
    }
}
