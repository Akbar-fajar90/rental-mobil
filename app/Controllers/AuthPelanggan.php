<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PelangganModel;
use Google\Client as GoogleClient;
use League\OAuth2\Client\Provider\Facebook;

class AuthPelanggan extends BaseController
{
    protected $pelangganModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->pelangganModel = new PelangganModel();
        helper(['form', 'url', 'cookie', 'asset']);
    }

    public function login()
    {
        if (session()->get('isLoggedInPelanggan')) {
            return redirect()->to('/');
        }
        
        $data = [
            'title' => 'Login Pelanggan - Rental Mobil'
        ];
        return view('pelanggan/login', $data);
    }

    public function doLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Cari user dengan email yang memiliki provider 'email' (manual)
        $user = $this->pelangganModel->where('email', $email)
                                     ->where('provider', 'email')
                                     ->first();

        if ($user && password_verify((string)$password, $user->password)) {
            $this->setUserSession($user);
            return redirect()->to('/');
        }

        return redirect()->back()->with('error', 'Email atau password salah.');
    }

    public function register()
    {
        $data = [
            'title' => 'Daftar Akun - Rental Mobil'
        ];
        return view('pelanggan/register', $data);
    }

    public function doRegister()
    {
        $rules = [
            'nama' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[t_pelanggan.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash((string)$this->request->getPost('password'), PASSWORD_DEFAULT),
            'provider' => 'email'
        ];

        if ($this->pelangganModel->save($data)) {
            return redirect()->to('/login')->with('success', 'Pendaftaran berhasil. Silakan login.');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal mendaftar. Silakan coba lagi.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    // ========== GOOGLE LOGIN ==========
    public function google()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/google/callback'));
        $client->addScope("email");
        $client->addScope("profile");

        return redirect()->to($client->createAuthUrl());
    }

    public function googleCallback()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(base_url('auth/google/callback'));

        if ($this->request->getVar('code')) {
            try {
                $token = $client->fetchAccessTokenWithAuthCode($this->request->getVar('code'));
                if(isset($token['error'])) {
                    throw new \Exception($token['error_description'] ?? $token['error']);
                }
                $client->setAccessToken($token);
                $googleService = new \Google\Service\Oauth2($client);
                $data = $googleService->userinfo->get();

                $user = $this->socialLoginProcess([
                    'email' => $data->email,
                    'nama' => $data->name,
                    'provider' => 'google',
                    'provider_id' => $data->id,
                    'avatar' => $data->picture
                ]);

                $this->setUserSession($user);
                return redirect()->to('/');
            } catch (\Exception $e) {
                return redirect()->to('/login')->with('error', 'Google Auth Error: ' . $e->getMessage());
            }
        }

        return redirect()->to('/login')->with('error', 'Login Google dibatalkan.');
    }

    // ========== FACEBOOK LOGIN ==========
    public function facebook()
    {
        $provider = new Facebook([
            'clientId'          => env('FACEBOOK_APP_ID'),
            'clientSecret'      => env('FACEBOOK_APP_SECRET'),
            'redirectUri'       => base_url('auth/facebook/callback'),
            'graphApiVersion'   => 'v12.0',
        ]);

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['email'],
        ]);
        
        session()->set('oauth2state', $provider->getState());
        return redirect()->to($authUrl);
    }

    public function facebookCallback()
    {
        $provider = new Facebook([
            'clientId'          => env('FACEBOOK_APP_ID'),
            'clientSecret'      => env('FACEBOOK_APP_SECRET'),
            'redirectUri'       => base_url('auth/facebook/callback'),
            'graphApiVersion'   => 'v12.0',
        ]);

        $state = $this->request->getVar('state');
        if (!$state || $state !== session()->get('oauth2state')) {
            session()->remove('oauth2state');
            return redirect()->to('/login')->with('error', 'Invalid OAuth state.');
        }

        if (!$this->request->getVar('code')) {
            return redirect()->to('/login')->with('error', 'Login Facebook dibatalkan.');
        }

        try {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $this->request->getVar('code')
            ]);
            $user = $provider->getResourceOwner($token);
            $data = $user->toArray();

            $loggedUser = $this->socialLoginProcess([
                'email' => $data['email'] ?? $data['id'] . '@facebook.com',
                'nama' => $data['name'],
                'provider' => 'facebook',
                'provider_id' => $data['id'],
                'avatar' => $user->getPictureUrl()
            ]);

            $this->setUserSession($loggedUser);
            return redirect()->to('/');
        } catch (\Exception $e) {
            return redirect()->to('/login')->with('error', 'Login Facebook gagal: ' . $e->getMessage());
        }
    }

    // ========== PRIVATE METHODS ==========
    private function socialLoginProcess($data)
    {
        // Cari user berdasarkan provider dan provider_id (akun sosial yang sudah ada)
        $user = $this->pelangganModel->where('provider', $data['provider'])
                                     ->where('provider_id', $data['provider_id'])
                                     ->first();

        if (!$user) {
            // Jika belum ada akun sosial ini, cek apakah email sudah terdaftar dengan provider lain (misal: email manual)
            $existingEmail = $this->pelangganModel->where('email', $data['email'])->first();
            
            if ($existingEmail) {
                // TAUTKAN AKUN: Update data provider di akun yang sudah ada
                $this->pelangganModel->update($existingEmail->id_pelanggan, [
                    'provider' => $data['provider'],
                    'provider_id' => $data['provider_id'],
                    'avatar' => $data['avatar'],
                    'last_login' => date('Y-m-d H:i:s')
                ]);
                $user = $this->pelangganModel->find($existingEmail->id_pelanggan);
            } else {
                // BUAT AKUN BARU
                $insertData = [
                    'nama' => $data['nama'],
                    'email' => $data['email'],
                    'provider' => $data['provider'],
                    'provider_id' => $data['provider_id'],
                    'avatar' => $data['avatar'],
                    'last_login' => date('Y-m-d H:i:s')
                ];
                
                $id = $this->pelangganModel->insert($insertData);
                $user = $this->pelangganModel->find($id);
            }
        } else {
            // Akun sudah ada, update avatar dan last login
            $this->pelangganModel->update($user->id_pelanggan, [
                'avatar' => $data['avatar'],
                'last_login' => date('Y-m-d H:i:s')
            ]);
        }

        return $user;
    }

    private function setUserSession($user)
    {
        $data = [
            'id_pelanggan' => $user->id_pelanggan,
            'nama_pelanggan' => $user->nama,
            'email_pelanggan' => $user->email,
            'avatar_pelanggan' => $user->avatar,
            'isLoggedInPelanggan' => true,
        ];

        session()->set($data);
        return true;
    }
}
