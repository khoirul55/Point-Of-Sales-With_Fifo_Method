<div class="span9">
    <div class="content">
        <div class="module">
            <div class="module-head">
                <h3>Edit Data Pelanggan</h3>
            </div>
            <div class="module-body">
                <a class="btn btn-primary" href="index.php?page=data_pelanggan">Kembali</a>
            </div>
            <div class="module-body table">
                <?php
                $id = $_GET['id'];
                $ambil = $koneksi->query("SELECT * FROM tb_pelanggan WHERE pelanggan_id='$id'");
                $pecah = $ambil->fetch_object();
                ?>
                <form method="post" class="form-horizontal row-fluid">
                    <div class="control-group">
                        <label class="control-label">Nama Pelanggan</label>
                        <div class="controls">
                            <input value="<?php echo $pecah->pelanggan_id ?>" type="hidden" name="id">
                            <input value="<?php echo $pecah->pelanggan_nama ?>" type="text" name="nama" placeholder="Nama" class="span8">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Alamat</label>
                        <div class="controls">
                            <input value="<?php echo $pecah->pelanggan_alamat ?>" type="text" name="alamat" placeholder="Alamat" class="span8">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">No Telephone</label>
                        <div class="controls">
                            <input value="<?php echo $pecah->pelanggan_tlp ?>" type="text" name="tlp" placeholder="No Telephone" class="span8">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Diskon (%)</label>
                        <div class="controls">
                            <input value="<?php echo $pecah->pelanggan_diskon ?>" type="number" name="diskon" placeholder="Diskon" class="span8" step="0.01" min="0" max="100">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" name="edit" class="btn btn-success" value="Edit">
                        </div>
                    </div>
                </form>
                <?php
                if (isset($_POST['edit'])) {
                    $id       = $_POST['id'];
                    $nama     = $_POST['nama'];
                    $alamat   = $_POST['alamat'];
                    $tlp      = $_POST['tlp'];
                    $diskon   = $_POST['diskon'];

                    $edit = $koneksi->query("UPDATE tb_pelanggan SET    
                                                pelanggan_nama     = '$nama',
                                                pelanggan_alamat   = '$alamat',
                                                pelanggan_tlp      = '$tlp',
                                                pelanggan_diskon   = '$diskon'
                                                WHERE pelanggan_id = '$id'");

                    if ($edit) {
                        echo "<script>alert('Data berhasil di edit');</script>";
                        echo "<script>window.location='index.php?page=data_pelanggan'</script>";
                    } else {
                        echo "<script>alert('Data gagal di edit');</script>";
                        echo "<script>window.location='index.php?page=data_pelanggan'</script>";
                    }
                }
                ?>
                <hr style="display: block; height: 1px;border: 0; border-top: 1px solid #ccc;margin: 1em 0; padding: 0;">
            </div>
        </div>
    </div>
    <!--/.module-->
</div>
<!--/.content-->
</div>