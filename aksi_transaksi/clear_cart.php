<?php
require_once 'koneksi.php';
require_once 'session_check.php';
ensure_session_started();

if (isset($_GET['id_transaksi'])) {
    $id_transaksi = $_GET['id_transaksi'];
    
    // Update transaction status to completed
    $update_status = $koneksi->prepare("UPDATE tb_transaksi SET status = 'selesai' WHERE id_transaksi = ?");
    $update_status->bind_param("s", $id_transaksi);
    $update_status->execute();
    
    // Generate new transaction ID
    $new_id_transaksi = 'TRX-' . date('YmdHis');
    
    // Create new pending transaction
    $koneksi->query("INSERT INTO tb_transaksi (id_transaksi, tgl_transaksi, status) 
                     VALUES ('$new_id_transaksi', NOW(), 'pending')");
    
    $_SESSION['current_transaction_id'] = $new_id_transaksi;
    
    echo "Cart cleared and new transaction created successfully";
} else {
    echo "Error: No transaction ID provided";
}
?>

