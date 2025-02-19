<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Pengguna</h3>
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
                    <th>Username</th>
                    <th>Password</th>
                    <th>Nama</th>
                    <th>Level</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ambil = $koneksi->query("SELECT * FROM tb_pengguna");
                $no = 1;
                while ($pecah = $ambil->fetch_object()) {
                ?>
                    <tr>
                        <td><?php echo $no++ ?></td>
                        <td><?php echo $pecah->pengguna_user ?></td>
                        <td><?php echo $pecah->pengguna_pass ?></td>
                        <td><?php echo $pecah->pengguna_nama ?></td>
                        <td><?php echo $pecah->pengguna_level ?></td>
                        <td>
                            <a href="index.php?page=aksi_pengguna/pengguna_edit&id=<?php echo $pecah->pengguna_id ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="index.php?page=aksi_pengguna/pengguna_hapus&id=<?php echo $pecah->pengguna_id ?>" class="btn btn-danger btn-sm delete-confirm">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Pengguna</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="user" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="pass" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level" class="form-control" required>
                            <option value="Admin">Admin</option>
                            <option value="Pemilik">Pemilik</option>
                            <option value="Kasir">Kasir</option>
                        </select>
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
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $nama = $_POST['nama'];
    $level = $_POST['level'];

    $tambah = $koneksi->query("INSERT INTO tb_pengguna (pengguna_user,pengguna_pass,pengguna_nama,pengguna_level) VALUES ('$user','$pass','$nama','$level')");

    if ($tambah) {
        echo "<script>alert('Data berhasil di tambah');</script>";
        echo "<script>window.location='index.php?page=data_pengguna'</script>";
    } else {
        echo "<script>alert('Data gagal di tambah');</script>";
        echo "<script>window.location='index.php?page=data_pengguna'</script>";
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

$(function() {
    $('.delete-confirm').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
});
</script>
