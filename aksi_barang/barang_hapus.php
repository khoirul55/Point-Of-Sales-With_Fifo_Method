<?php
$id = $_GET['id'];

// Start a transaction
$koneksi->begin_transaction();

try {
    // Delete related records in tb_penggunaan_stok
    $koneksi->query("DELETE FROM tb_penggunaan_stok WHERE batch_id IN (SELECT batch_id FROM tb_stok_batch WHERE barang_id = $id)");

    // Delete related records in tb_stok_batch
    $koneksi->query("DELETE FROM tb_stok_batch WHERE barang_id = $id");

    // Delete related records in tb_detail_transaksi
    $koneksi->query("DELETE FROM tb_detail_transaksi WHERE barang_id = $id");

    // Delete related records in tb_barang_msk
    $koneksi->query("DELETE FROM tb_barang_msk WHERE barang_id = $id");

    // Delete the product from tb_barang
    $koneksi->query("DELETE FROM tb_barang WHERE barang_id = $id");

    // Commit the transaction
    $koneksi->commit();

    echo "<script>alert('Data berhasil dihapus');</script>";
    echo "<script>window.location='index.php?page=data_barang'</script>";
} catch (Exception $e) {
    // An error occurred; rollback the transaction
    $koneksi->rollback();
    echo "<script>alert('Data gagal dihapus: " . $e->getMessage() . "');</script>";
    echo "<script>window.location='index.php?page=data_barang'</script>";
}

