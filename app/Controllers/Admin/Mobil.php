<?php namespace App\Controllers\Admin;
 
use App\Controllers\BaseController;
use App\Models\MobilModel;
 
class Mobil extends BaseController
{
    /** @var MobilModel */
    protected $mobilModel;

    public function __construct()
    {
        $this->mobilModel = new MobilModel();
        helper(['form', 'url', 'asset']);
    }

    public function index()
    {
        // Ambil semua data mobil menggunakan model
        $mobil_list = $this->mobilModel->orderBy('id_mobil', 'DESC')->findAll();
        
        // Hitung statistik
        $total_armada = $this->mobilModel->countAll();
        $total_tersedia = $this->mobilModel->where('status', 'tersedia')->countAllResults();
        $total_disewa = $this->mobilModel->where('status', 'disewa')->countAllResults();
        
        $data = [
            'title' => 'Daftar Mobil',
            'page_title' => 'Manajemen Mobil',
            'active_menu' => 'mobil',
            'mobil_list' => $mobil_list,
            'total_armada' => $total_armada,
            'total_tersedia' => $total_tersedia,
            'total_disewa' => $total_disewa
        ];
        
        return view('admin/mobil', $data);
    }
    
    public function getMobil(int|string $id)
    {
        $mobil = $this->mobilModel->find($id);
        if ($mobil) {
            return $this->response->setJSON($mobil);
        }
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
    }
    
    public function simpan()
    {
        // Validasi Input
        $rules = [
            'plat_nomor'     => 'required|is_unique[t_mobil.plat_nomor]|regex_match[/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/]',
            'merk'           => 'required|min_length[3]',
            'tahun'          => 'required|numeric|exact_length[4]',
            'tarif_per_hari' => 'required|numeric|greater_than[0]',
            'denda_per_hari' => 'required|numeric|greater_than_equal_to[0]',
            'status'         => 'required|in_list[tersedia,disewa,perbaikan]',
            'foto_mobil'     => 'uploaded[foto_mobil]|max_size[foto_mobil,2048]|is_image[foto_mobil]|mime_in[foto_mobil,image/jpg,image/jpeg,image/png]'
        ];

        $messages = [
            'plat_nomor' => [
                'regex_match' => 'Format Plat Nomor salah (Contoh: B 1234 ABC)',
                'is_unique'   => 'Plat Nomor sudah terdaftar'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $foto = $this->request->getFile('foto_mobil');
        $nama_foto = '';
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $nama_foto = $foto->getRandomName();
            $foto->move(FCPATH . 'assets/img', $nama_foto);
        }
        
        $data = [
            'plat_nomor'     => strtoupper($this->request->getPost('plat_nomor')),
            'merk'           => $this->request->getPost('merk'),
            'tahun'          => $this->request->getPost('tahun'),
            'foto_mobil'     => $nama_foto,
            'tarif_per_hari' => $this->request->getPost('tarif_per_hari'),
            'denda_per_hari' => $this->request->getPost('denda_per_hari'),
            'status'         => $this->request->getPost('status'),
            'device_id'      => $this->request->getPost('device_id')
        ];

        if (!$this->mobilModel->insert($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data ke database');
        }
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil ditambahkan');
    }
    
    public function update(int|string $id)
    {
        $id = (int)$id;
        
        // Validasi Input
        $rules = [
            'plat_nomor'     => "required|regex_match[/^[A-Z]{1,2}\s\d{1,4}\s[A-Z]{1,3}$/]|is_unique[t_mobil.plat_nomor,id_mobil,{$id}]",
            'merk'           => 'required|min_length[3]',
            'tahun'          => 'required|numeric|exact_length[4]',
            'tarif_per_hari' => 'required|numeric|greater_than[0]',
            'denda_per_hari' => 'required|numeric|greater_than_equal_to[0]',
            'status'         => 'required|in_list[tersedia,disewa,perbaikan]'
        ];

        // Jika ada upload foto baru, tambahkan rule
        $foto = $this->request->getFile('foto_mobil');
        if ($foto && $foto->isValid()) {
            $rules['foto_mobil'] = 'max_size[foto_mobil,2048]|is_image[foto_mobil]|mime_in[foto_mobil,image/jpg,image/jpeg,image/png]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama_foto = $this->request->getPost('foto_mobil_lama');
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Hapus foto lama
            if ($nama_foto && file_exists(FCPATH . 'assets/img/' . $nama_foto)) {
                unlink(FCPATH . 'assets/img/' . $nama_foto);
            }
            $nama_foto = $foto->getRandomName();
            $foto->move(FCPATH . 'assets/img', $nama_foto);
        }
        
        $data = [
            'id_mobil'       => $id,
            'plat_nomor'     => strtoupper($this->request->getPost('plat_nomor')),
            'merk'           => $this->request->getPost('merk'),
            'tahun'          => $this->request->getPost('tahun'),
            'foto_mobil'     => $nama_foto,
            'tarif_per_hari' => $this->request->getPost('tarif_per_hari'),
            'denda_per_hari' => $this->request->getPost('denda_per_hari'),
            'status'         => $this->request->getPost('status'),
            'device_id'      => $this->request->getPost('device_id')
        ];

        if (!$this->mobilModel->save($data)) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate data di database');
        }
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil diupdate');
    }
    
    public function hapus(int|string $id)
    {
        $id = (int)$id;
        $mobil = $this->mobilModel->find($id);
        if ($mobil) {
            // Cek apakah mobil sedang disewa
            if ($mobil->status === 'disewa') {
                return redirect()->to(base_url('admin/mobil'))->with('error', 'Tidak bisa menghapus mobil yang sedang disewa!');
            }

            $nama_foto = $mobil->foto_mobil;
            if ($nama_foto && file_exists(FCPATH . 'assets/img/' . $nama_foto)) {
                unlink(FCPATH . 'assets/img/' . $nama_foto);
            }
            $this->mobilModel->delete($id);
            return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil dihapus');
        }
        
        return redirect()->to(base_url('admin/mobil'))->with('error', 'Data tidak ditemukan');
    }
}