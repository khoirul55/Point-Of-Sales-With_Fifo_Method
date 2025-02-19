<?php


$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$id) {
    echo "<script>alert('ID supplier tidak valid');</script>";
    echo "<script>window.location='index.php?page=data_supplier'</script>";
    exit;
}

$koneksi->begin_transaction();

try {
    // Check if supplier can be deleted
    $stmt = $koneksi->prepare("SELECT COUNT(*) as supply_count FROM tb_barang_msk WHERE supplier_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();
    
    if ($row->supply_count > 0) {
        throw new Exception("Tidak dapat menghapus supplier - masih ada data supply barang yang dengan terkait supplier ini");
    }

    // Delete the supplier
    $stmt = $koneksi->prepare("DELETE FROM tb_supplier WHERE supplier_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows == 0) {
        throw new Exception("Data supplier tidak ditemukan");
    }

    $koneksi->commit();
    echo "<script>alert('Data supplier berhasil dihapus');</script>";
    echo "<script>window.location='index.php?page=data_supplier'</script>";
} catch (Exception $e) {
    $koneksi->rollback();
    echo "<script>alert('Data supplier gagal dihapus: " . htmlspecialchars($e->getMessage()) . "');</script>";
    echo "<script>window.location='index.php?page=data_supplier'</script>";
}
?>