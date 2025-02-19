<?php
require_once 'koneksi.php';
require_once 'session_check.php';
ensure_session_started();

function hapusTransaksi($id) {
    global $koneksi;
    $koneksi->begin_transaction();

    try {
        // Get transaction details
        $get_transaksi = $koneksi->prepare("SELECT dt.id_transaksi, t.status, dt.barang_id, dt.detail_jumlah 
                                           FROM tb_detail_transaksi dt
                                           JOIN tb_transaksi t ON dt.id_transaksi = t.id_transaksi
                                           WHERE dt.detail_id = ?");
        if ($get_transaksi === false) {
            throw new Exception("Error in query preparation: " . $koneksi->error);
        }
        $get_transaksi->bind_param("i", $id);
        $get_transaksi->execute();
        $transaksi = $get_transaksi->get_result()->fetch_object();

        if (!$transaksi) {
            throw new Exception("Detail transaksi tidak ditemukan.");
        }

        // Soft delete the detail transaction
        $soft_delete = $koneksi->prepare("UPDATE tb_detail_transaksi SET is_deleted = 1 WHERE detail_id = ?");
        $soft_delete->bind_param("i", $id);
        $soft_delete->execute();

        // Get all stock usage for this detail
        $get_usage = $koneksi->prepare("SELECT batch_id, jumlah FROM tb_penggunaan_stok WHERE detail_id = ?");
        $get_usage->bind_param("i", $id);
        $get_usage->execute();
        $usage_result = $get_usage->get_result();

        $total_restored = 0;
        while ($usage = $usage_result->fetch_object()) {
            // Restore stock to each batch
            $restore_batch = $koneksi->prepare("UPDATE tb_stok_batch 
                                              SET jumlah_tersisa = jumlah_tersisa + ? 
                                              WHERE batch_id = ?");
            $restore_batch->bind_param("ii", $usage->jumlah, $usage->batch_id);
            $restore_batch->execute();
            if ($restore_batch->affected_rows == 0) {
                throw new Exception("Gagal mengembalikan stok ke batch.");
            }
            $total_restored += $usage->jumlah;
        }

        // Restore main stock
        $restore_stock = $koneksi->prepare("UPDATE tb_barang 
                                          SET barang_stok = barang_stok + ? 
                                          WHERE barang_id = ?");
        if ($restore_stock === false) {
            throw new Exception("Gagal mempersiapkan query update stok: " . $koneksi->error);
        }
        $restore_stock->bind_param("ii", $total_restored, $transaksi->barang_id);
        if (!$restore_stock->execute()) {
            throw new Exception("Gagal mengembalikan stok utama: " . $restore_stock->error);
        }

        // Delete stock usage records
        $delete_usage = $koneksi->prepare("DELETE FROM tb_penggunaan_stok WHERE detail_id = ?");
        $delete_usage->bind_param("i", $id);
        $delete_usage->execute();

        // Update transaction total
        $update_total = $koneksi->prepare("UPDATE tb_transaksi 
                                          SET total_transaksi = COALESCE((SELECT SUM(detail_total) 
                                                                         FROM tb_detail_transaksi 
                                                                         WHERE id_transaksi = ? AND is_deleted = 0), 0) 
                                          WHERE id_transaksi = ?");
        $update_total->bind_param("ss", $transaksi->id_transaksi, $transaksi->id_transaksi);
        $update_total->execute();

        $koneksi->commit();
        return true;
    } catch (Exception $e) {
        $koneksi->rollback();
        error_log("Error in hapusTransaksi: " . $e->getMessage());
        return false;
    }
}

$id = $_GET['id'] ?? null;
if ($id) {
    if (hapusTransaksi($id)) {
        echo "<script>
            alert('Item berhasil dihapus dari keranjang dan stok dikembalikan');
            window.location='index.php?page=data_transaksi_pos_lanjut&id_transaksi=" . $_GET['id_transaksi'] . "';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus item: Terjadi kesalahan saat memproses penghapusan.');
            window.location='index.php?page=data_transaksi_pos_lanjut&id_transaksi=" . $_GET['id_transaksi'] . "';
        </script>";
    }
} else {
    echo "<script>
        alert('ID transaksi tidak valid');
        window.location='index.php?page=data_transaksi_pos';
    </script>";
}