<?php
require_once 'koneksi.php';
require_once 'session_check.php';
ensure_session_started();

if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
} else {
    die("Error: No transaction ID provided.");
}

// Fetch transaction details
$query = $koneksi->prepare("SELECT t.*, p.pelanggan_nama, p.pelanggan_alamat, p.pelanggan_tlp 
                           FROM tb_transaksi t
                           LEFT JOIN tb_pelanggan p ON t.pelanggan_id = p.pelanggan_id
                           WHERE t.id_transaksi = ?");

if ($query === false) {
    die("Error in query preparation: " . $koneksi->error);
}

$query->bind_param("s", $id_transaksi);
if (!$query->execute()) {
    die("Error executing query: " . $query->error);
}

$result = $query->get_result();

if ($result->num_rows == 0) {
    die("Transaksi tidak ditemukan.");
}

$transaksi = $result->fetch_assoc();

// Fetch transaction items
$items_query = $koneksi->prepare("SELECT dt.*, b.barang_nama, b.barang_jual 
                                 FROM tb_detail_transaksi dt
                                 JOIN tb_barang b ON dt.barang_id = b.barang_id
                                 WHERE dt.id_transaksi = ? AND dt.is_deleted = 0");

if ($items_query === false) {
    die("Error in items query preparation: " . $koneksi->error);
}

$items_query->bind_param("s", $id_transaksi);
if (!$items_query->execute()) {
    die("Error executing items query: " . $items_query->error);
}

$items_result = $items_query->get_result();

$items = [];
while ($item = $items_result->fetch_assoc()) {
    $items[] = $item;
}

// Calculate totals
$subtotal = array_sum(array_column($items, 'detail_total'));
$discount = $transaksi['discount'];
$total = $transaksi['total_transaksi'];
$payment = $transaksi['payment'];
$change = $payment - $total;

// Update transaction status to 'selesai' if it's not already
if ($transaksi['status'] !== 'selesai') {
    $update_status = $koneksi->prepare("UPDATE tb_transaksi SET status = 'selesai' WHERE id_transaksi = ?");
    if ($update_status === false) {
        die("Error in update status query preparation: " . $koneksi->error);
    }
    $update_status->bind_param("s", $id_transaksi);
    if (!$update_status->execute()) {
        die("Error updating transaction status: " . $update_status->error);
    }
}

// Clear the cart items
$clear_cart = $koneksi->prepare("UPDATE tb_detail_transaksi SET is_deleted = 1 WHERE id_transaksi = ?");
if ($clear_cart === false) {
    die("Error in clear cart query preparation: " . $koneksi->error);
}
$clear_cart->bind_param("s", $id_transaksi);
if (!$clear_cart->execute()) {
    die("Error clearing cart: " . $clear_cart->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Belanja</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 300px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .receipt-info {
            margin-bottom: 10px;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
        }
        .item-table td {
            padding: 3px 0;
        }
        .total-table {
            width: 100%;
            margin-top: 10px;
            border-top: 1px dashed #000;
        }
        .total-table td {
            padding: 3px 0;
        }
        .right {
            text-align: right;
        }
        .center {
            text-align: center;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">TOKO KELAPA ADE</h2>
        <p style="margin: 5px 0;">Jl. Contoh No. 123, Kota Contoh</p>
        <p style="margin: 5px 0;">Telp: (021) 1234567</p>
    </div>

    <div class="divider"></div>

    <div class="receipt-info">
        <p style="margin: 3px 0;">No: <?php echo $transaksi['id_transaksi']; ?></p>
        <p style="margin: 3px 0;">Tgl: <?php echo date('d/m/Y H:i', strtotime($transaksi['tgl_transaksi'])); ?></p>
        <p style="margin: 3px 0;">Kasir: Admin</p>
        <p style="margin: 3px 0;">Pelanggan: <?php echo $transaksi['pelanggan_nama'] ?? 'Umum'; ?></p>
    </div>

    <div class="divider"></div>

    <table class="item-table">
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td colspan="4"><?php echo $item['barang_nama']; ?></td>
            </tr>
            <tr>
                <td><?php echo $item['detail_jumlah']; ?> x</td>
                <td><?php echo rupiah($item['barang_jual']); ?></td>
                <td class="right"><?php echo rupiah($item['detail_total']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="total-table">
        <tr>
            <td>Subtotal</td>
            <td class="right"><?php echo rupiah($subtotal); ?></td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="right"><?php echo rupiah($discount); ?></td>
        </tr>
        <tr>
            <td><strong>TOTAL</strong></td>
            <td class="right"><strong><?php echo rupiah($total); ?></strong></td>
        </tr>
        <tr>
            <td>Tunai</td>
            <td class="right"><?php echo rupiah($payment); ?></td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="right"><?php echo rupiah($change); ?></td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="footer">
        <p style="margin: 5px 0;">Terima kasih atas kunjungan Anda!</p>
        <p style="margin: 5px 0;">Barang yang sudah dibeli</p>
        <p style="margin: 5px 0;">tidak dapat ditukar atau dikembalikan.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

