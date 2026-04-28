<?php namespace App\Controllers\Admin;
 
use App\Controllers\BaseController;
use App\Models\MobilModel;
 
class Mobil extends BaseController
{
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
    
    public function getMobil($id)
    {
        $mobil = $this->mobilModel->find($id);
        if ($mobil) {
            return $this->response->setJSON($mobil);
        }
        return $this->response->setJSON(['error' => 'Data tidak ditemukan'], 404);
    }
    
    public function simpan()
    {
        // Validasi Upload File
        $foto = $this->request->getFile('foto_mobil');
        $nama_foto = '';

        $rules = [
            'foto_mobil' => 'uploaded[foto_mobil]|max_size[foto_mobil,2048]|is_image[foto_mobil]|mime_in[foto_mobil,image/jpg,image/jpeg,image/png]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError('foto_mobil'));
        }
        
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $nama_foto = $foto->getRandomName();
            $foto->move(FCPATH . 'assets/img', $nama_foto);
        }
        
        $data = [
            'plat_nomor' => $this->request->getPost('plat_nomor'),
            'merk' => $this->request->getPost('merk'),
            'tahun' => $this->request->getPost('tahun'),
            'foto_mobil' => $nama_foto,
            'tarif_per_hari' => $this->request->getPost('tarif_per_hari'),
            'denda_per_hari' => $this->request->getPost('denda_per_hari'),
            'status' => $this->request->getPost('status'),
            'device_id' => $this->request->getPost('device_id')
        ];

        if (!$this->mobilModel->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $this->mobilModel->errors());
        }
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil ditambahkan');
    }
    
    public function update($id)
    {
        $foto = $this->request->getFile('foto_mobil');
        $nama_foto = $this->request->getPost('foto_mobil_lama');
        
        // Jika ada upload foto baru, validasi
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $rules = [
                'foto_mobil' => 'max_size[foto_mobil,2048]|is_image[foto_mobil]|mime_in[foto_mobil,image/jpg,image/jpeg,image/png]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', $this->validator->getError('foto_mobil'));
            }

            // Hapus foto lama
            if ($nama_foto && file_exists(FCPATH . 'assets/img/' . $nama_foto)) {
                unlink(FCPATH . 'assets/img/' . $nama_foto);
            }
            $nama_foto = $foto->getRandomName();
            $foto->move(FCPATH . 'assets/img', $nama_foto);
        }
        
        $data = [
            'id_mobil' => $id,
            'plat_nomor' => $this->request->getPost('plat_nomor'),
            'merk' => $this->request->getPost('merk'),
            'tahun' => $this->request->getPost('tahun'),
            'foto_mobil' => $nama_foto,
            'tarif_per_hari' => $this->request->getPost('tarif_per_hari'),
            'denda_per_hari' => $this->request->getPost('denda_per_hari'),
            'status' => $this->request->getPost('status'),
            'device_id' => $this->request->getPost('device_id')
        ];

        if (!$this->mobilModel->save($data)) {
            return redirect()->back()->withInput()->with('errors', $this->mobilModel->errors());
        }
        
        return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil diupdate');
    }
    
    public function hapus($id)
    {
        $mobil = $this->mobilModel->find($id);
        if ($mobil) {
            $nama_foto = is_array($mobil) ? $mobil['foto_mobil'] : $mobil->foto_mobil;
            if ($nama_foto && file_exists(FCPATH . 'assets/img/' . $nama_foto)) {
                unlink(FCPATH . 'assets/img/' . $nama_foto);
            }
            $this->mobilModel->delete($id);
            return redirect()->to(base_url('admin/mobil'))->with('success', 'Mobil berhasil dihapus');
        }
        
        return redirect()->to(base_url('admin/mobil'))->with('error', 'Data tidak ditemukan');
    }
}