<?php
$id = $_GET['id'];
$ambil = $koneksi->query("SELECT * FROM tb_kategori WHERE kategori_id = $id");
$pecah = $ambil->fetch_object();

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];

    $edit = $koneksi->query("UPDATE tb_kategori SET kategori_nama = '$nama' WHERE kategori_id = '$id'");

    if ($edit) {
        echo "<script>alert('Data berhasil diedit');</script>";
        echo "<script>window.location='index.php?page=data_kategori'</script>";
    } else {
        echo "<script>alert('Data gagal diedit');</script>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Data Kategori</h3>
    </div>
    <div class="card-body">
        <form action="" method="post" class="form-horizontal">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama Kategori</label>
                <div class="col-sm-10">
                    <input type="text" name="nama" value="<?php echo $pecah->kategori_nama ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=data_kategori" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>

