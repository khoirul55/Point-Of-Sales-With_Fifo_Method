<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Supplier</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Data Supplier</li>
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
                            <h3 class="card-title">Data Supplier</h3>
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
                                        <th>Nama Supplier</th>
                                        <th>Alamat</th>
                                        <th>No Telpon</th>
                                        <th width="110">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ambil = $koneksi->query("SELECT * FROM tb_supplier");
                                    $no = 1;
                                    while ($pecah = $ambil->fetch_object()) {
                                    ?>
                                        <tr>
                                            <td><?php echo $no++ ?></td>
                                            <td><?php echo $pecah->supplier_nama ?></td>
                                            <td><?php echo $pecah->supplier_alamat ?></td>
                                            <td><?php echo $pecah->supplier_tlp ?></td>
                                            <td>
                                                <a href="index.php?page=aksi_supplier/supplier_edit&id=<?php echo $pecah->supplier_id ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="index.php?page=aksi_supplier/supplier_hapus&id=<?php echo $pecah->supplier_id ?>" class="btn btn-danger btn-sm">Hapus</a>
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
                <h4 class="modal-title">Tambah Data Supplier</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama Supplier</label>
                        <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat Supplier</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Alamat Supplier" required>
                    </div>
                    <div class="form-group">
                        <label for="tlp">No Telpon</label>
                        <input type="text" class="form-control" id="tlp" name="tlp" placeholder="No Telpon" required>
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
    $nama   = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $tlp    = $_POST['tlp'];

    $tambah = $koneksi->query("INSERT INTO tb_supplier (supplier_nama,supplier_alamat,supplier_tlp) VALUES ('$nama','$alamat','$tlp')");

    if ($tambah) {
        echo "<script>
            alert('Data berhasil ditambahkan');
            window.location='index.php?page=data_supplier';
        </script>";
    } else {
        echo "<script>
            alert('Data gagal ditambahkan');
            window.location='index.php?page=data_supplier';
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

