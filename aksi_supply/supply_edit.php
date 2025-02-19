<?php

function canDeleteSupply($koneksi, $msk_id) {
    // Check if any stock from this batch has been used
    $query = "SELECT COUNT(*) as used_count 
              FROM tb_penggunaan_stok ps 
              JOIN tb_stok_batch sb ON ps.batch_id = sb.batch_id 
              WHERE sb.msk_id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $msk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    
    return $row->used_count == 0;
}

if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php?page=data_supply'</script>";
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Handle form submission
if (isset($_POST['update'])) {
    $koneksi->begin_transaction();
    
    try {
        $supplier_id = filter_input(INPUT_POST, 'supplier', FILTER_SANITIZE_NUMBER_INT);
        $faktur = filter_input(INPUT_POST, 'faktur', FILTER_SANITIZE_STRING);
        $jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_SANITIZE_NUMBER_INT);
        $harga = filter_input(INPUT_POST, 'harga', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $tanggal = filter_input(INPUT_POST, 'tanggal', FILTER_SANITIZE_STRING);
        $keterangan = filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING);

        // Check if any stock has been used
        if (!canDeleteSupply($koneksi, $id)) {
            throw new Exception("Tidak dapat mengubah supply - stock telah digunakan dalam transaksi");
        }

        // Update supply record
        $stmt = $koneksi->prepare("
            UPDATE tb_barang_msk 
            SET supplier_id = ?, msk_faktur = ?, msk_jumlah = ?, 
                msk_harga_beli = ?, msk_tgl = ?, msk_ket = ?
            WHERE msk_id = ?
        ");
        $stmt->bind_param("isidssi", $supplier_id, $faktur, $jumlah, $harga, $tanggal, $keterangan, $id);
        $stmt->execute();

        // Update stock batch
        $stmt = $koneksi->prepare("
            UPDATE tb_stok_batch 
            SET jumlah_tersisa = ?, harga_beli = ?, tanggal_masuk = ?
            WHERE msk_id = ?
        ");
        $stmt->bind_param("idsi", $jumlah, $harga, $tanggal, $id);
        $stmt->execute();

        // Update total stock in barang
        $stmt = $koneksi->prepare("
            UPDATE tb_barang b
            SET barang_stok = (
                SELECT SUM(sb.jumlah_tersisa)
                FROM tb_stok_batch sb
                WHERE sb.barang_id = b.barang_id
            )
            WHERE barang_id = (
                SELECT barang_id 
                FROM tb_barang_msk 
                WHERE msk_id = ?
            )
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $koneksi->commit();
        echo "<script>alert('Data berhasil diupdate');</script>";
        echo "<script>window.location='index.php?page=data_supply';</script>";
    } catch (Exception $e) {
        $koneksi->rollback();
        echo "<script>alert('Data gagal diupdate: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// Get current supply data
$stmt = $koneksi->prepare("
    SELECT m.*, b.barang_nama, b.kategori_id
    FROM tb_barang_msk m
    JOIN tb_barang b ON m.barang_id = b.barang_id
    WHERE m.msk_id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$supply = $stmt->get_result()->fetch_object();

if (!$supply) {
    echo "<script>alert('Data supply tidak ditemukan');</script>";
    echo "<script>window.location='index.php?page=data_supply';</script>";
    exit;
}
?>

<!-- Edit Form HTML -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Data Supply</h3>
    </div>
    <div class="card-body">
        <form action="" method="post">
            <div class="form-group">
                <label>Nama Supplier</label>
                <select name="supplier" class="form-control select2" required>
                    <?php
                    $suppliers = $koneksi->query("SELECT * FROM tb_supplier ORDER BY supplier_nama ASC");
                    while ($supplier = $suppliers->fetch_object()) {
                        $selected = ($supplier->supplier_id == $supply->supplier_id) ? 'selected' : '';
                        echo "<option value='" . $supplier->supplier_id . "' " . $selected . ">" . 
                             htmlspecialchars($supplier->supplier_nama) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Faktur</label>
                <input type="text" name="faktur" class="form-control" 
                       value="<?php echo htmlspecialchars($supply->msk_faktur) ?>" required>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control" 
                       value="<?php echo $supply->msk_jumlah ?>" required>
            </div>
            <div class="form-group">
                <label>Harga Beli</label>
                <input type="number" name="harga" class="form-control" step="0.01" 
                       value="<?php echo $supply->msk_harga_beli ?>" required>
            </div>
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" 
                       value="<?php echo $supply->msk_tgl ?>" required>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <input type="text" name="keterangan" class="form-control" 
                       value="<?php echo htmlspecialchars($supply->msk_ket) ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="index.php?page=data_supply" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
</div>