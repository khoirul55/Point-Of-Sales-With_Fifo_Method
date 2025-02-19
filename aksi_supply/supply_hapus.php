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

// Modified supply_hapus.php
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    echo "<script>alert('Invalid supply ID');</script>";
    echo "<script>window.location='index.php?page=data_supply'</script>";
    exit;
}

$koneksi->begin_transaction();

try {
    // Check if supply can be deleted
    if (!canDeleteSupply($koneksi, $id)) {
        throw new Exception("Tidak dapat menghapus supply - stock telah digunakan dalam transaksi");
    }

    // Get supply details
    $stmt = $koneksi->prepare("SELECT * FROM tb_barang_msk WHERE msk_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $supply = $stmt->get_result()->fetch_object();
    
    if (!$supply) {
        throw new Exception("Data supply tidak ditemukan");
    }

    // Delete from tb_stok_batch first (due to foreign key constraint)
    $stmt = $koneksi->prepare("DELETE FROM tb_stok_batch WHERE msk_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Delete the supply entry
    $stmt = $koneksi->prepare("DELETE FROM tb_barang_msk WHERE msk_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Update total stock in tb_barang
    $stmt = $koneksi->prepare("
        UPDATE tb_barang 
        SET barang_stok = (
            SELECT COALESCE(SUM(jumlah_tersisa), 0) 
            FROM tb_stok_batch 
            WHERE barang_id = ?
        ) 
        WHERE barang_id = ?
    ");
    $stmt->bind_param("ii", $supply->barang_id, $supply->barang_id);
    $stmt->execute();

    $koneksi->commit();
    echo "<script>alert('Data berhasil dihapus');</script>";
    echo "<script>window.location='index.php?page=data_supply'</script>";
} catch (Exception $e) {
    $koneksi->rollback();
    echo "<script>alert('Data gagal dihapus: " . htmlspecialchars($e->getMessage()) . "');</script>";
    echo "<script>window.location='index.php?page=data_supply'</script>";
}
?>
