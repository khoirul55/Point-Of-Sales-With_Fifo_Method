<?php
$id = $_GET['id'];

// Start a transaction
$koneksi->begin_transaction();

try {
    // Check if there are any products using this category
    $check = $koneksi->query("SELECT * FROM tb_barang WHERE kategori_id = $id");
    if ($check->num_rows > 0) {
        throw new Exception("Kategori ini masih digunakan oleh beberapa produk. Hapus atau ubah kategori produk terlebih dahulu.");
    }

    // Delete the category
    $hapus = $koneksi->query("DELETE FROM tb_kategori WHERE kategori_id = $id");

    // Commit the transaction
    $koneksi->commit();

    echo "<script>alert('Data berhasil dihapus');</script>";
    echo "<script>window.location='index.php?page=data_kategori'</script>";
} catch (Exception $e) {
    // An error occurred; rollback the transaction
    $koneksi->rollback();
    echo "<script>alert('Data gagal dihapus: " . $e->getMessage() . "');</script>";
    echo "<script>window.location='index.php?page=data_kategori'</script>";
}

