<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Produk</title>
     <style>
        body { font-family: sans-serif; margin: 20px; }
        .produk { border: 1px solid #eee; padding: 15px; margin-bottom: 15px; border-radius: 5px; background-color: #f9f9f9; }
        .produk h2 { margin-top: 0; color: #333; }
        .produk p { margin: 5px 0; }
        .navigasi-link { display: inline-block; margin-bottom: 20px; padding: 8px 12px; background-color: #e7e7e7; color: black; text-decoration: none; border-radius: 4px;}
        .navigasi-link:hover { background-color: #ddd; }
        .form-tambah label { margin-right: 5px;}
        .form-tambah input[type=number] { width: 50px; padding: 4px; margin-right: 5px;}
        .form-tambah button { padding: 5px 10px; cursor: pointer; background-color: #5cb85c; color: white; border: none; border-radius: 3px;}
        .form-tambah button:hover { background-color: #4cae4c;}
     </style>
</head>
<body>

<h1>Daftar Produk</h1>

<?php
    // Hitung jumlah item unik di keranjang
    $keranjang = session()->get('keranjang') ?? [];
    $jumlahItemDiKeranjang = count($keranjang);
?>
<a href="<?= site_url('/keranjang') ?>" class="navigasi-link">
    Lihat Keranjang (<?= $jumlahItemDiKeranjang ?> item)
</a>

<?php if (empty($daftarProduk)): ?>
    <p>Tidak ada produk untuk ditampilkan saat ini.</p>
<?php else: ?>
    <?php foreach ($daftarProduk as $produk): ?>
        <div class="produk">
            <h2><?= esc($produk['nama']) ?></h2>
            <p>Harga: Rp <?= number_format($produk['harga'], 0, ',', '.') ?></p>

            <!-- Form untuk menambah ke keranjang -->
            <?= form_open(site_url('/keranjang/tambah'), ['class' => 'form-tambah']) ?>
                <input type="hidden" name="id_produk" value="<?= esc($produk['id']) ?>">
                <input type="hidden" name="nama_produk" value="<?= esc($produk['nama']) ?>">
                <input type="hidden" name="harga_produk" value="<?= esc($produk['harga']) ?>">
                <label for="jumlah_<?= esc($produk['id']) ?>">Jumlah:</label>
                <input type="number" id="jumlah_<?= esc($produk['id']) ?>" name="jumlah" value="1" min="1">
                <button type="submit">Tambah ke Keranjang</button>
            <?= form_close() ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>