<?php
include('../koneksi.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to log messages
function logMessage($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../error.log');
}

$period = isset($_GET['period']) ? $_GET['period'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$show_batch_id = isset($_GET['show_batch_id']) ? $_GET['show_batch_id'] : 'no';

logMessage("Period: $period, Start Date: $start_date");

$where = "WHERE tb_stok_batch.jumlah_tersisa > 0";
$params = [];
$types = "";

if ($period != 'all' && $start_date) {
    switch ($period) {
        case 'daily':
            $where .= " AND DATE(tb_stok_batch.tanggal_masuk) = ?";
            $params[] = $start_date;
            $types .= "s";
            break;
        case 'monthly':
            $where .= " AND DATE_FORMAT(tb_stok_batch.tanggal_masuk, '%Y-%m') = ?";
            $params[] = date('Y-m', strtotime($start_date));
            $types .= "s";
            break;
        case 'yearly':
            $where .= " AND YEAR(tb_stok_batch.tanggal_masuk) = ?";
            $params[] = $start_date; // Use the year directly from $start_date
            $types .= "s";
            break;
    }
}

logMessage("WHERE clause: $where");
logMessage("Params: " . print_r($params, true));

function getPeriodText($period, $start_date) {
    if (empty($start_date)) {
        return "";
    }
    
    switch($period) {
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
logMessage("Period Text: $periodText");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Stok Barang - TOKO KELAPA ADE</title>
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
        <h2 class="report-title">
            LAPORAN STOK BARANG <?php echo $periodText; ?>
        </h2>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>No</th>
                <?php if ($show_batch_id == 'yes'): ?>
                <th>Batch ID</th>
                <?php endif; ?>
                <th>Faktur Supply</th>
                <th>Kategori</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok Batch</th>
                <th>Tanggal Masuk</th>
                <th>Total Stok</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT 
                        tb_barang.*,
                        tb_kategori.kategori_nama,
                        tb_stok_batch.batch_id,
                        tb_stok_batch.jumlah_tersisa,
                        tb_stok_batch.harga_beli,
                        tb_stok_batch.tanggal_masuk,
                        tb_barang_msk.msk_faktur
                    FROM tb_barang 
                    LEFT JOIN tb_kategori ON tb_barang.kategori_id = tb_kategori.kategori_id
                    LEFT JOIN tb_stok_batch ON tb_barang.barang_id = tb_stok_batch.barang_id
                    LEFT JOIN tb_barang_msk ON tb_stok_batch.msk_id = tb_barang_msk.msk_id
                    $where
                    ORDER BY tb_barang.barang_nama ASC, tb_stok_batch.tanggal_masuk ASC";

            logMessage("SQL Query: $query");

            $stmt = $koneksi->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            logMessage("Number of rows returned: " . $result->num_rows);

            $no = 1;
            $current_barang = '';
            $total_stok = 0;
            $grand_total_stok = 0;

            while ($pecah = $result->fetch_object()) {
                $new_barang = ($current_barang !== $pecah->barang_nama);
                if ($new_barang && $current_barang !== '') {
                    // Print total row for previous group
                    echo "<tr class='summary-row'>";
                    echo "<td colspan='" . ($show_batch_id == 'yes' ? '10' : '9') . "' class='text-right'><strong>Total Stok {$current_barang}</strong></td>";
                    echo "<td class='text-right'><strong>{$total_stok}</strong></td>";
                    echo "</tr>";
                    $total_stok = 0;
                }
                
                $current_barang = $pecah->barang_nama;
                $total_stok += $pecah->jumlah_tersisa;
                $grand_total_stok += $pecah->jumlah_tersisa;
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <?php if ($show_batch_id == 'yes'): ?>
                    <td><?php echo $pecah->batch_id ?></td>
                    <?php endif; ?>
                    <td><?php echo htmlspecialchars($pecah->msk_faktur) ?></td>
                    <td><?php echo htmlspecialchars($pecah->kategori_nama) ?></td>
                    <td><?php echo htmlspecialchars($pecah->barang_kode ?: 'Belum diatur') ?></td>
                    <td><?php echo htmlspecialchars($pecah->barang_nama) ?></td>
                    <td class="text-right"><?php echo rupiah($pecah->harga_beli) ?></td>
                    <td class="text-right"><?php echo $pecah->barang_jual ? rupiah($pecah->barang_jual) : 'Belum diatur' ?></td>
                    <td class="text-right"><?php echo $pecah->jumlah_tersisa ?></td>
                    <td><?php echo tgl_indo($pecah->tanggal_masuk) ?></td>
                    <td></td>
                </tr>
            <?php 
            }
            // Print total for last group
            if ($current_barang !== '') {
                echo "<tr class='summary-row'>";
                echo "<td colspan='" . ($show_batch_id == 'yes' ? '10' : '9') . "' class='text-right'><strong>Total Stok {$current_barang}</strong></td>";
                echo "<td class='text-right'><strong>{$total_stok}</strong></td>";
                echo "</tr>";
                
                // Print grand total
                echo "<tr class='total-row'>";
                echo "<td colspan='" . ($show_batch_id == 'yes' ? '10' : '9') . "' class='text-right'><strong>Total Keseluruhan Stok</strong></td>";
                echo "<td class='text-right'><strong>{$grand_total_stok}</strong></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
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

