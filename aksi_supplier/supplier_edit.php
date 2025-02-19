<?php


if (!isset($_GET['id'])) {
    echo "<script>window.location='index.php?page=data_supplier'</script>";
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

// Handle form submission
if (isset($_POST['update'])) {
    $koneksi->begin_transaction();
    
    try {
        $nama = filter_input(INPUT_POST, 'nama', FILTER_SANITIZE_STRING);
        $alamat = filter_input(INPUT_POST, 'alamat', FILTER_SANITIZE_STRING);
        $telepon = filter_input(INPUT_POST, 'telepon', FILTER_SANITIZE_STRING);

        // Check if supplier has any associated transactions
        $stmt = $koneksi->prepare("SELECT COUNT(*) as supply_count FROM tb_barang_msk WHERE supplier_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_object();

        // Update supplier record
        $stmt = $koneksi->prepare("
            UPDATE tb_supplier 
            SET supplier_nama = ?, supplier_alamat = ?, supplier_tlp = ?
            WHERE supplier_id = ?
        ");
        $stmt->bind_param("sssi", $nama, $alamat, $telepon, $id);
        $stmt->execute();

        $koneksi->commit();
        echo "<script>alert('Data supplier berhasil diupdate');</script>";
        echo "<script>window.location='index.php?page=data_supplier';</script>";
    } catch (Exception $e) {
        $koneksi->rollback();
        echo "<script>alert('Data supplier gagal diupdate: " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// Get current supplier data
$stmt = $koneksi->prepare("SELECT * FROM tb_supplier WHERE supplier_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$supplier = $stmt->get_result()->fetch_object();

if (!$supplier) {
    echo "<script>alert('Data supplier tidak ditemukan');</script>";
    echo "<script>window.location='index.php?page=data_supplier';</script>";
    exit;
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Data Supplier</h3>
    </div>
    <div class="card-body">
        <form action="" method="post">
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" name="nama" class="form-control" 
                       value="<?php echo htmlspecialchars($supplier->supplier_nama) ?>" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <input type="text" name="alamat" class="form-control" 
                       value="<?php echo htmlspecialchars($supplier->supplier_alamat) ?>" required>
            </div>
            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="telepon" class="form-control" 
                       value="<?php echo htmlspecialchars($supplier->supplier_tlp) ?>" required>
            </div>
            <div class="form-group">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="index.php?page=data_supplier" class="btn btn-default">Kembali</a>
            </div>
        </form>
    </div>
</div>