<?php


// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Initialize variables
$period = isset($_POST['period']) ? $_POST['period'] : 'all';
$date_picker = isset($_POST['date_picker']) ? $_POST['date_picker'] : '';

// Add logging for debugging
error_log("Initial period: " . $period);
error_log("Initial date_picker: " . $date_picker);

// If yearly period, ensure we only use the year
if ($period === 'yearly') {
    $date_picker = substr($date_picker, 0, 4) . '-01-01';
}

error_log("Processed date_picker: " . $date_picker);

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

error_log("Period text: " . $periodText);

?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Laporan Supply Barang</li>
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
                            <h4 class="mb-3">LAPORAN SUPPLY BARANG <?php echo $periodText; ?></h4>
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
                                            $print_date = $date_picker;
                                            if ($period === 'yearly') {
                                                $print_date = substr($date_picker, 0, 4) . '-01-01';
                                            }
                                            $print_url = "aksi_laporan/cetak_supply.php?period=" . $period . "&start_date=" . $print_date;
                                            echo "<a href='$print_url' target='_blank' class='btn btn-success form-control'><i class='fas fa-print'></i> Cetak Laporan</a>";
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php
                        $where = "";
                        $params = [];
                        $types = "";

                        if (isset($_POST['filter']) && $_POST['period'] != 'all') {
                            $period = $_POST['period'];
                            $date_picked = $_POST['date_picker'];
                            
                            error_log("Filter applied - Period: " . $period . ", Date picked: " . $date_picked);

                            switch ($period) {
                                case 'daily':
                                    $where = "WHERE DATE(tb_barang_msk.msk_tgl) = ?";
                                    $params[] = $date_picked;
                                    $types .= "s";
                                    break;
                                case 'monthly':
                                    $where = "WHERE DATE_FORMAT(tb_barang_msk.msk_tgl, '%Y-%m') = ?";
                                    $params[] = date('Y-m', strtotime($date_picked));
                                    $types .= "s";
                                    break;
                                case 'yearly':
                                    $where = "WHERE YEAR(tb_barang_msk.msk_tgl) = ?";
                                    $params[] = substr($date_picked, 0, 4);
                                    $types .= "s";
                                    break;
                            }
                        }

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
                                ORDER BY tb_supplier.supplier_nama ASC, tb_barang_msk.msk_tgl ASC";

                        error_log("SQL Query: " . $query);
                        error_log("SQL Params: " . print_r($params, true));

                        $stmt = $koneksi->prepare($query);
                        if (!empty($params)) {
                            $stmt->bind_param($types, ...$params);
                        }
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            ?>
                            <div class="table-responsive">
                                <p class="text-info"><i>* Data diurutkan berdasarkan nama supplier dan tanggal masuk barang</i></p>
                                <table id="example1" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Supplier</th>
                                            <th>Faktur Supply</th>
                                            <th>Kategori</th>
                                            <th>Nama Barang</th>
                                            <th class="text-right">Jumlah</th>
                                            <th class="text-right">Harga Beli</th>
                                            <th class="text-right">Total</th>
                                            <th>Tanggal Masuk</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $no = 1;
                                    $current_supplier = '';
                                    $supplier_total = 0;
                                    $grand_total = 0;

                                    while ($row = $result->fetch_assoc()) {
                                        error_log("Processing row: " . print_r($row, true));

                                        $new_supplier = ($current_supplier !== $row['supplier_nama']);
                                        if ($new_supplier && $current_supplier !== '') {
                                            // Print supplier subtotal
                                            echo "<tr class='table-secondary'>";
                                            echo "<td colspan='7' class='text-right'><strong>Total Supply dari {$current_supplier}</strong></td>";
                                            echo "<td class='text-right'><strong>" . rupiah($supplier_total) . "</strong></td>";
                                            echo "<td></td>";
                                            echo "</tr>";
                                            $supplier_total = 0;
                                        }
                                        
                                        $current_supplier = $row['supplier_nama'];
                                        $subtotal = $row['msk_jumlah'] * $row['msk_harga_beli'];
                                        $supplier_total += $subtotal;
                                        $grand_total += $subtotal;
                                        ?>
                                        <tr>
                                            <td><?php echo $no++ ?></td>
                                            <td><?php echo htmlspecialchars($row['supplier_nama']) ?></td>
                                            <td><?php echo htmlspecialchars($row['msk_faktur']) ?></td>
                                            <td><?php echo htmlspecialchars($row['kategori_nama']) ?></td>
                                            <td><?php echo htmlspecialchars($row['barang_nama']) ?></td>
                                            <td class="text-right"><?php echo $row['msk_jumlah'] ?></td>
                                            <td class="text-right"><?php echo rupiah($row['msk_harga_beli']) ?></td>
                                            <td class="text-right"><?php echo rupiah($subtotal) ?></td>
                                            <td><?php echo tgl_indo($row['msk_tgl']) ?></td>
                                        </tr>
                                    <?php
                                    }
                                    if ($current_supplier !== '') {
                                        echo "<tr class='table-secondary'>";
                                        echo "<td colspan='7' class='text-right'><strong>Total Supply dari {$current_supplier}</strong></td>";
                                        echo "<td class='text-right'><strong>" . rupiah($supplier_total) . "</strong></td>";
                                        echo "<td></td>";
                                        echo "</tr>";
                                        
                                        // Print grand total
                                        echo "<tr class='table-primary'>";
                                        echo "<td colspan='7' class='text-right'><strong>Total Keseluruhan Supply</strong></td>";
                                        echo "<td class='text-right'><strong>" . rupiah($grand_total) . "</strong></td>";
                                        echo "<td></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                    </tbody>
                                </table>
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

