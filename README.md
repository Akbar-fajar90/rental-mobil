# 🚗 DriveEase - Sistem Manajemen Rental Mobil Modern

[![Framework](https://img.shields.io/badge/Framework-CodeIgniter%204-EF4223?style=for-the-badge&logo=codeigniter)](https://codeigniter.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![Database](https://img.shields.io/badge/Database-MySQL-4479A1?style=for-the-badge&logo=mysql)](https://mysql.com)

**DriveEase** adalah platform manajemen persewaan mobil berbasis web yang dirancang untuk menyederhanakan operasional bisnis rental. Dibangun dengan fokus pada **keamanan**, **kecepatan**, dan **pengalaman pengguna (UX)** yang premium.

---

## ✨ Fitur Utama

### 🛠️ Untuk Administrator (Dashboard Powerful)
- **Manajemen Armada Pro**: CRUD mobil dengan validasi teknis (plat nomor, tahun, tarif).
- **Sistem Approval**: Konfirmasi atau penolakan pengajuan sewa dengan validasi dokumen pelanggan.
- **Modul Pengembalian**: Perhitungan denda otomatis berdasarkan keterlambatan (hari) dan kondisi fisik kendaraan.
- **Reporting System**: Export laporan riwayat transaksi ke format **PDF, Excel, dan Word**.
- **Analitik Dashboard**: Statistik ketersediaan armada dan ringkasan keuangan secara real-time.

### 👤 Untuk Pelanggan (Booking Seamless)
- **Katalog Mobil Dinamis**: Cari dan pilih unit kendaraan berdasarkan spesifikasi.
- **Social Login**: Registrasi dan login instan menggunakan akun **Google atau Facebook**.
- **Payment Simulator**: Alur pembayaran digital yang interaktif (meniru gateway premium seperti Midtrans).
- **Riwayat Transaksi**: Pantau status pengajuan dan cetak invoice penyewaan secara mandiri.

---

## 🛡️ Keamanan & Arsitektur
Aplikasi ini dibangun dengan standar keamanan tinggi:
- **CSRF Protection**: Melindungi form dari serangan pemalsuan request.
- **XSS Filtering**: Menghindari eksekusi skrip berbahaya pada input user.
- **Secure File Upload**: Validasi mime-type dan integritas gambar pada setiap upload dokumen.
- **MVC Pattern**: Arsitektur kode yang rapi dan mudah dipelihara (Maintainable).

---

## 🚀 Cara Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/Akbar-fajar90/rental-mobil.git
   ```

2. **Install Dependensi**
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**
   - Rename file `env` menjadi `.env`
   - Sesuaikan konfigurasi database:
     ```env
     database.default.hostname = localhost
     database.default.database = rental_mobil
     database.default.username = root
     database.default.password = 
     ```

4. **Jalankan Aplikasi**
   ```bash
   php spark serve
   ```

---

## 🛠️ Stack Teknologi
- **Backend**: PHP 8.2 (CodeIgniter 4)
- **Frontend**: Bootstrap 5, Vanilla CSS (Premium Customization)
- **Iconography**: Bootstrap Icons
- **PDF Engine**: Dompdf
- **Excel Engine**: PhpSpreadsheet

---

## 🤝 Kontribusi
Kami menerima kontribusi untuk pengembangan fitur lebih lanjut. Silakan buat *pull request* atau laporkan *issue* jika menemukan kendala.

---

## 📄 Lisensi
Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  Dibuat dengan ❤️ untuk solusi transportasi yang lebih baik.
</p>
