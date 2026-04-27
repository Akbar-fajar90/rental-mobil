<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insert data pegawai admin
        $this->db->table('t_pegawai')->insert([
            'nama'      => 'Admin Utama',
            'jabatan'   => 'Administrator',
            'no_telp'   => '081234567890',
            'username'  => 'admin',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT),
            'status'    => 'aktif'
        ]);
        
        echo "Seeder berhasil dijalankan!\n";
        echo "Username admin: admin\n";
        echo "Password admin: admin123\n";
    }
}