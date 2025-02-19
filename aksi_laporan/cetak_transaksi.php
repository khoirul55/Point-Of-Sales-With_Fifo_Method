<?php
include('../koneksi.php');

$period = isset($_GET['period']) ? $_GET['period'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';

function getPeriodText($period, $start_date) {
    switch ($period) {
        case 'daily':
            return "HARIAN (" . date('d F Y', strtotime($start_date)) . ")";
        case 'monthly':
            return "BULANAN (" . date('F Y', strtotime($start_date)) . ")";
        case 'yearly':
            return "TAHUNAN (" . $start_date . ")";
        default:
            return "";
    }
}

$periodText = getPeriodText($period, $start_date);

$where = "WHERE tb_transaksi.status = 'selesai' AND tb_detail_transaksi.is_deleted = 0";
if ($period != 'all' && $start_date) {
    switch ($period) {
        case 'daily':
            $where .= " AND DATE(tb_transaksi.tgl_transaksi) = '$start_date'";
            break;
        case 'monthly':
            $where .= " AND DATE_FORMAT(tb_transaksi.tgl_transaksi, '%Y-%m') = '" . date('Y-m', strtotime($start_date)) . "'";
            break;
        case 'yearly':
            $where .= " AND YEAR(tb_transaksi.tgl_transaksi) = '$start_date'";
            break;
    }
}

$query = "SELECT 
            tb_transaksi.*,
            tb_detail_transaksi.*,
            tb_barang.*,
            COALESCE(tb_pelanggan.pelanggan_nama, 'Umum') as pelanggan_nama,
            COALESCE(tb_pelanggan.pelanggan_tlp, '-') as pelanggan_tlp
          FROM tb_transaksi 
          JOIN tb_detail_transaksi ON tb_transaksi.id_transaksi = tb_detail_transaksi.id_transaksi 
          JOIN tb_barang ON tb_detail_transaksi.barang_id = tb_barang.barang_id 
          LEFT JOIN tb_pelanggan ON tb_transaksi.pelanggan_id = tb_pelanggan.pelanggan_id
          $where
          ORDER BY tb_transaksi.id_transaksi ASC, tb_transaksi.tgl_transaksi ASC";

$result = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - TOKO KELAPA ADE</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .report-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
        }
        .company-address {
            font-size: 12px;
            color: #666;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 10px;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .summary-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .total-row {
            background-color: #dee2e6;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 20px;
            text-align: right;
        }
        @media print {
            body {
                width: 210mm;
                height: 297mm;
            }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1 class="company-name">TOKO KELAPA ADE</h1>
        <p class="company-address">Jl. Prof. M. Yamin, Permanti, Kec. Pd. Tinggi, Kota Sungai Penuh, Jambi 37111</p>
        <h2 class="report-title">LAPORAN TRANSAKSI PENJUALAN <?php echo $periodText; ?></h2>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>No</th>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Nama Konsumen</th>
                <th>No. Tlp</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            $no = 1;
            $current_transaksi = '';
            $subtotal = 0;

            while ($pecah = $result->fetch_object()) {
                $total += $pecah->detail_total;
                
                if ($current_transaksi != $pecah->id_transaksi) {
                    if ($current_transaksi != '') {
                        echo "<tr class='summary-row'>
                            <td colspan='7' class='text-right'><strong>Subtotal:</strong></td>
                            <td class='text-right'><strong>" . rupiah($subtotal) . "</strong></td>
                        </tr>";
                        $subtotal = 0;
                    }
                    $current_transaksi = $pecah->id_transaksi;
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo htmlspecialchars($pecah->id_transaksi) ?></td>
                    <td><?php echo tgl_indo($pecah->tgl_transaksi) ?></td>
                    <td><?php echo htmlspecialchars($pecah->pelanggan_nama) ?></td>
                    <td><?php echo htmlspecialchars($pecah->pelanggan_tlp) ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php
                }
                $subtotal += $pecah->detail_total;
            ?>
                <tr>
                    <td colspan="5"></td>
                    <td><?php echo htmlspecialchars($pecah->barang_nama) ?></td>
                    <td class="text-right"><?php echo $pecah->detail_jumlah ?></td>
                    <td class="text-right"><?php echo rupiah($pecah->detail_total) ?></td>
                </tr>
            <?php 
            }
            if ($current_transaksi != '') {
                echo "<tr class='summary-row'>
                    <td colspan='7' class='text-right'><strong>Subtotal:</strong></td>
                    <td class='text-right'><strong>" . rupiah($subtotal) . "</strong></td>
                </tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right"><strong>Total Penjualan:</strong></td>
                <td class="text-right"><strong><?php echo rupiah($total) ?></strong></td>
            </tr>
            <tr class="total-row">
                <td colspan="7" class="text-right"><strong>Jumlah Transaksi:</strong></td>
                <td class="text-right"><strong><?php echo $no - 1; ?></strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature-section">
        <p>Sungai Penuh, <?php echo tgl_indo(date('Y-m-d')); ?></p>
        <br><br><br>
        <p><strong>ADE</strong></p>
        <p>Pemilik Toko</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

