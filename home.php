<?php
// Pastikan session sudah dimulai dan variabel level pengguna tersedia
if (!isset($_SESSION['pengguna_level'])) {
    header("Location: login.php");
    exit;
}

$user_name = isset($_SESSION['pengguna_nama']) ? $_SESSION['pengguna_nama'] : 'Pengguna';
$user_level = $_SESSION['pengguna_level'];

// Fungsi untuk mengecek apakah pengguna adalah admin atau pemilik
function isAdminOrOwner($level) {
    return in_array(strtolower($level), ['admin', 'pemilik']);
}

// Ambil data untuk dashboard
$today_sales = getTodaySales($koneksi);
$today_transactions = getTodayTransactions($koneksi);

// Data yang hanya diambil untuk admin dan pemilik
if (isAdminOrOwner($user_level)) {
    $total_stock = getTotalStock($koneksi);
    $low_stock_count = getLowStockCount($koneksi);
    $top_products = getTopProducts($koneksi);
    $sales_data = getSalesData($koneksi);
    $sales_by_category = getSalesByCategory($koneksi);

    $dates_json = json_encode($sales_data['dates']);
    $sales_json = json_encode($sales_data['sales']);
    $category_names = json_encode(array_column($sales_by_category, 'kategori_nama'));
    $category_sales = json_encode(array_column($sales_by_category, 'total_sales'));
}
?>

<!-- CSS kustom tetap sama -->
<style>
    .small-box { transition: all 0.3s; }
    .small-box:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .chart-container { position: relative; margin: auto; height: 300px; width: 100%; }
    .card { transition: all 0.3s; }
    .card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
</style>

<!-- Alert selamat datang -->
<div class="alert alert-info alert-dismissible fade show" role="alert" id="welcomeAlert">
    <h5><i class="icon fas fa-info"></i> Selamat datang!</h5>
    <?php
    switch(strtolower($user_level)) {
        case 'admin':
            echo "Halo Admin $user_name, selamat datang di dashboard administrasi.";
            break;
        case 'kasir':
            echo "Halo Kasir $user_name, selamat bekerja hari ini!";
            break;
        case 'pemilik':
            echo "Selamat datang, Bapak/Ibu $user_name. Ini adalah ringkasan bisnis Anda.";
            break;
        default:
            echo "Selamat datang, $user_name.";
    }
    ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo rupiah($today_sales); ?></h3>
                        <p>Penjualan Hari Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $today_transactions; ?></h3>
                        <p>Transaksi Hari Ini</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                </div>
            </div>
            <?php if (isAdminOrOwner($user_level)): ?>
            <div class="col-lg-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $total_stock; ?></h3>
                        <p>Total Stok</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo $low_stock_count; ?></h3>
                        <p>Stok Menipis</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (isAdminOrOwner($user_level)): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Grafik Penjualan 7 Hari Terakhir</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Penjualan per Kategori (Bulan Ini)</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Produk Terlaris</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 150px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Cari produk..." id="productSearch">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Total Terjual</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">
                                <?php foreach ($top_products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['barang_nama']); ?></td>
                                        <td><?php echo $product['total_sold']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Stok Menipis</h3>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Stok Tersisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $low_stock_items = getLowStockItems($koneksi);
                                foreach ($low_stock_items as $item): 
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['barang_nama']); ?></td>
                                        <td><?php echo $item['barang_stok']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if (isAdminOrOwner($user_level)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    var ctx = document.getElementById('salesChart').getContext('2d');
    var salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $dates_json; ?>,
            datasets: [{
                label: 'Penjualan',
                data: <?php echo $sales_json; ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + context.parsed.y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    var ctxCategory = document.getElementById('categoryChart').getContext('2d');
    var categoryChart = new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: <?php echo $category_names; ?>,
            datasets: [{
                data: <?php echo $category_sales; ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Pencarian Cepat untuk Produk Terlaris
    $('#productSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("#productTableBody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});
</script>
<?php endif; ?>

<script>
// Fungsi untuk menutup alert selamat datang setelah beberapa detik
setTimeout(function() {
    $("#welcomeAlert").fadeOut("slow");
}, 10000); // Alert akan hilang setelah 10 detik
</script>