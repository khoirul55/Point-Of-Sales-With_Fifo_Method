<?php
// data_transaksi_pos.php - Initial Transaction Page

require_once 'koneksi.php';
require_once 'session_check.php';
ensure_session_started();

// Generate a new transaction ID with format TRX-YmdHis
function generateTransactionId() {
    return 'TRX-' . date('YmdHis');
}

if (isset($_GET['new_transaction'])) {
    $_SESSION['current_transaction_id'] = $_GET['new_transaction'];
} else {
    $_SESSION['current_transaction_id'] = generateTransactionId();
}

$id_transaksi = $_SESSION['current_transaction_id'];
$tanggal = date('Y-m-d');
$pelanggan_id = 0;

if (isset($_POST['simpan'])) {
    $pelanggan_id = $_POST['pelanggan_id'];
    $tanggal = $_POST['tanggal'];
    $id_transaksi = $_POST['id_transaksi'];

    $check_existing = $koneksi->prepare("SELECT id_transaksi FROM tb_transaksi WHERE id_transaksi = ?");
    $check_existing->bind_param("s", $id_transaksi);
    $check_existing->execute();
    $result = $check_existing->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nomor transaksi sudah ada. Silakan gunakan nomor lain.'
            });
        </script>";
    } else {
        $insert = $koneksi->prepare("INSERT INTO tb_transaksi (id_transaksi, pelanggan_id, tgl_transaksi, status) VALUES (?, ?, ?, 'pending')");
        $insert->bind_param("sis", $id_transaksi, $pelanggan_id, $tanggal);

        if ($insert->execute()) {
            $_SESSION['current_transaction_id'] = $id_transaksi;
            echo "<script>window.location.href='index.php?page=data_transaksi_pos_lanjut&id_transaksi=$id_transaksi';</script>";
            exit();
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memulai transaksi: " . $koneksi->error . "'
                });
            </script>";
        }
    }
}
?>

<div class="content-wrapper bg-light">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Mulai Transaksi Baru
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item active">Mulai Transaksi</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Transaction Form Card -->
                <div class="col-md-6">
                    <div class="card card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-invoice mr-2"></i>
                                Informasi Transaksi
                            </h3>
                        </div>
                        <form action="" method="post" id="transactionForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="id_transaksi">
                                        <i class="fas fa-hashtag mr-1"></i>
                                        Nomor Transaksi
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="id_transaksi" 
                                           name="id_transaksi" 
                                           value="<?php echo htmlspecialchars($id_transaksi); ?>" 
                                           readonly>
                                </div>
                                
                                <div class="form-group">
                                    <label for="tanggal">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        Tanggal Transaksi
                                    </label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="tanggal" 
                                           name="tanggal" 
                                           value="<?php echo htmlspecialchars($tanggal); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="pelanggan_id">
                                        <i class="fas fa-user mr-1"></i>
                                        Member
                                    </label>
                                    <select class="form-control select2bs4" 
                                            id="pelanggan_id" 
                                            name="pelanggan_id" 
                                            style="width: 100%;">
                                        <option value="0">Pelanggan Umum</option>
                                        <?php
                                        $ambil = $koneksi->query("SELECT * FROM tb_pelanggan ORDER BY pelanggan_nama");
                                        while ($pelanggan = $ambil->fetch_object()) {
                                            echo "<option value='" . htmlspecialchars($pelanggan->pelanggan_id) . "'>" 
                                                . htmlspecialchars($pelanggan->pelanggan_nama) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" name="simpan" class="btn btn-primary btn-block btn-lg">
                                    <i class="fas fa-play mr-2"></i>
                                    Mulai Transaksi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Cards -->
                <div class="col-md-6">
                    <!-- Welcome Card -->
                    <div class="card card-info shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle mr-2"></i>
                                Informasi Sistem
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5>
                                    <i class="icon fas fa-info mr-2"></i>
                                    Selamat Datang!
                                </h5>
                                <p class="mb-0">Selamat datang di sistem Point of Sale (POS) TOKO KELAPA ADE.</p>
                            </div>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check text-success mr-2"></i>
                                    Untuk memulai transaksi baru, silakan isi form di sebelah kiri
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
                                    Nomor transaksi dibuat otomatis untuk keamanan
                                </li>
                                <li>
                                    <i class="fas fa-user text-info mr-2"></i>
                                    Pilih "Pelanggan Umum" jika pelanggan tidak terdaftar
                                </li>
                            </ul>
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(function () {
    // Initialize Select2 with Bootstrap 4 theme
    $('.select2bs4').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih pelanggan',
        allowClear: true
    });

    // Form validation
    $('#transactionForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });
});
</script>

