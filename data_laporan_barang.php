<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                  
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Laporan Stok Barang</li>
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
                            <h4 class="mb-3">LAPORAN STOK BARANG <?php echo $periodText; ?></h4>
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
                                            $print_url = "aksi_laporan/cetak_barang.php?period=" . $period . "&start_date=" . $date_picker;
                                            echo "<a href='$print_url' target='_blank' class='btn btn-success form-control'><i class='fas fa-print'></i> Cetak Laporan</a>";
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php
$where = "WHERE tb_stok_batch.jumlah_tersisa > 0";
$params = [];
$types = "";

if (isset($_POST['filter']) && $_POST['period'] != 'all') {
    $period = $_POST['period'];
    $date_picked = $_POST['date_picker'];
    
    switch ($period) {
        case 'daily':
            $where .= " AND DATE(tb_stok_batch.tanggal_masuk) = ?";
            $params[] = $date_picked;
            $types .= "s";
            break;
        case 'monthly':
            $date_parts = explode('-', $date_picked);
            $where .= " AND YEAR(tb_stok_batch.tanggal_masuk) = ? AND MONTH(tb_stok_batch.tanggal_masuk) = ?";
            $params[] = $date_parts[0]; // Year
            $params[] = $date_parts[1]; // Month
            $types .= "ss";
            break;
        case 'yearly':
            $where .= " AND YEAR(tb_stok_batch.tanggal_masuk) = ?";
            $params[] = $date_picked;
            $types .= "s";
            break;
    }
}

// Lanjutkan dengan query dan pemrosesan data
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

$stmt = $koneksi->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                ?>
                                <div class="table-responsive">
                                    <p class="text-info"><i>* Data diurutkan menggunakan metode FIFO berdasarkan tanggal masuk barang</i></p>
                                    <table id="example1" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Batch ID</th>
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
                                        $no = 1;
                                        $current_barang = '';
                                        $total_stok = 0;
                                        $grand_total_stok = 0;

                                        while ($pecah = $result->fetch_object()) {
                                            $new_barang = ($current_barang !== $pecah->barang_nama);
                                            if ($new_barang && $current_barang !== '') {
                                                // Print total row for previous group
                                                echo "<tr class='table-secondary'>";
                                                echo "<td colspan='10' class='text-right'><strong>Total Stok {$current_barang}</strong></td>";
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
                                                <td><?php echo $pecah->batch_id ?></td>
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
                                           echo "<tr class='table-secondary'>";
                                           echo "<td colspan='10' class='text-right'><strong>Total Stok {$current_barang}</strong></td>";
                                           echo "<td class='text-right'><strong>{$total_stok}</strong></td>";
                                           echo "</tr>";
                                           
                                           // Print grand total
                                           echo "<tr class='table-primary'>";
                                           echo "<td colspan='10' class='text-right'><strong>Total Keseluruhan Stok</strong></td>";
                                           echo "<td class='text-right'><strong>{$grand_total_stok}</strong></td>";
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
