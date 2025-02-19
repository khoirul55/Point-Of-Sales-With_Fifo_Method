<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$user_name = isset($_SESSION['pengguna_nama']) ? $_SESSION['pengguna_nama'] : '';
$user_level = isset($_SESSION['pengguna_level']) ? $_SESSION['pengguna_level'] : '';

$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require_once $root . '/POSFIFO_ADEKELAPA/koneksi.php';

// Cek login
if (empty($_SESSION['pengguna'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TOKO KELAPA ADE</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/adminlte/plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="assets/adminlte/dist/css/adminlte.min.css">
    <?php include 'components/head.php'; ?>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <?php include 'components/top_bar.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include 'components/side_bar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Main content -->
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        $file_path = __DIR__ . "/{$page}.php";
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            // Check if it's in a subdirectory
            $parts = explode('/', $page);
            if (count($parts) > 1) {
                $file_path = __DIR__ . "/{$parts[0]}/{$parts[1]}.php";
                if (file_exists($file_path)) {
                    include $file_path;
                } else {
                    echo "Halaman tidak ditemukan";
                }
            } else {
                echo "Halaman tidak ditemukan";
            }
        }
        ?>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Footer -->
    <?php include 'components/footer.php'; ?>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="assets/adminlte/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/adminlte/dist/js/adminlte.js"></script>
<?php include 'components/script.php'; ?>
</body>
</html>

