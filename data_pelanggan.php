<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Member</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Member</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Member</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                                    + Tambah Data
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="10">No</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Alamat</th>
                                        <th>No Telpon</th>
                                        <th>Diskon (%)</th>
                                        <th width="110">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ambil = $koneksi->query("SELECT * FROM tb_pelanggan");
                                    $no = 1;
                                    while ($pecah = $ambil->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $no++ ?></td>
                                            <td><?php echo $pecah->pelanggan_nama ?></td>
                                            <td><?php echo $pecah->pelanggan_alamat ?></td>
                                            <td><?php echo $pecah->pelanggan_tlp ?></td>
                                            <td><?php echo $pecah->pelanggan_diskon ?></td>
                                            <td>
                                                <a href="index.php?page=aksi_pelanggan/pelanggan_edit&id=<?php echo $pecah->pelanggan_id ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="index.php?page=aksi_pelanggan/pelanggan_hapus&id=<?php echo $pecah->pelanggan_id ?>" class="btn btn-danger btn-sm">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Member</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Pelanggan" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="tlp">No Telephone</label>
                        <input type="text" class="form-control" id="tlp" name="tlp" placeholder="No Telephone" required>
                    </div>
                    <div class="form-group">
                        <label for="diskon">Diskon (%)</label>
                        <input type="number" class="form-control" id="diskon" name="diskon" placeholder="Diskon" step="0.01" min="0" max="100" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if (isset($_POST['simpan'])) {
    $nama     = $_POST['nama'];
    $alamat   = $_POST['alamat'];
    $tlp      = $_POST['tlp'];
    $diskon   = $_POST['diskon'];
    $tambah = $koneksi->query("INSERT INTO tb_pelanggan (pelanggan_nama,pelanggan_alamat,pelanggan_tlp,pelanggan_diskon) VALUES ('$nama','$alamat','$tlp', '$diskon')");

    if ($tambah) {
        echo "<script>
            alert('Data berhasil ditambahkan');
            window.location='index.php?page=data_pelanggan';
        </script>";
    } else {
        echo "<script>
            alert('Data gagal ditambahkan');
            window.location='index.php?page=data_pelanggan';
        </script>";
    }
}
?>

<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>

