<style>
        body {
            font-size: 14px;
        }
        .content-wrapper {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
        .table {
            font-size: 12px;
        }
        .table th, .table td {
            padding: 0.5rem;
        }
        .table-sm th, .table-sm td {
            padding: 0.3rem;
        }
        .table-fixed {
            table-layout: fixed;
        }
        .table-fixed th, .table-fixed td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,.075);
        }
        .text-small {
            font-size: 85%;
        }
        .pagination {
            justify-content: center;
        }
        .table th, .table td {
        padding: 0.5rem;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    </style>
<?php $items_per_page = 20; // Adjust this value as needed
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                  
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Laporan Transaksi Penjualan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            $period = isset($_POST['period']) ? $_POST['period'] : 'all';
                            $date_picker = isset($_POST['date_picker']) ? $_POST['date_picker'] : '';

                            function getPeriodText($period, $date_picker) {
                                switch ($period) {
                                    case 'daily':
                                        return "HARIAN (" . date('d F Y', strtotime($date_picker)) . ")";
                                    case 'monthly':
                                        return "BULANAN (" . date('F Y', strtotime($date_picker)) . ")";
                                    case 'yearly':
                                        return "TAHUNAN (" . date('Y', strtotime($date_picker)) . ")";
                                    default:
                                        return "";
                                }
                            }

                            $periodText = getPeriodText($period, $date_picker);
                            ?>
                            <h4 class="mb-3">LAPORAN TRANSAKSI PENJUALAN <?php echo $periodText; ?></h4>
                            <form method="post" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="period">Periode</label>
                                            <select name="period" id="period" class="form-control">
                                                <option value="all" <?php echo $period == 'all' ? 'selected' : ''; ?>>Semua Data</option>
                                                <option value="daily" <?php echo $period == 'daily' ? 'selected' : ''; ?>>Harian</option>
                                                <option value="monthly" <?php echo $period == 'monthly' ? 'selected' : ''; ?>>Bulanan</option>
                                                <option value="yearly" <?php echo $period == 'yearly' ? 'selected' : ''; ?>>Tahunan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="date_picker">Pilih Tanggal</label>
                                            <input type="date" name="date_picker" id="date_picker" class="form-control" value="<?php echo $date_picker; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" name="filter" class="btn btn-primary form-control">Filter</button>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <?php
                                            $print_url = "aksi_laporan/cetak_transaksi.php?period=" . $period . "&start_date=" . $date_picker;
                                            echo "<a href='$print_url' target='_blank' class='btn btn-success form-control'><i class='fas fa-print'></i> Cetak Laporan</a>";
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php
                            $where = "WHERE tb_transaksi.status = 'selesai' AND tb_detail_transaksi.is_deleted = 0";
                            $params = [];
                            $types = "";

                            if (isset($_POST['filter']) && $_POST['period'] != 'all') {
                                $period = $_POST['period'];
                                $date_picked = $_POST['date_picker'];
                                
                                switch ($period) {
                                    case 'daily':
                                        $where .= " AND DATE(tb_transaksi.tgl_transaksi) = ?";
                                        $params[] = $date_picked;
                                        $types .= "s";
                                        break;
                                    case 'monthly':
                                        $where .= " AND DATE_FORMAT(tb_transaksi.tgl_transaksi, '%Y-%m') = ?";
                                        $params[] = date('Y-m', strtotime($date_picked));
                                        $types .= "s";
                                        break;
                                    case 'yearly':
                                        $where .= " AND YEAR(tb_transaksi.tgl_transaksi) = ?";
                                        $params[] = $date_picked;
                                        $types .= "s";
                                        break;
                                }
                            }

                            $query = "SELECT 
                                        tb_transaksi.id_transaksi,
                                        tb_transaksi.tgl_transaksi,
                                        COALESCE(tb_pelanggan.pelanggan_nama, 'Umum') as pelanggan_nama,
                                        COALESCE(tb_pelanggan.pelanggan_tlp, '-') as pelanggan_tlp,
                                        tb_barang.barang_nama,
                                        tb_detail_transaksi.detail_jumlah,
                                        tb_detail_transaksi.detail_total
                                    FROM tb_transaksi 
                                    JOIN tb_detail_transaksi ON tb_transaksi.id_transaksi = tb_detail_transaksi.id_transaksi 
                                    JOIN tb_barang ON tb_detail_transaksi.barang_id = tb_barang.barang_id 
                                    LEFT JOIN tb_pelanggan ON tb_transaksi.pelanggan_id = tb_pelanggan.pelanggan_id
                                    $where
                                    ORDER BY tb_transaksi.id_transaksi ASC, tb_transaksi.tgl_transaksi ASC";

                            $stmt = $koneksi->prepare($query);
                            if (!empty($params)) {
                                $stmt->bind_param($types, ...$params);
                            }
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                            ?>
                                <div class="table-responsive">
    <p class="text-info"><i>* Data diurutkan berdasarkan nomor transaksi dan tanggal transaksi</i></p>
    <table id="example1" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 10%;">No Faktur</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Nama Konsumen</th>
                <th style="width: 10%;">No. Tlp</th>
                <th style="width: 20%;">Nama Barang</th>
                <th class="text-right" style="width: 10%;">Jumlah</th>
                <th class="text-right" style="width: 20%;">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $no = 1;
        $current_transaksi = '';
        $transaksi_total = 0;
        $grand_total = 0;

        while ($row = $result->fetch_assoc()) {
            $new_transaksi = ($current_transaksi !== $row['id_transaksi']);
            if ($new_transaksi && $current_transaksi !== '') {
                // Print transaction subtotal
                echo "<tr class='table-secondary'>";
                echo "<td colspan='7' class='text-right'><strong>Total Transaksi {$current_transaksi}</strong></td>";
                echo "<td class='text-right'><strong>" . rupiah($transaksi_total) . "</strong></td>";
                echo "</tr>";
                $transaksi_total = 0;
            }
            
            $current_transaksi = $row['id_transaksi'];
            $transaksi_total += $row['detail_total'];
            $grand_total += $row['detail_total'];

            if ($new_transaksi) {
            ?>
                <tr>
                    <td class="text-center"><?php echo $no++ ?></td>
                    <td><?php echo htmlspecialchars($row['id_transaksi']) ?></td>
                    <td><?php echo tgl_indo($row['tgl_transaksi']) ?></td>
                    <td><?php echo htmlspecialchars($row['pelanggan_nama']) ?></td>
                    <td><?php echo htmlspecialchars($row['pelanggan_tlp']) ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php
            }
            ?>
            <tr>
                <td colspan="5"></td>
                <td><?php echo htmlspecialchars($row['barang_nama']) ?></td>
                <td class="text-right"><?php echo $row['detail_jumlah'] ?></td>
                <td class="text-right"><?php echo rupiah($row['detail_total']) ?></td>
            </tr>
        <?php
        }
        if ($current_transaksi !== '') {
            echo "<tr class='table-secondary'>";
            echo "<td colspan='7' class='text-right'><strong>Total Transaksi {$current_transaksi}</strong></td>";
            echo "<td class='text-right'><strong>" . rupiah($transaksi_total) . "</strong></td>";
            echo "</tr>";
            
            // Print grand total
            echo "<tr class='table-primary'>";
            echo "<td colspan='7' class='text-right'><strong>Total Keseluruhan Transaksi</strong></td>";
            echo "<td class='text-right'><strong>" . rupiah($grand_total) . "</strong></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>


                                </div>
                            <?php
                            } else {
                                echo "<p>Tidak ada data untuk periode yang dipilih.</p>";
                            }
                            ?>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var periodSelect = document.getElementById('period');
        var datePicker = document.getElementById('date_picker');

        function updateDatePicker() {
            if (periodSelect.value === 'all') {
                datePicker.disabled = true;
                datePicker.value = '';
            } else {
                datePicker.disabled = false;
                switch(periodSelect.value) {
                    case 'daily':
                        datePicker.type = 'date';
                        break;
                    case 'monthly':
                        datePicker.type = 'month';
                        break;
                    case 'yearly':
                        datePicker.type = 'number';
                        datePicker.min = '2000';
                        datePicker.max = new Date().getFullYear().toString();
                        datePicker.placeholder = 'YYYY';
                        break;
                }
                datePicker.value = '<?php echo $date_picker; ?>';
            }
        }

        periodSelect.addEventListener('change', updateDatePicker);
        updateDatePicker();
    });
</script>

