<?php
require_once 'koneksi.php';

if (isset($_POST['simpan'])) {
    $supplier = filter_input(INPUT_POST, 'supplier', FILTER_SANITIZE_NUMBER_INT);
    $kategori = filter_input(INPUT_POST, 'kategori', FILTER_SANITIZE_NUMBER_INT);
    $barang_nama = filter_input(INPUT_POST, 'barang_nama', FILTER_SANITIZE_STRING);
    $faktur = filter_input(INPUT_POST, 'faktur', FILTER_SANITIZE_STRING);
    $jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_SANITIZE_NUMBER_INT);
    $harga = filter_input(INPUT_POST, 'harga', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $tanggal = filter_input(INPUT_POST, 'tanggal', FILTER_SANITIZE_STRING);
    $keterangan = filter_input(INPUT_POST, 'keterangan', FILTER_SANITIZE_STRING);

    // Start transaction
    $koneksi->begin_transaction();

    try {
        // Check if the product exists
        $stmt = $koneksi->prepare("SELECT * FROM tb_barang WHERE barang_nama = ? AND kategori_id = ?");
        $stmt->bind_param("si", $barang_nama, $kategori);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            // Insert new product
            $stmt = $koneksi->prepare("INSERT INTO tb_barang (barang_nama, kategori_id, barang_stok, barang_tgl) VALUES (?, ?, 0, ?)");
            $stmt->bind_param("sis", $barang_nama, $kategori, $tanggal);
            $stmt->execute();
            $barang_id = $koneksi->insert_id;
        } else {
            $barang = $result->fetch_object();
            $barang_id = $barang->barang_id;
            
            // Update existing product's date
            $stmt = $koneksi->prepare("UPDATE tb_barang SET barang_tgl = ? WHERE barang_id = ?");
            $stmt->bind_param("si", $tanggal, $barang_id);
            $stmt->execute();
        }

        // Insert supply record
        $stmt = $koneksi->prepare("INSERT INTO tb_barang_msk (supplier_id, barang_id, msk_faktur, msk_jumlah, msk_harga_beli, msk_tgl, msk_ket) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisidss", $supplier, $barang_id, $faktur, $jumlah, $harga, $tanggal, $keterangan);
        $stmt->execute();
        $msk_id = $koneksi->insert_id;

        // Insert into stock batch
        $stmt = $koneksi->prepare("INSERT INTO tb_stok_batch (barang_id, msk_id, jumlah_tersisa, harga_beli, tanggal_masuk) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiids", $barang_id, $msk_id, $jumlah, $harga, $tanggal);
        $stmt->execute();

        // Update total stock in barang
        $stmt = $koneksi->prepare("UPDATE tb_barang SET barang_stok = barang_stok + ? WHERE barang_id = ?");
        $stmt->bind_param("ii", $jumlah, $barang_id);
        $stmt->execute();

        // Update date in tb_barang if newer - REMOVED
        // $stmt = $koneksi->prepare("UPDATE tb_barang SET barang_tgl = GREATEST(barang_tgl, ?) WHERE barang_id = ?");
        // $stmt->bind_param("si", $tanggal, $barang_id);
        // $stmt->execute();

        $koneksi->commit();
        echo "<script>alert('Data berhasil disimpan');</script>";
        echo "<script>window.location='index.php?page=data_supply';</script>";
    } catch (Exception $e) {
        $koneksi->rollback();
        echo "<script>alert('Data gagal disimpan: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Supply Barang</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                + Tambah Data
            </button>
        </div>
    </div>
    <div class="card-body">
        <p class="text-info mb-3"><i>* Data diurutkan berdasarkan tanggal masuk terbaru</i></p>
        <table id="example1" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="10">No</th>
                    <th>Nama Supplier</th>
                    <th>Kategori</th>
                    <th>Nama Barang</th>
                    <th>Faktur</th>
                    <th>Jumlah</th>
                    <th>Sisa Stok</th>
                    <th>Harga Beli</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT 
                            tb_barang_msk.*,
                            tb_supplier.supplier_nama,
                            tb_barang.barang_nama,
                            tb_kategori.kategori_nama,
                            tb_stok_batch.jumlah_tersisa
                        FROM tb_barang_msk 
                        JOIN tb_supplier ON tb_barang_msk.supplier_id = tb_supplier.supplier_id 
                        JOIN tb_barang ON tb_barang_msk.barang_id = tb_barang.barang_id 
                        JOIN tb_kategori ON tb_barang.kategori_id = tb_kategori.kategori_id
                        LEFT JOIN tb_stok_batch ON tb_barang_msk.msk_id = tb_stok_batch.msk_id
                        ORDER BY msk_tgl DESC, msk_id DESC";
                        
                $result = $koneksi->query($query);
                $no = 1;
                while ($pecah = $result->fetch_object()) {
                    ?>
                    <tr>
                        <td><?php echo $no++ ?></td>
                        <td><?php echo htmlspecialchars($pecah->supplier_nama) ?></td>
                        <td><?php echo htmlspecialchars($pecah->kategori_nama) ?></td>
                        <td><?php echo htmlspecialchars($pecah->barang_nama) ?></td>
                        <td><?php echo htmlspecialchars($pecah->msk_faktur) ?></td>
                        <td><?php echo $pecah->msk_jumlah ?></td>
                        <td><?php echo $pecah->jumlah_tersisa ?></td>
                        <td><?php echo rupiah($pecah->msk_harga_beli) ?></td>
                        <td><?php echo tgl_indo($pecah->msk_tgl) ?></td>
                        <td><?php echo htmlspecialchars($pecah->msk_ket) ?></td>
                        <td>
                            <a href="index.php?page=aksi_supply/supply_edit&id=<?php echo $pecah->msk_id ?>" 
                               class="btn btn-warning btn-sm">Edit</a>
                            <a href="index.php?page=aksi_supply/supply_hapus&id=<?php echo $pecah->msk_id ?>" 
                               class="btn btn-danger btn-sm delete-confirm">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Supply</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Supplier</label>
                        <select name="supplier" class="form-control select2" required>
                            <option value="">Pilih Supplier</option>
                            <?php
                            $ambil = $koneksi->query("SELECT * FROM tb_supplier ORDER BY supplier_nama ASC");
                            while ($pecah = $ambil->fetch_object()) {
                                ?>
                                <option value="<?php echo $pecah->supplier_id ?>">
                                    <?php echo htmlspecialchars($pecah->supplier_nama) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control select2" required>
                            <option value="">Pilih Kategori</option>
                            <?php
                            $ambil_kategori = $koneksi->query("SELECT * FROM tb_kategori ORDER BY kategori_nama ASC");
                            while ($kategori = $ambil_kategori->fetch_object()) {
                                ?>
                                <option value="<?php echo $kategori->kategori_id ?>">
                                    <?php echo htmlspecialchars($kategori->kategori_nama) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="barang_nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Faktur</label>
                        <input type="text" name="faktur" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input type="number" name="harga" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" required>
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

<script>
$(function () {
    $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

    $('.select2').select2({
        theme: 'bootstrap4'
    });

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

