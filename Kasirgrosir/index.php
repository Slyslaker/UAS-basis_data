<?php
// Tampilkan error agar mudah debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$koneksi = new mysqli("localhost", "root", "", "grosir");
if ($koneksi->connect_errno) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil daftar transaksi
$sql = "SELECT t.id_transaksi, t.tanggal, p.nama_pelanggan, t.total, t.jenis_pembayaran
        FROM transaksi t
        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        ORDER BY t.tanggal DESC";
$result = $koneksi->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi - Kasir Grosir</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; margin: 0; }
        header { background: #007bff; color: white; padding: 15px 20px; text-align: center; }
        main { max-width: 1000px; margin: 20px auto; background: white; padding: 20px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .btn { display: inline-block; background: #28a745; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; }
        .btn:hover { background: #218838; }
    </style>
</head>
<body>
    <header>
        <h1>Kasir Grosir - PT Sumber Cipta Multi Niaga</h1>
    </header>
    <main>
        <h2>Daftar Transaksi</h2>
        <a href="tambah_transaksi.php" class="btn">+ Tambah Transaksi</a>
        <table>
            <tr>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Nama Pelanggan</th>
                <th>Total Pembayaran</th>
                <th>Jenis Pembayaran</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_transaksi']) ?></td>
                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                    <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                    <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['jenis_pembayaran']) ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center;">Tidak ada transaksi.</td></tr>
            <?php endif; ?>
        </table>
    </main>
</body>
</html>
<?php $koneksi->close(); ?>
