<?php
$id = $_GET['id'];
$ambil = $koneksi->query("SELECT * FROM tb_barang WHERE barang_id = $id");
$pecah = $ambil->fetch_object();

if (isset($_POST['edit'])) {
    $kategori = $_POST['kategori'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $jual = $_POST['jual'];

    // Check if the kode barang already exists for other products
    $cek = $koneksi->query("SELECT * FROM tb_barang WHERE barang_kode = '$kode' AND barang_id != '$id'");
    if ($cek->num_rows > 0) {
        echo "<script>alert('Kode barang sudah ada!');</script>";
    } else {
        $edit = $koneksi->query("UPDATE tb_barang SET 
            kategori_id = '$kategori',
            barang_kode = '$kode',
            barang_nama = '$nama',
            barang_jual = '$jual'
            WHERE barang_id = '$id'");

        if ($edit) {
            echo "<script>alert('Data berhasil diupdate');</script>";
            echo "<script>window.location='index.php?page=data_barang'</script>";
        } else {
            echo "<script>alert('Data gagal diupdate: " . $koneksi->error . "');</script>";
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Data Barang</h3>
    </div>
    <div class="card-body">
        <form action="" method="post" class="form-horizontal">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kategori</label>
                <div class="col-sm-10">
                    <select name="kategori" class="form-control">
                        <?php
                        $ambil_kategori = $koneksi->query("SELECT * FROM tb_kategori");
                        while ($kategori = $ambil_kategori->fetch_object()) {
                            $selected = ($kategori->kategori_id == $pecah->kategori_id) ? 'selected' : '';
                            echo "<option value='{$kategori->kategori_id}' {$selected}>{$kategori->kategori_nama}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Kode Barang</label>
                <div class="col-sm-10">
                    <input type="text" name="kode" value="<?php echo $pecah->barang_kode ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Barang</label>
                <div class="col-sm-10">
                    <input type="text" name="nama" value="<?php echo $pecah->barang_nama ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Harga Jual</label>
                <div class="col-sm-10">
                    <input type="number" name="jual" value="<?php echo $pecah->barang_jual ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Stok</label>
                <div class="col-sm-10">
                    <input type="number" value="<?php echo $pecah->barang_stok ?>" class="form-control" readonly>
                    <small class="form-text text-muted">Stok diatur otomatis oleh sistem FIFO</small>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="edit" class="btn btn-primary">Update</button>
                    <a href="index.php?page=data_barang" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

