<?php
include('../koneksi.php');

$period = isset($_GET['period']) ? $_GET['period'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';

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
            return "TAHUNAN (" . date('Y', strtotime($start_date)) . ")";
        default:
            return "";
    }
}

$periodText = getPeriodText($period, $start_date);

$where = "";
$params = [];
$types = "";

if ($period != 'all' && $start_date) {
    switch ($period) {
        case 'daily':
            $where = "WHERE DATE(tb_barang_msk.msk_tgl) = ?";
            $params[] = $start_date;
            $types .= "s";
            break;
        case 'monthly':
            $where = "WHERE DATE_FORMAT(tb_barang_msk.msk_tgl, '%Y-%m') = ?";
            $params[] = date('Y-m', strtotime($start_date));
            $types .= "s";
            break;
        case 'yearly':
            $where = "WHERE YEAR(tb_barang_msk.msk_tgl) = ?";
            $params[] = date('Y', strtotime($start_date));
            $types .= "s";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Supply - TOKO KELAPA ADE</title>
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
            .report-table {
                page-break-inside: auto;
            }
            .report-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            .report-table thead {
                display: table-header-group;
            }
            .report-table tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body>
    <div class="report-header">
        <h1 class="company-name">TOKO KELAPA ADE</h1>
        <p class="company-address">Jl. Prof. M. Yamin, Permanti, Kec. Pd. Tinggi, Kota Sungai Penuh, Jambi 37111</p>
        <h2 class="report-title">LAPORAN SUPPLY BARANG <?php echo $periodText; ?></h2>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Supplier</th>
                <th>Faktur Supply</th>
                <th>Kategori</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Sisa Stok</th>
                <th>Harga Beli</th>
                <th>Total</th>
                <th>Tanggal Masuk</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT 
                        tb_barang_msk.*,
                        tb_supplier.supplier_nama,
                        tb_barang.barang_nama,
                        tb_kategori.kategori_nama,
                        tb_stok_batch.jumlah_tersisa
                    FROM tb_barang_msk 
                    JOIN tb_supplier ON tb_barang_msk.supplier_id = tb_supplier.supplier_id 
                    JOIN tb_barang ON tb_barang_msk.barang_id = tb_barang.barang_id 
                    JOIN tb_kategori ON tb_barang.kategori_id = tb_kategori.kategori_id
                    LEFT JOIN tb_stok_batch ON tb_barang_msk.msk_id = tb_stok_batch.msk_id
                    $where
                    ORDER BY tb_barang_msk.msk_tgl DESC, tb_barang_msk.msk_id DESC";

            $stmt = $koneksi->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            $total = 0;
            $no = 1;

            while ($pecah = $result->fetch_object()) {
                $subtotal = $pecah->msk_jumlah * $pecah->msk_harga_beli;
                $total += $subtotal;
            ?>
                <tr>
                    <td><?php echo $no++ ?></td>
                    <td><?php echo htmlspecialchars($pecah->supplier_nama) ?></td>
                    <td><?php echo htmlspecialchars($pecah->msk_faktur) ?></td>
                    <td><?php echo htmlspecialchars($pecah->kategori_nama) ?></td>
                    <td><?php echo htmlspecialchars($pecah->barang_nama) ?></td>
                    <td class="text-right"><?php echo $pecah->msk_jumlah ?></td>
                    <td class="text-right"><?php echo $pecah->jumlah_tersisa ?></td>
                    <td class="text-right"><?php echo rupiah($pecah->msk_harga_beli) ?></td>
                    <td class="text-right"><?php echo rupiah($subtotal) ?></td>
                    <td><?php echo tgl_indo($pecah->msk_tgl) ?></td>
                    <td><?php echo htmlspecialchars($pecah->msk_ket) ?></td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right"><strong>Total Supply:</strong></td>
                <td class="text-right"><strong><?php echo rupiah($total); ?></strong></td>
                <td colspan="2"></td>
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

