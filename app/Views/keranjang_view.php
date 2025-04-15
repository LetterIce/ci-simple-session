<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: middle; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 1.2em; text-align: right; }
        .actions a, .actions button { margin-right: 5px; padding: 5px 10px; text-decoration: none; cursor: pointer; }
        .update-form input[type=number] { width: 60px; padding: 4px; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .sukses { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .gagal { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .navigasi-link { display: inline-block; margin-bottom: 20px; padding: 8px 12px; background-color: #e7e7e7; color: black; text-decoration: none; border-radius: 4px;}
        .navigasi-link:hover { background-color: #ddd; }
        .aksi-keranjang { margin-top: 20px; text-align: right; }
        .aksi-keranjang a, .aksi-keranjang button { margin-left: 10px; }
        .checkout-btn { background-color: #4CAF50; color: white; border: none;}
        .checkout-btn:hover { background-color: #45a049; }
    </style>
</head>
<body>

<h1>Keranjang Belanja Anda</h1>

<a href="<?= site_url('/produk') ?>" class="navigasi-link">Lihat Produk Lain</a>

<?php // Ambil instance sesi untuk cek flashdata
$sesi = session(); ?>
<?php if ($pesanSukses = $sesi->getFlashdata('sukses')): ?>
    <div class="message sukses">
        <?= esc($pesanSukses) ?>
    </div>
<?php endif; ?>
<?php if ($pesanGagal = $sesi->getFlashdata('gagal')): ?>
    <div class="message gagal">
        <?= esc($pesanGagal) ?>
    </div>
<?php endif; ?>

<?php if (empty($itemKeranjang)): ?>
    <p>Keranjang belanja Anda masih kosong.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga Satuan</th>
                <th>Jumlah</th>
                <th>Subtotal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($itemKeranjang as $idItem => $item): ?>
                <tr>
                    <td><?= esc($item['nama']) ?></td>
                    <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td>
                        <!-- Form untuk update jumlah -->
                        <?= form_open(site_url('/keranjang/perbarui'), ['class' => 'update-form']) ?>
                            <input type="hidden" name="id_item" value="<?= esc($idItem) ?>">
                            <input type="number" name="jumlah" value="<?= esc($item['jumlah']) ?>" min="0" title="Ubah jumlah (0 untuk hapus)">
                            <button type="submit">Perbarui</button>
                        <?= form_close() ?>
                    </td>
                    <td>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                    <td class="actions">
                        <a href="<?= site_url('/keranjang/hapus/' . esc($idItem)) ?>" onclick="return confirm('Yakin ingin menghapus item \'<?= esc($item['nama']) ?>\' dari keranjang?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
         <tfoot>
            <tr>
                <td colspan="4" class="total">Total Belanja:</td>
                <td>Rp <?= number_format($totalBelanja, 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="aksi-keranjang">
        <a href="<?= site_url('/keranjang/kosongkan') ?>" onclick="return confirm('Yakin ingin mengosongkan seluruh keranjang belanja?')" class="actions">Kosongkan Keranjang</a>
        <a href="#" class="actions checkout-btn">Checkout</a> <!-- Arahkan ke proses checkout -->
    </div>

<?php endif; ?>

</body>
</html>