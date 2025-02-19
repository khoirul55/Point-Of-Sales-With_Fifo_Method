<style>
    .main-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        padding: 0.25rem 0.5rem; /* Reduced padding */
        background-color: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.1); /* Reduced shadow */
        height: 40px; /* Set a fixed height */
    }
    .main-header .nav-link {
        padding: 0.15rem 0.3rem; /* Reduced padding */
        font-size: 0.85rem; /* Smaller font size */
    }
    .navbar-nav .nav-item {
        display: flex;
        align-items: center;
    }
    body.sidebar-collapse .main-header {
        margin-left: 0;
    }
    .navbar-brand {
        font-size: 1rem; /* Smaller brand font size */
        padding-top: 0;
        padding-bottom: 0;
    }
</style>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top custom-topbar">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="index.php" class="nav-link">Home</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
                <?php 
                echo isset($_SESSION['pengguna']) ? htmlspecialchars($_SESSION['pengguna']) : 'Guest'; 
                echo isset($_SESSION['pengguna_level']) ? ' | ' . htmlspecialchars($_SESSION['pengguna_level']) : '';
                ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="index.php?page=logout" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- /.navbar -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    var header = document.querySelector('.main-header');
    var headerHeight = header.offsetHeight;
    document.body.style.paddingTop = headerHeight + 'px';
    
    // Adjust content padding when window is resized
    window.addEventListener('resize', function() {
        headerHeight = header.offsetHeight;
        document.body.style.paddingTop = headerHeight + 'px';
    });
});
</script>

