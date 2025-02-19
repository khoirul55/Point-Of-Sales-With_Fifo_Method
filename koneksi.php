<?php
$host = 'localhost';
$name = 'root';
$pass = '';
$db   = 'db_fifo';
$no   = 1;
$nodua   = 1;
$koneksi = mysqli_connect($host, $name, $pass, $db);

if ($koneksi) {
    // echo "Terkoneksi";
} else {
    echo "Gagal Koneksi";
}

// Untuk Rupiah
if (!function_exists('rupiah')) {
    function rupiah($angka) {
        if (!is_numeric($angka)) return "Rp 0";
        $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
        return $hasil_rupiah;
    }
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal)
    {
        error_log("tgl_indo input: " . $tanggal);
        if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
        
        $bulan = array(
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des'
        );
        
        try {
            $pecahkan = explode('-', $tanggal);
            if (count($pecahkan) !== 3) return $tanggal;
            
            $tgl = (int)$pecahkan[2];
            $bln = (int)$pecahkan[1];
            $thn = (int)$pecahkan[0];
            
            $result = $tgl . ' ' . $bulan[$bln] . ' ' . $thn;
            error_log("tgl_indo output: " . $result);
            return $result;
        } catch (Exception $e) {
            error_log("tgl_indo error: " . $e->getMessage());
            return $tanggal;
        }
    }
}




// Low stock items checker with prepared statement
if (!function_exists('getLowStockItems')) {
    function getLowStockItems($threshold = 10) {
        global $koneksi;
        $query = "SELECT barang_id, barang_nama, barang_stok 
                 FROM tb_barang 
                 WHERE barang_stok <= ?";
                 
        if ($stmt = $koneksi->prepare($query)) {
            $stmt->bind_param("i", $threshold);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            
            $low_stock_items = [];
            while ($row = $result->fetch_assoc()) {
                $low_stock_items[] = $row;
            }
            return $low_stock_items;
        }
        return [];
    }
}

function getTodaySales($koneksi) {
    $query = "SELECT COALESCE(SUM(total_transaksi), 0) as total FROM tb_transaksi WHERE DATE(tgl_transaksi) = CURDATE() AND status = 'selesai'";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in today's sales query: " . $koneksi->error);
        return 0;
    }
    return $result->fetch_assoc()['total'];
}

function getTodayTransactions($koneksi) {
    $query = "SELECT COUNT(*) as count FROM tb_transaksi WHERE DATE(tgl_transaksi) = CURDATE() AND status = 'selesai'";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in today's transactions query: " . $koneksi->error);
        return 0;
    }
    return $result->fetch_assoc()['count'];
}

function getTotalStock($koneksi) {
    $query = "SELECT SUM(jumlah_tersisa) as total FROM tb_stok_batch";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in total stock query: " . $koneksi->error);
        return 0;
    }
    return $result->fetch_assoc()['total'];
}

function getLowStockCount($koneksi) {
    $query = "SELECT COUNT(*) as count FROM tb_barang WHERE barang_stok < 10";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in low stock query: " . $koneksi->error);
        return 0;
    }
    return $result->fetch_assoc()['count'];
}

