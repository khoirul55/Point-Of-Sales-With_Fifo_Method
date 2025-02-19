<?php
$id = $_GET['id'];
$ambil = $koneksi->query("SELECT * FROM tb_pengguna WHERE pengguna_id = $id");
$pecah = $ambil->fetch_object();

if (isset($_POST['edit'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $nama = $_POST['nama'];
    $level = $_POST['level'];

    $edit = $koneksi->query("UPDATE tb_pengguna SET 
        pengguna_user = '$user',
        pengguna_pass = '$pass',
        pengguna_nama = '$nama',
        pengguna_level = '$level'
        WHERE pengguna_id = '$id'");

    if ($edit) {
        echo "<script>alert('Data berhasil diupdate');</script>";
        echo "<script>window.location='index.php?page=data_pengguna'</script>";
    } else {
        echo "<script>alert('Data gagal diupdate: " . $koneksi->error . "');</script>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Data Pengguna</h3>
    </div>
    <div class="card-body">
        <form action="" method="post" class="form-horizontal">
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Username</label>
                <div class="col-sm-10">
                    <input type="text" name="user" value="<?php echo $pecah->pengguna_user ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Password</label>
                <div class="col-sm-10">
                    <input type="password" name="pass" value="<?php echo $pecah->pengguna_pass ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Nama</label>
                <div class="col-sm-10">
                    <input type="text" name="nama" value="<?php echo $pecah->pengguna_nama ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Level</label>
                <div class="col-sm-10">
                    <select name="level" class="form-control" required>
                        <option value="Admin" <?php echo ($pecah->pengguna_level == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="Pimpinan" <?php echo ($pecah->pengguna_level == 'Pemilik') ? 'selected' : ''; ?>>Pemilik</option>
                        <option value="Staff Gudang" <?php echo ($pecah->pengguna_level == 'Kasir') ? 'selected' : ''; ?>>Kasir</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="edit" class="btn btn-primary">Update</button>
                    <a href="index.php?page=data_pengguna" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </form>
    </div>
</div>