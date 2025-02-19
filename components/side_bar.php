<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
        <span class="brand-text font-weight-light">TOKO KELAPA ADE</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?php echo isset($_SESSION['pengguna_nama']) ? htmlspecialchars($_SESSION['pengguna_nama']) : 'Guest'; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="index.php" class="nav-link <?php echo empty($_GET['page']) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php if (isset($_SESSION['pengguna_level']) && $_SESSION['pengguna_level'] == "Pemilik"): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Laporan
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="index.php?page=data_laporan_barang" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan Stok Barang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=data_laporan_supply" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan Supply</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=data_laporan_transaksi" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan Transaksi Penjualan</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['pengguna_level']) && $_SESSION['pengguna_level'] == "Kasir"): ?>
                    <li class="nav-item">
                        <a href="index.php?page=data_transaksi_pos" class="nav-link <?php echo ($_GET['page'] ?? '') == 'data_transaksi_pos' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=data_pelanggan" class="nav-link <?php echo ($_GET['page'] ?? '') == 'data_pelanggan' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Member</p>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['pengguna_level']) && $_SESSION['pengguna_level'] == "Admin"): ?>
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Master Data
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                        <li class="nav-item">
                                <a href="index.php?page=data_kategori" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kategori</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=data_supplier" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=data_supply" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Supply Barang</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="index.php?page=data_barang" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Barang</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?page=data_pelanggan" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Member</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a href="index.php?page=data_transaksi_pos" class="nav-link <?php echo ($_GET['page'] ?? '') == 'data_transaksi_pos' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-cash-register"></i>
                            <p>Transaksi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                                <a href="index.php?page=data_pengguna" class="nav-link">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                    <p>Pengguna</p>
                                </a>
                      </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="index.php?page=logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
