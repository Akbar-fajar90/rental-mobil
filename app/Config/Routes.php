<?php
namespace Config;

$routes = Services::routes();

// Route default
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->get('test', 'Test::index');

// ========== ROUTE PELANGGAN LANDING ==========
$routes->get('/', 'Pelanggan::index');
$routes->get('/pelanggan/dashboard', 'Pelanggan::index');
$routes->get('/mobil', 'Pelanggan::mobil');
$routes->get('/detail/(:num)', 'Pelanggan::detail/$1');
$routes->get('/about', 'Page::about');
$routes->get('/tentang', 'Page::about');
$routes->get('/contact', 'Page::contact');
$routes->get('/kontak', 'Page::contact');
$routes->post('/contact/send', 'Page::send_message');

// ========== ROUTE RENTAL PELANGGAN (LOGGED IN) ==========
$routes->group('', ['filter' => 'auth_pelanggan'], function($routes) {
    $routes->get('sewa/(:num)', 'Pelanggan::sewa_form/$1');
    $routes->post('sewa/proses', 'Pelanggan::proses_sewa');
    $routes->get('riwayat', 'Pelanggan::riwayat');
    $routes->get('riwayat/detail/(:num)', 'Pelanggan::detail_sewa/$1');
    $routes->get('riwayat/invoice/(:num)', 'Pelanggan::cetak_invoice/$1');
    
    // Payment routes (Simulation)
    $routes->get('payment/checkout/(:num)', 'PaymentController::checkout/$1');
    $routes->get('payment/simulate/(:num)', 'PaymentController::simulate/$1');
    $routes->post('payment/process-simulation', 'PaymentController::process_simulation');
    $routes->post('payment/manual', 'PaymentController::processManual');
    $routes->get('payment/finish', 'PaymentController::finish');
});

// ========== ROUTE AUTH PELANGGAN ==========
$routes->get('/login', 'AuthPelanggan::login');
$routes->post('/auth/doLogin', 'AuthPelanggan::doLogin');
$routes->get('/register', 'AuthPelanggan::register');
$routes->post('/auth/doRegister', 'AuthPelanggan::doRegister');
$routes->get('/logout', 'AuthPelanggan::logout');

// Social Login
$routes->get('/auth/google', 'AuthPelanggan::google');
$routes->get('/auth/google/callback', 'AuthPelanggan::googleCallback');
$routes->get('/auth/facebook', 'AuthPelanggan::facebook');
$routes->get('/auth/facebook/callback', 'AuthPelanggan::facebookCallback');

// ========== ROUTE LOGIN ADMIN ==========
$routes->get('/admin/login', 'Admin\Auth::login');
$routes->post('/admin/doLogin', 'Admin\Auth::doLogin');
$routes->get('/admin/forgot-password', 'Admin\ForgotPassword::index');
$routes->post('/admin/reset-password', 'Admin\ForgotPassword::reset');
$routes->get('/admin/logout', 'Admin\Auth::logout');

// ========== DASHBOARD ADMIN (Butuh Login) ==========
$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('dashboard', 'Admin\Dashboard::index');
    
    //========ROUTE MOBIL(CRUD)
    $routes->get('mobil', 'Admin\Mobil::index');
    $routes->post('mobil/simpan', 'Admin\Mobil::simpan');
    $routes->post('mobil/update/(:num)', 'Admin\Mobil::update/$1');
    $routes->get('mobil/hapus/(:num)', 'Admin\Mobil::hapus/$1');
    $routes->get('mobil/getMobil/(:num)', 'Admin\Mobil::getMobil/$1');  // ← Untuk AJAX
      // ========== ROUTE PELANGGAN (CRUD) ==========
    $routes->get('pelanggan', 'Admin\Pelanggan::index');
    $routes->post('pelanggan/save', 'Admin\Pelanggan::save');
    $routes->get('pelanggan/edit/(:num)', 'Admin\Pelanggan::edit/$1');
    $routes->post('pelanggan/update/(:num)', 'Admin\Pelanggan::update/$1');
    $routes->get('pelanggan/delete/(:num)', 'Admin\Pelanggan::delete/$1');
    $routes->get('pelanggan/getData/(:num)', 'Admin\Pelanggan::getData/$1');
    
    // ROUTE UNTUK AJAX EDIT FORM (WAJIB ADA!)
    $routes->get('pelanggan/editForm/(:num)', 'Admin\Pelanggan::editForm/$1');


    // ========== ROUTE PENYEWAAN ==========
    $routes->get('penyewaan', 'Admin\Penyewaan::index');
    $routes->post('penyewaan/approve/(:num)', 'Admin\Penyewaan::approve/$1');
    $routes->post('penyewaan/reject/(:num)', 'Admin\Penyewaan::reject/$1');
    $routes->get('penyewaan/detail/(:num)', 'Admin\Penyewaan::detail/$1');
    $routes->get('penyewaan/print/(:num)', 'Admin\Penyewaan::print/$1');
    $routes->get('penyewaan/validate-documents/(:num)', 'Admin\Penyewaan::validateDocuments/$1');
    $routes->get('penyewaan/check-availability/(:num)/(:any)/(:any)', 'Admin\Penyewaan::checkAvailability/$1/$2/$3');

    // ========== ROUTE PENGEMBALIAN ==========
$routes->get('pengembalian', 'Admin\Pengembalian::index');
$routes->get('pengembalian/search', 'Admin\Pengembalian::searchRental');
$routes->post('pengembalian/process', 'Admin\Pengembalian::process');
$routes->get('pengembalian/getRentalDetail/(:num)', 'Admin\Pengembalian::getRentalDetail/$1');

// ========== ROUTE EXPORT ==========
$routes->get('pengembalian/export/excel', 'Admin\Pengembalian::exportExcel');
$routes->get('pengembalian/export/word', 'Admin\Pengembalian::exportWord');
$routes->get('pengembalian/export/pdf', 'Admin\Pengembalian::exportPdf');

// ========== ROUTE PEMBAYARAN ==========
$routes->get('pembayaran', 'Admin\Pembayaran::index');
$routes->post('pembayaran/process', 'Admin\Pembayaran::process');
$routes->get('pembayaran/invoice/(:num)', 'Admin\Pembayaran::invoice/$1');
$routes->get('pembayaran/getSisaBayar/(:num)', 'Admin\Pembayaran::getSisaBayar/$1');

// ========== ROUTE LAPORAN ==========
$routes->get('laporan', 'Admin\Laporan::index');
$routes->get('laporan/export/excel', 'Admin\Laporan::exportExcel');
$routes->get('laporan/export/pdf', 'Admin\Laporan::exportPdf');

// ========== ROUTE ADMIN (PEGAWAI) ==========
  $routes->get('admin', 'Admin\AdminController::index');
    $routes->post('admin/save', 'Admin\AdminController::save');
    $routes->post('admin/update/(:num)', 'Admin\AdminController::update/$1');
    $routes->get('admin/delete/(:num)', 'Admin\AdminController::delete/$1');
    $routes->get('admin/getAdmin/(:num)', 'Admin\AdminController::getAdmin/$1');
    $routes->post('admin/updateSettings', 'Admin\AdminController::updateSettings');

});
