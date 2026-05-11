<?php

namespace App\Controllers;

use App\Models\PenyewaanModel;
use App\Models\PembayaranModel;
use App\Models\PelangganModel;
use CodeIgniter\Controller;
use Exception;

class PaymentController extends Controller
{
    protected $penyewaanModel;
    protected $pembayaranModel;
    protected $pelangganModel;

    public function __construct()
    {
        $this->penyewaanModel = new PenyewaanModel();
        $this->pembayaranModel = new PembayaranModel();
        $this->pelangganModel = new PelangganModel();
        helper(['url', 'form', 'midtrans']);
    }

    /**
     * Prepare payment and get Snap Token
     */
    public function bayarSewa($id_sewa)
    {
        $id_sewa = (int)$id_sewa;
        $sewa = $this->penyewaanModel->select('t_sewa.*, t_pelanggan.nama as customer_name, t_pelanggan.email, t_pelanggan.no_telp, t_mobil.merk as car_name')
                    ->join('t_pelanggan', 't_pelanggan.id_pelanggan = t_sewa.id_pelanggan')
                    ->join('t_mobil', 't_mobil.id_mobil = t_sewa.id_mobil')
                    ->find($id_sewa);

        if (!$sewa) {
            return redirect()->back()->with('error', 'Data penyewaan tidak ditemukan');
        }

        // Cek jika sudah lunas
        $totalPaid = $this->pembayaranModel->getTotalPaidBySewa($id_sewa);
        $amountToPay = $sewa->sub_total - $totalPaid;

        if ($amountToPay <= 0) {
            return redirect()->back()->with('success', 'Penyewaan ini sudah lunas');
        }

        // Data Transaksi
        $order_id = 'RENTAL-' . $id_sewa . '-' . time();
        $customer_details = [
            'first_name' => $sewa->customer_name,
            'email'      => $sewa->email,
            'phone'      => $sewa->no_telp,
        ];

        $item_details = [
            [
                'id'       => $sewa->id_mobil,
                'price'    => (int)$amountToPay,
                'quantity' => 1,
                'name'     => 'Rental Mobil: ' . $sewa->car_name,
            ]
        ];

        try {
            $snapToken = createMidtransTransaction($order_id, $amountToPay, $customer_details, $item_details);
            
            $data = [
                'title'     => 'Pembayaran Sewa',
                'sewa'      => $sewa,
                'snapToken' => $snapToken,
                'amount'    => $amountToPay,
                'order_id'  => $order_id
            ];

            return view('pelanggan/payment_midtrans', $data);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Midtrans Notification Handler (Webhook)
     */
    public function notificationHandler()
    {
        initMidtrans();
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        // Parse ID Sewa dari Order ID (format: RENTAL-ID-TIMESTAMP)
        $parts = explode('-', $order_id);
        $id_sewa = isset($parts[1]) ? (int)$parts[1] : 0;

        if ($id_sewa === 0) return $this->response->setStatusCode(400);

        $status_bayar = 'belum';
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
            $status_bayar = 'batal';
        }

        // Cek apakah sudah ada catatan pembayaran untuk order_id ini
        $existingPayment = $this->pembayaranModel->where('midtrans_order_id', $order_id)->first();

        $paymentData = [
            'id_sewa'            => $id_sewa,
            'tgl_bayar'         => date('Y-m-d H:i:s'),
            'jumlah_bayar'      => $notif->gross_amount,
            'metode_bayar'      => ($type == 'bank_transfer') ? 'transfer' : 'ewallet',
            'status_bayar'      => $status_bayar,
            'midtrans_order_id' => $order_id,
            'transaction_status' => $transaction,
            'payment_type'      => $type,
            'fraud_status'      => $fraud,
        ];

        if ($existingPayment) {
            $this->pembayaranModel->update($existingPayment->id_pembayaran, $paymentData);
        } else {
            $this->pembayaranModel->insert($paymentData);
        }

        return $this->response->setJSON(['status' => 'OK']);
    }

    public function finish()
    {
        return view('pelanggan/payment_finish', ['status' => 'success']);
    }

    public function unfinish()
    {
        return view('pelanggan/payment_finish', ['status' => 'pending']);
    }

    public function error()
    {
        return view('pelanggan/payment_finish', ['status' => 'error']);
    }
}