function getTopProducts($koneksi) {
    $query = "SELECT b.barang_nama, SUM(dt.detail_jumlah) as total_sold
               FROM tb_detail_transaksi dt
               JOIN tb_barang b ON dt.barang_id = b.barang_id
               JOIN tb_transaksi t ON dt.id_transaksi = t.id_transaksi
               WHERE t.status = 'selesai'
               GROUP BY dt.barang_id
               ORDER BY total_sold DESC
               LIMIT 5";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in top products query: " . $koneksi->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSalesByCategory($koneksi) {
    $query = "SELECT k.kategori_nama, SUM(dt.detail_total) as total_sales
              FROM tb_detail_transaksi dt
              JOIN tb_barang b ON dt.barang_id = b.barang_id
              JOIN tb_kategori k ON b.kategori_id = k.kategori_id
              JOIN tb_transaksi t ON dt.id_transaksi = t.id_transaksi
              WHERE t.status = 'selesai' AND MONTH(t.tgl_transaksi) = MONTH(CURRENT_DATE())
              GROUP BY k.kategori_id
              ORDER BY total_sales DESC
              LIMIT 5";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in sales by category query: " . $koneksi->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getLowStockItems($koneksi, $threshold = 10) {
    $query = "SELECT barang_id, barang_nama, barang_stok 
              FROM tb_barang 
              WHERE barang_stok <= ?
              ORDER BY barang_stok ASC
              LIMIT 5";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $threshold);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTopProductsToday($koneksi) {
    $query = "SELECT b.barang_nama, SUM(dt.detail_jumlah) as total_sold
               FROM tb_detail_transaksi dt
               JOIN tb_barang b ON dt.barang_id = b.barang_id
               JOIN tb_transaksi t ON dt.id_transaksi = t.id_transaksi
               WHERE t.status = 'selesai' AND DATE(t.tgl_transaksi) = CURDATE()
               GROUP BY dt.barang_id
               ORDER BY total_sold DESC
               LIMIT 5";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in top products today query: " . $koneksi->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSalesData($koneksi) {
    $query = "SELECT DATE(tgl_transaksi) as date, COALESCE(SUM(total_transaksi), 0) as total
               FROM tb_transaksi
               WHERE tgl_transaksi >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                 AND status = 'selesai'
               GROUP BY DATE(tgl_transaksi)
               ORDER BY date ASC";
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in sales data query: " . $koneksi->error);
        return [];
    }
    
    $last_7_days = [];
    for ($i = 6; $i >= 0; $i--) {
        $last_7_days[date('Y-m-d', strtotime("-$i days"))] = 0;
    }

    while ($row = $result->fetch_assoc()) {
        $last_7_days[$row['date']] = $row['total'];
    }

    $dates = [];
    $sales = [];
    foreach ($last_7_days as $date => $total) {
        $dates[] = date('d/m', strtotime($date));
        $sales[] = $total;
    }

    return ['dates' => $dates, 'sales' => $sales];
}
function createNewTransaction($koneksi) {
    $new_id_transaksi = 'TRX-' . date('ymdHis');
    $koneksi->query("INSERT INTO tb_transaksi (id_transaksi, tgl_transaksi, total_transaksi, discount, payment, status) 
                     VALUES ('$new_id_transaksi', NOW(), 0, 0, 0, 'pending')");
    return $new_id_transaksi;
}

function clearCurrentTransaction($koneksi, $id_transaksi) {
    $koneksi->begin_transaction();
    
    try {
        // Soft delete items from cart
        $stmt = $koneksi->prepare("UPDATE tb_detail_transaksi SET is_deleted = 1 WHERE id_transaksi = ?");
        $stmt->bind_param("s", $id_transaksi);
        $stmt->execute();
        
        // Create new transaction
        $new_id_transaksi = 'TRX-' . date('ymdHis');
        $stmt = $koneksi->prepare("INSERT INTO tb_transaksi (id_transaksi, tgl_transaksi, total_transaksi, discount, payment, status) 
                                   VALUES (?, NOW(), 0, 0, 0, 'pending')");
        $stmt->bind_param("s", $new_id_transaksi);
        $stmt->execute();
        
        $koneksi->commit();
        return $new_id_transaksi;
    } catch (Exception $e) {
        $koneksi->rollback();
        error_log("Error in clearCurrentTransaction: " . $e->getMessage());
        return false;
    }
}

function cleanupPendingTransactions($koneksi, $timeout_minutes = 60) {
    $stmt = $koneksi->prepare("UPDATE tb_transaksi 
                               SET status = 'cancelled' 
                               WHERE status = 'pending' 
                               AND TIMESTAMPDIFF(MINUTE, tgl_transaksi, NOW()) > ?");
    $stmt->bind_param("i", $timeout_minutes);
    $stmt->execute();
}

// Ubah fungsi cleanupOldTransactions agar menggunakan soft delete
function cleanupOldTransactions($koneksi) {
    // Hanya update is_deleted untuk transaksi yang dibatalkan
    $koneksi->query("UPDATE tb_detail_transaksi 
                     SET is_deleted = 1
                     WHERE id_transaksi IN (
                         SELECT id_transaksi 
                         FROM tb_transaksi 
                         WHERE status = 'cancelled'
                     )");
}

// Tambahkan fungsi baru untuk memastikan transaksi yang selesai tidak terhapus
function restoreCompletedTransactions($koneksi) {
    $koneksi->query("UPDATE tb_detail_transaksi 
                     SET is_deleted = 0
                     WHERE id_transaksi IN (
                         SELECT id_transaksi 
                         FROM tb_transaksi 
                         WHERE status = 'selesai'
                     )");
}

function getUnpricedItems($koneksi) {
    $query = "SELECT DISTINCT tb_barang.* 
              FROM tb_barang 
              LEFT JOIN tb_stok_batch ON tb_barang.barang_id = tb_stok_batch.barang_id
              WHERE tb_barang.barang_jual = 0 OR tb_barang.barang_jual IS NULL
              GROUP BY tb_barang.barang_id
              HAVING SUM(tb_stok_batch.jumlah_tersisa) > 0 OR SUM(tb_stok_batch.jumlah_tersisa) IS NULL
              ORDER BY tb_barang.barang_nama ASC";
    
    $result = $koneksi->query($query);
    if (!$result) {
        error_log("Error in unpriced items query: " . $koneksi->error);
        return [];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}


cleanupPendingTransactions($koneksi);
cleanupOldTransactions($koneksi);
restoreCompletedTransactions($koneksi); 
?>

