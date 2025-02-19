<?php
$id = $_GET['id'];

// Start a transaction
$koneksi->begin_transaction();

try {
    // Delete the user from tb_pengguna
    $koneksi->query("DELETE FROM tb_pengguna WHERE pengguna_id = $id");

    // Commit the transaction
    $koneksi->commit();

    echo "<script>alert('Data berhasil dihapus');</script>";
    echo "<script>window.location='index.php?page=data_pengguna'</script>";
} catch (Exception $e) {
    // An error occurred; rollback the transaction
    $koneksi->rollback();
    echo "<script>alert('Data gagal dihapus: " . $e->getMessage() . "');</script>";
    echo "<script>window.location='index.php?page=data_pengguna'</script>";
}