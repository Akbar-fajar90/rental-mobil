<?php

namespace App\Controllers;

use App\Models\PelangganModel;
use CodeIgniter\Controller;

class LupaPassword extends Controller
{
    /** @var PelangganModel */
    protected $pelangganModel;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        helper(['url', 'form']);
    }

    public function index()
    {
        return view('pelanggan/lupa_password', ['title' => 'Lupa Password']);
    }

    public function send_reset_link()
    {
        $email = $this->request->getPost('email');
        
        $pelanggan = $this->pelangganModel->asArray()->where('email', $email)->first();
        
        if (!$pelanggan) {
            return redirect()->back()->with('error', 'Email tidak ditemukan dalam sistem kami.');
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->pelangganModel->skipValidation(true)->update($pelanggan['id_pelanggan'], [
            'reset_token' => $token,
            'reset_expires' => $expires
        ]);

        $resetLink = base_url('lupa_password/reset/' . $token);

        $emailConfig = \Config\Services::email();
        $emailConfig->setTo($email);
        $emailConfig->setSubject('Reset Password Rental Mobil');
        $emailConfig->setMessage("Halo {$pelanggan['nama']},<br><br>Klik link berikut untuk mereset password Anda: <br><a href='{$resetLink}'>{$resetLink}</a><br><br>Link ini hanya berlaku selama 1 jam.");

        if ($emailConfig->send()) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }
    }

    public function reset(string $token)
    {
        $pelanggan = $this->pelangganModel->asArray()->where('reset_token', $token)
                                          ->where('reset_expires >=', date('Y-m-d H:i:s'))
                                          ->first();
        
        if (!$pelanggan) {
            return redirect()->to('/login')->with('error', 'Token reset password tidak valid atau sudah kadaluarsa.');
        }

        return view('pelanggan/reset_password', [
            'title' => 'Reset Password',
            'token' => $token
        ]);
    }

    public function update_password()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $confirm = $this->request->getPost('confirm_password');

        if ($password !== $confirm) {
            return redirect()->back()->with('error', 'Password dan Konfirmasi Password tidak cocok.');
        }

        $pelanggan = $this->pelangganModel->asArray()->where('reset_token', $token)
                                          ->where('reset_expires >=', date('Y-m-d H:i:s'))
                                          ->first();
        
        if (!$pelanggan) {
            return redirect()->to('/login')->with('error', 'Token tidak valid.');
        }

        $this->pelangganModel->skipValidation(true)->update($pelanggan['id_pelanggan'], [
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'reset_token' => null,
            'reset_expires' => null
        ]);

        return redirect()->to('/login')->with('success', 'Password berhasil diubah. Silakan login dengan password baru.');
    }
}
