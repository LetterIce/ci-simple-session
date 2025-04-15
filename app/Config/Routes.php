<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
/// app/Config/Routes.php

// ... (kode routes lainnya)

// Rute untuk Keranjang Belanja (Bahasa Indonesia)
// Pastikan mengarah ke KeranjangController
$routes->get('/keranjang', 'KeranjangController::index');          // Menampilkan keranjang
$routes->post('/keranjang/tambah', 'KeranjangController::tambah');     // Menambah item (via POST)
$routes->post('/keranjang/perbarui', 'KeranjangController::perbarui'); // Memperbarui item (via POST)
$routes->get('/keranjang/hapus/(:segment)', 'KeranjangController::hapus/$1'); // Menghapus item (:segment menangkap ID)
$routes->get('/keranjang/kosongkan', 'KeranjangController::kosongkan'); // Mengosongkan keranjang

// Rute untuk halaman produk demo (Bahasa Indonesia)
// Pastikan mengarah ke KeranjangController::produk
$routes->get('/produk', 'KeranjangController::produk');

// Jika ingin halaman produk jadi halaman utama:
// $routes->get('/', 'KeranjangController::produk');


// ... (kode routes lainnya)