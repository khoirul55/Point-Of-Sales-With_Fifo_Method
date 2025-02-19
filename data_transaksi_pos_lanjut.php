<?php
require_once 'koneksi.php';
require_once 'session_check.php';
ensure_session_started();

if (!isset($_SESSION['current_transaction_id'])) {
    echo "<script>
        alert('Tidak ada transaksi aktif. Silakan mulai transaksi baru.');
        window.location.href='index.php?page=data_transaksi_pos';
    </script>";
    exit();
}

$id_transaksi = $_SESSION['current_transaction_id'];

// Check if this is a new transaction
$check_new = $koneksi->prepare("SELECT COUNT(*) FROM tb_detail_transaksi WHERE id_transaksi = ? AND is_deleted = 0");
$check_new->bind_param("s", $id_transaksi);
$check_new->execute();
$is_new = ($check_new->get_result()->fetch_row()[0] == 0);

if ($is_new) {
    // New transaction, ensure cart is empty
    $koneksi->query("UPDATE tb_detail_transaksi SET is_deleted = 1 WHERE id_transaksi = '$id_transaksi'");
}

if (isset($_POST['simpanmodal'])) {
    $barang = $_POST['barang_id'];
    $jumlah = $_POST['jumlah'];
    $id_transaksi = $_SESSION['current_transaction_id'];

    // Get product details
    $stmt = $koneksi->prepare("SELECT * FROM tb_barang WHERE barang_id = ?");
    $stmt->bind_param("i", $barang);
    $stmt->execute();
    $result = $stmt->get_result();
    $pecah_barang = $result->fetch_object();
    $harga = $pecah_barang->barang_jual;
    
    // Calculate total
    $total = $jumlah * $harga;
    
    $koneksi->begin_transaction();
    
    try {
        // Check if there's enough stock
        $stok_query = $koneksi->prepare("SELECT SUM(jumlah_tersisa) as total_stok FROM tb_stok_batch WHERE barang_id = ?");
        $stok_query->bind_param("i", $barang);
        $stok_query->execute();
        $stok_result = $stok_query->get_result();
        $stok_data = $stok_result->fetch_object();
        if ($stok_data->total_stok < $jumlah) {
            throw new Exception("Stok tidak mencukupi. Stok tersedia: " . $stok_data->total_stok);
        }

        // Always add a new detail transaction
        $insert_query = "INSERT INTO tb_detail_transaksi (id_transaksi, barang_id, detail_jumlah, detail_total) 
                    VALUES (?, ?, ?, ?)";
        $stmt = $koneksi->prepare($insert_query);
        $stmt->bind_param("siid", $id_transaksi, $barang, $jumlah, $total);
        $stmt->execute();
        
        if ($stmt->affected_rows == 0) {
            throw new Exception("Gagal menambahkan detail transaksi");
        }
        
        $detail_id = $koneksi->insert_id;
        
        // Process FIFO stock
        $sisa_jumlah = $jumlah;
        $ambil_batch = $koneksi->prepare("SELECT * FROM tb_stok_batch 
                                         WHERE barang_id = ? AND jumlah_tersisa > 0 
                                         ORDER BY tanggal_masuk ASC");
        $ambil_batch->bind_param("i", $barang);
        $ambil_batch->execute();
        $batch_result = $ambil_batch->get_result();
        
        while ($batch = $batch_result->fetch_object()) {
            if ($sisa_jumlah <= 0) break;
            
            $jumlah_diambil = min($sisa_jumlah, $batch->jumlah_tersisa);
            
            // Update batch stock
            $update_batch = $koneksi->prepare("UPDATE tb_stok_batch SET jumlah_tersisa = jumlah_tersisa - ? WHERE batch_id = ?");
            $update_batch->bind_param("ii", $jumlah_diambil, $batch->batch_id);
            $update_batch->execute();
            
            // Record stock usage
            $insert_usage = $koneksi->prepare("INSERT INTO tb_penggunaan_stok (detail_id, batch_id, jumlah, harga_beli) VALUES (?, ?, ?, ?)");
            $insert_usage->bind_param("iiid", $detail_id, $batch->batch_id, $jumlah_diambil, $batch->harga_beli);
            $insert_usage->execute();
            
            $sisa_jumlah -= $jumlah_diambil;
        }
        
        // Update main stock
        $update_stock = $koneksi->prepare("UPDATE tb_barang SET barang_stok = barang_stok - ? WHERE barang_id = ?");
        $update_stock->bind_param("ii", $jumlah, $barang);
        $update_stock->execute();
        
        // Update transaction total
        $update_total = $koneksi->prepare("UPDATE tb_transaksi 
        SET total_transaksi = (
            SELECT SUM(detail_total) 
            FROM tb_detail_transaksi 
            WHERE id_transaksi = ? AND is_deleted = 0
        ) 
        WHERE id_transaksi = ?");
        $update_total->bind_param("ss", $id_transaksi, $id_transaksi);
        $update_total->execute();
        
        $koneksi->commit();
        
        echo "<script>
            alert('Produk berhasil ditambahkan ');
            window.location='index.php?page=data_transaksi_pos_lanjut&id_transaksi=$id_transaksi';
        </script>";
        
    } catch (Exception $e) {
        $koneksi->rollback();
        echo "<script>
            alert('Gagal menambahkan produk: " . $e->getMessage() . "');
            window.location='index.php?page=data_transaksi_pos_lanjut&id_transaksi=$id_transaksi';
        </script>";
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = $search ? "AND (b.barang_nama LIKE ? OR b.barang_kode LIKE ?)" : '';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>TRANSAKSI</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">TRANSAKSI</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pilih Barang</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="" method="get" class="mb-3">
                                <input type="hidden" name="page" value="data_transaksi_pos_lanjut">
                                <input type="hidden" name="id_transaksi" value="<?php echo htmlspecialchars($id_transaksi); ?>">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari barang..." value="<?php echo htmlspecialchars($search); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <style>
                                .product-card {
                                    height: 100%;
                                    display: flex;
                                    flex-direction: column;
                                }
                                .product-card .card-body {
                                    flex-grow: 1;
                                    display: flex;
                                    flex-direction: column;
                                }
                                .product-name {
                                    font-weight: bold;
                                    font-size: 14px;
                                    line-height: 1.3;
                                    max-height: 2.6em;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    display: -webkit-box;
                                    -webkit-line-clamp: 2;
                                    -webkit-box-orient: vertical;
                                }
                                .product-price {
                                    font-size: 16px;
                                    margin-top: auto;
                                }
                                .product-stock {
                                    font-size: 12px;
                                    color: #6c757d;
                                }
                            </style>

                            <div class="row">
                                <?php
                                $query = "SELECT b.*, k.kategori_nama, SUM(sb.jumlah_tersisa) as stok_tersedia 
                                    FROM tb_barang b
                                    JOIN tb_kategori k ON b.kategori_id = k.kategori_id 
                                    LEFT JOIN tb_stok_batch sb ON b.barang_id = sb.barang_id
                                    WHERE 1=1 $search_condition
                                    GROUP BY b.barang_id
                                    HAVING stok_tersedia > 0
                                    ORDER BY b.barang_nama ASC";
                                $stmt = $koneksi->prepare($query);
                                if ($search) {
                                    $search_param = "%$search%";
                                    $stmt->bind_param("ss", $search_param, $search_param);
                                }
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($pecah = $result->fetch_object()) {
                                ?>
                                    <div class="col-md-3 col-sm-6 col-12 mb-3">
                                        <div class="card product-card">
                                            <div class="card-body">
                                                <h5 class="card-title product-name"><?php echo htmlspecialchars($pecah->barang_nama); ?></h5>
                                                <p class="card-text product-price"><?php echo rupiah($pecah->barang_jual); ?></p>
                                                <p class="card-text product-stock">
                                                    <small class="text-muted">Stok: <?php echo $pecah->stok_tersedia; ?></small>
                                                </p>
                                                <button class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#modal-<?php echo $pecah->barang_id; ?>">
                                                    <i class="fas fa-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="modal-<?php echo $pecah->barang_id; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="" method="post">
                                                    <input type="hidden" name="barang_id" value="<?php echo $pecah->barang_id; ?>">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title"><?php echo htmlspecialchars($pecah->barang_nama); ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Jumlah</label>
                                                            <input type="number" name="jumlah" class="form-control" min="1" max="<?php echo $pecah->stok_tersedia; ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                                        <button type="submit" name="simpanmodal" class="btn btn-primary">Tambah</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Barang Belanja</h3>
                        </div>
                        <div class="card-body">
                            <?php
                            $query = "SELECT tb_transaksi.*, tb_pelanggan.* 
                                      FROM tb_transaksi 
                                      LEFT JOIN tb_pelanggan ON tb_transaksi.pelanggan_id = tb_pelanggan.pelanggan_id 
                                      WHERE tb_transaksi.id_transaksi = ?";
                            $stmt = $koneksi->prepare($query);
                            $stmt->bind_param("s", $id_transaksi);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $ambil_detail = $result->fetch_object();
                            $diskon = $ambil_detail->pelanggan_diskon ?? 0;
                            ?>
                            <div class="alert alert-info">
                                <?php if ($ambil_detail->pelanggan_id): ?>
                                    <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($ambil_detail->pelanggan_nama); ?></p>
                                    <p><strong>No. Telp:</strong> <?php echo htmlspecialchars($ambil_detail->pelanggan_tlp); ?></p>
                                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($ambil_detail->pelanggan_alamat); ?></p>
                                    <p><strong>Diskon:</strong> <?php echo number_format($diskon, 2); ?>%</p>
                                <?php else: ?>
                    <p><strong>Pelanggan:</strong> Umum</p>
                    <p><strong>Diskon:</strong> 0%</p>
                <?php endif; ?>
                <p><strong>No. Transaksi:</strong> <?php echo htmlspecialchars($id_transaksi); ?></p>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th width="60">Qty</th>
                        <th width="100">Subtotal</th>
                        <th width="30"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $cart_query = "SELECT dt.*, b.barang_nama, b.barang_jual 
                    FROM tb_detail_transaksi dt
                    JOIN tb_barang b ON dt.barang_id = b.barang_id
                    JOIN tb_transaksi t ON dt.id_transaksi = t.id_transaksi
                    WHERE t.id_transaksi = ? AND dt.is_deleted = 0
                    ORDER BY dt.detail_id ASC";
                    $cart_stmt = $koneksi->prepare($cart_query);
                    $cart_stmt->bind_param("s", $id_transaksi);
                    $cart_stmt->execute();
                    $cart_result = $cart_stmt->get_result();

                    while ($item = $cart_result->fetch_assoc()) {
                        $total += $item['detail_total'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['barang_nama']); ?></td>
                            <td class="text-center"><?php echo $item['detail_jumlah']; ?></td>
                            <td class="text-right"><?php echo rupiah($item['detail_total']); ?></td>
                            <td>
                                <a href="index.php?page=aksi_transaksi/transaksi_hapus&id=<?php echo $item['detail_id']; ?>&id_transaksi=<?php echo $id_transaksi; ?>" 
                                   class="btn btn-danger btn-sm btn-delete-item">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td class="text-right" colspan="2"><strong><?php echo rupiah($total); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Diskon (<?php echo number_format($diskon, 2); ?>%)</strong></td>
                        <td class="text-right" colspan="2"><strong><?php echo rupiah($total * $diskon / 100); ?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Total Setelah Diskon</strong></td>
                        <td class="text-right" colspan="2"><strong><?php echo rupiah($total - ($total * $diskon / 100)); ?></strong></td>
                    </tr>
                </tfoot>
            </table>

            <form method="post">
                <div class="form-group">
                    <label>Pembayaran</label>
                    <input type="number" name="pembayaran" class="form-control" required>
                </div>
                <button type="submit" name="proses_pembayaran" class="btn btn-primary btn-block">Proses Pembayaran</button>
            </form>
            <?php
            if (isset($_POST['proses_pembayaran'])) {
                $pembayaran = $_POST['pembayaran'];
                
                // Calculate total with discount
                $total_after_discount = $total - ($total * $diskon / 100);
                
                if ($pembayaran < $total_after_discount) {
                    echo "<div class='alert alert-danger mt-3'>Pembayaran kurang! Total: " . rupiah($total_after_discount) . ", Pembayaran: " . rupiah($pembayaran) . "</div>";
                } else {
                    // Start database transaction
                    $koneksi->begin_transaction();
                    
                    try {
                        // Update transaction
                        $sql_update = "UPDATE tb_transaksi 
                                       SET payment = ?, 
                                           discount = ?,
                                           total_transaksi = ?,
                                           status = 'selesai'
                                       WHERE id_transaksi = ?";
                        $stmt = $koneksi->prepare($sql_update);
                        $discount_amount = $total * $diskon / 100;
                        $stmt->bind_param("ddds", $pembayaran, $discount_amount, $total_after_discount, $id_transaksi);
                        $stmt->execute();
                        
                        $koneksi->commit();
                        
                        $kembalian = $pembayaran - $total_after_discount;
                        echo "<div class='alert alert-success mt-3'>
                                <p><strong>Total Belanja:</strong> " . rupiah($total_after_discount) . "</p>
                                <p><strong>Pembayaran:</strong> " . rupiah($pembayaran) . "</p>
                                <p><strong>Kembalian:</strong> " . rupiah($kembalian) . "</p>
                              </div>";
                        
                        echo "<div class='text-center mt-3'>
                                <a href='index.php?page=aksi_transaksi/cetak_struk&id=" . $id_transaksi . "' class='btn btn-primary' target='_blank' 
                                   onclick='finishTransaction(\"" . $id_transaksi . "\")'>
                                    <i class='fas fa-print'></i> Cetak Struk
                                </a>
                              </div>";
                        
                        // Clear the current transaction from the session
                        unset($_SESSION['current_transaction_id']);
                    } catch (Exception $e) {
                        $koneksi->rollback();
                        echo "<div class='alert alert-danger mt-3'>Gagal memproses pembayaran: " . $e->getMessage() . "</div>";
                    }
                }
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
$(function () {
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('.modal').on('shown.bs.modal', function () {
        $(this).find('input[name="jumlah"]').focus();
    });

    $('.btn-delete-item').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Anda yakin?',
            text: "Item akan dihapus dari keranjang!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});

function finishTransaction(transaksiId) {
    var printWindow = window.open('index.php?page=aksi_transaksi/cetak_struk&id=' + transaksiId, '_blank');
    
    // Clear the cart and create a new transaction
    fetch('clear_cart.php?id_transaksi=' + transaksiId)
        .then(response => response.text())
        .then(data => {
            console.log(data);
            // Redirect to new transaction page after a short delay
            setTimeout(function() {
                window.location.href = 'index.php?page=data_transaksi_pos';
            }, 1000);
        })
        .catch(error => console.error('Error:', error));
}
</script>

