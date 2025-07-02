<?php
$koneksi = new mysqli("localhost", "root", "", "grosir");
if ($koneksi->connect_errno) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data pelanggan & produk untuk dropdown
$pelanggan = $koneksi->query("SELECT id_pelanggan, nama_pelanggan FROM pelanggan");
$produk = $koneksi->query("SELECT id_produk, nama_produk FROM produk");

// Jika form disubmit
if (isset($_POST['simpan'])) {
    $id_pelanggan = $_POST['id_pelanggan'];
    $tanggal = date('Y-m-d H:i:s');
    $jenis_pembayaran = $_POST['jenis_pembayaran'];
    $produk_terpilih = $_POST['produk'];  // array produk
    $qty = $_POST['qty'];                // array qty

    // Hitung total pembayaran
    $total = 0;
    foreach ($produk_terpilih as $index => $id_produk) {
        $result = $koneksi->query("SELECT harga_satuan FROM produk WHERE id_produk = $id_produk");
        $data = $result->fetch_assoc();
        $subtotal = $qty[$index] * $data['harga_satuan'];
        $total += $subtotal;
    }

    // Simpan transaksi ke tabel transaksi
    $koneksi->query("INSERT INTO transaksi (id_pelanggan, tanggal, total, jenis_pembayaran) VALUES ('$id_pelanggan', '$tanggal', $total, '$jenis_pembayaran')");
    $id_transaksi = $koneksi->insert_id;

    // Simpan detail transaksi
    foreach ($produk_terpilih as $index => $id_produk) {
        $result = $koneksi->query("SELECT harga_satuan FROM produk WHERE id_produk = $id_produk");
        $data = $result->fetch_assoc();
        $subtotal = $qty[$index] * $data['harga_satuan'];
        $koneksi->query("INSERT INTO detail_transaksi (id_transaksi, id_produk, qty, subtotal) VALUES ($id_transaksi, $id_produk, {$qty[$index]}, $subtotal)");
    }

    echo "<script>alert('Transaksi berhasil disimpan!'); window.location='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Input Transaksi Baru</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 20px auto; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 6px; }
        .produk-group { border: 1px solid #ccc; padding: 10px; margin-top: 10px; }
        .add-btn { margin-top: 10px; }
    </style>
    <script>
        function tambahProduk() {
            var container = document.getElementById("produk-container");
            var group = container.querySelector(".produk-group").cloneNode(true);
            container.appendChild(group);
        }
    </script>
</head>
<body>
    <h1>Input Transaksi Baru</h1>
    <form method="post">
        <label>Pelanggan</label>
        <select name="id_pelanggan" required>
            <option value="">- Pilih Pelanggan -</option>
            <?php while ($p = $pelanggan->fetch_assoc()) { ?>
                <option value="<?= $p['id_pelanggan'] ?>"><?= htmlspecialchars($p['nama_pelanggan']) ?></option>
            <?php } ?>
        </select>

        <label>Jenis Pembayaran</label>
        <select name="jenis_pembayaran" required>
            <option value="">- Pilih -</option>
            <option value="Tunai">Tunai</option>
            <option value="Transfer">Transfer</option>
        </select>

        <h3>Produk Dibeli</h3>
        <div id="produk-container">
            <div class="produk-group">
                <label>Produk</label>
                <select name="produk[]" required>
                    <option value="">- Pilih Produk -</option>
                    <?php
                    // reset data pointer produk karena sudah dibaca di atas
                    $produk->data_seek(0);
                    while ($pr = $produk->fetch_assoc()) { ?>
                        <option value="<?= $pr['id_produk'] ?>"><?= htmlspecialchars($pr['nama_produk']) ?></option>
                    <?php } ?>
                </select>
                <label>Qty</label>
                <input type="number" name="qty[]" min="1" required>
            </div>
        </div>

        <button type="button" class="add-btn" onclick="tambahProduk()">+ Tambah Produk</button>

        <br><br>
        <button type="submit" name="simpan">Simpan Transaksi</button>
    </form>
</body>
</html>
<?php $koneksi->close(); ?>
