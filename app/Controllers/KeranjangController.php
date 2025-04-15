<?php

namespace App\Controllers;

class KeranjangController extends BaseController
{
    // Helper yang dibutuhkan untuk form dan URL
    protected $helpers = ['form'];

    /**
     * Menampilkan isi keranjang belanja
     */
    public function index() // Nama 'index' umum digunakan, bisa juga 'tampilkan'
    {
        // Ambil data keranjang dari sesi menggunakan helper
        $keranjang = session()->get('keranjang');

        // Jika keranjang belum ada di sesi (masih null), inisialisasi sebagai array kosong
        if ($keranjang === null) {
            $keranjang = [];
        }

        // Siapkan data untuk dikirim ke view
        $data['itemKeranjang'] = $keranjang;
        $data['totalBelanja'] = $this->hitungTotal($keranjang); // Panggil fungsi hitung total

        // Muat view keranjang dan kirim datanya
        return view('keranjang_view', $data);
    }

    /**
     * Menambahkan item produk ke keranjang
     * Biasanya dipanggil dari halaman daftar produk atau detail produk
     */
    public function tambah()
    {
        // Ambil data produk dari request POST
        // Dalam aplikasi nyata, validasi lebih lanjut diperlukan
        $idProduk = $this->request->getPost('id_produk');
        $namaProduk = $this->request->getPost('nama_produk');
        $hargaProduk = $this->request->getPost('harga_produk');
        $jumlah = (int) $this->request->getPost('jumlah'); // Pastikan jumlah adalah integer

        // Validasi sederhana (bisa ditingkatkan)
        if (!$idProduk || !$namaProduk || !$hargaProduk || $jumlah <= 0) {
            // Set pesan error sementara (flash data)
            session()->setFlashdata('gagal', 'Data produk yang dikirim tidak lengkap atau tidak valid.');
            // Kembali ke halaman sebelumnya atau halaman produk
            return redirect()->back() ?? redirect()->to('/produk');
        }

        // Ambil keranjang yang sudah ada dari sesi
        $keranjang = session()->get('keranjang');
        if ($keranjang === null) {
            $keranjang = []; // Inisialisasi jika belum ada
        }

        // Gunakan ID produk sebagai kunci array untuk item di keranjang
        // Ini memastikan 1 produk hanya muncul 1 kali, jumlahnya yang bertambah
        $idItem = $idProduk;

        // Cek apakah produk (item) ini sudah ada di keranjang
        if (isset($keranjang[$idItem])) {
            // Jika sudah ada, tambahkan jumlahnya
            $keranjang[$idItem]['jumlah'] += $jumlah;
        } else {
            // Jika belum ada, tambahkan sebagai item baru
            $keranjang[$idItem] = [
                'id'        => $idProduk,
                'nama'      => $namaProduk,
                'harga'     => (float) $hargaProduk, // Pastikan harga adalah float/numeric
                'jumlah'    => $jumlah,
            ];
        }

        // Simpan kembali data keranjang yang sudah diperbarui ke dalam sesi
        session()->set('keranjang', $keranjang);

        // Set pesan sukses sementara (flash data)
        session()->setFlashdata('sukses', 'Produk berhasil ditambahkan ke keranjang!');

        // Arahkan pengguna kembali ke halaman keranjang
        return redirect()->to('/keranjang'); // Arahkan ke URL /keranjang
    }

    /**
     * Memperbarui jumlah item di keranjang
     */
    public function perbarui()
    {
        // Ambil id item dan jumlah baru dari POST request
        $idItem = $this->request->getPost('id_item');
        $jumlah = (int) $this->request->getPost('jumlah');

        // Ambil keranjang dari sesi
        $keranjang = session()->get('keranjang');

        // Pastikan keranjang ada dan item yang dimaksud ada di dalamnya
        if ($keranjang === null || !isset($keranjang[$idItem])) {
             session()->setFlashdata('gagal', 'Item tidak ditemukan di keranjang.');
            return redirect()->to('/keranjang');
        }

        // Jika jumlah baru lebih dari 0, perbarui jumlah item
        if ($jumlah > 0) {
            $keranjang[$idItem]['jumlah'] = $jumlah;
            session()->setFlashdata('sukses', 'Jumlah item berhasil diperbarui.');
        } else {
            // Jika jumlah 0 atau kurang, anggap pengguna ingin menghapus item
            unset($keranjang[$idItem]); // Hapus item dari array keranjang
             session()->setFlashdata('sukses', 'Item berhasil dihapus dari keranjang.');
        }

        // Simpan kembali keranjang yang sudah diperbarui ke sesi
        session()->set('keranjang', $keranjang);

        // Arahkan kembali ke halaman keranjang
        return redirect()->to('/keranjang');
    }

    /**
     * Menghapus satu jenis item dari keranjang berdasarkan ID-nya
     * @param string $idItem ID item (produk) yang akan dihapus
     */
    public function hapus($idItem)
    {
        // Ambil keranjang dari sesi
        $keranjang = session()->get('keranjang');

        // Cek apakah keranjang ada dan item yang dimaksud ada
        if ($keranjang !== null && isset($keranjang[$idItem])) {
            unset($keranjang[$idItem]); // Hapus item dari array
            session()->set('keranjang', $keranjang); // Simpan kembali keranjang ke sesi
            session()->setFlashdata('sukses', 'Item berhasil dihapus dari keranjang.');
        } else {
            session()->setFlashdata('gagal', 'Item tidak ditemukan di keranjang.');
        }

        // Arahkan kembali ke halaman keranjang
        return redirect()->to('/keranjang');
    }

    /**
     * Menghapus semua item dari keranjang (mengosongkan keranjang)
     */
    public function kosongkan()
    {
        // Hapus data dengan kunci 'keranjang' dari sesi
        session()->remove('keranjang');
        session()->setFlashdata('sukses', 'Keranjang belanja berhasil dikosongkan.');
        // Arahkan kembali ke halaman keranjang
        return redirect()->to('/keranjang');
    }

    /**
     * Fungsi privat untuk menghitung total harga semua item di keranjang
     * @param array $keranjang Data keranjang belanja
     * @return float Total harga
     */
    private function hitungTotal(array $keranjang): float
    {
        $total = 0.0;
        if (!empty($keranjang)) {
            foreach ($keranjang as $item) {
                // Pastikan harga dan jumlah ada dan merupakan angka
                if (isset($item['harga']) && is_numeric($item['harga']) && isset($item['jumlah']) && is_numeric($item['jumlah'])) {
                    $total += $item['harga'] * $item['jumlah'];
                }
            }
        }
        return $total;
    }

    // --- Fungsi Tambahan Untuk Demo ---
    /**
     * Halaman sederhana untuk menampilkan daftar produk dummy
     * agar bisa menambahkan item ke keranjang
     */
    public function produk() // Mengganti nama fungsi 'products'
    {
        // Data produk dummy (contoh)
        $data['daftarProduk'] = [
            ['id' => 'P001', 'nama' => 'Laptop Canggih', 'harga' => 15000000],
            ['id' => 'P002', 'nama' => 'Mouse Gaming', 'harga' => 350000],
            ['id' => 'P003', 'nama' => 'Keyboard Mekanik', 'harga' => 850000],
        ];
        // Muat view daftar produk
        return view('daftar_produk_view', $data);
    }
}