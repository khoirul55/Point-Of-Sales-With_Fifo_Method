<?php

require_once 'koneksi.php';
require_once 'session_check.php';
ensure_session_started();

function setAlert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<div class='alert alert-{$alert['type']} alert-dismissible fade show' role='alert'>
                {$alert['message']}
                <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
                    <span aria-hidden='true'>&times;</span>
                </button>
              </div>";
        unset($_SESSION['alert']);
    }
}

require_once 'koneksi.php';

// Initialize message variable
$message = '';
$message_type = '';

// Proses form tambah/edit barang
if (isset($_POST['simpan'])) {
    $barang_id = filter_input(INPUT_POST, 'barang_id', FILTER_SANITIZE_NUMBER_INT);
    $kode = filter_input(INPUT_POST, 'kode', FILTER_SANITIZE_STRING);
    $jual = filter_input(INPUT_POST, 'jual', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Check if the kode barang already exists
    $stmt = $koneksi->prepare("SELECT * FROM tb_barang WHERE barang_kode = ? AND barang_id != ?");
    $stmt->bind_param("si", $kode, $barang_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        setAlert('danger', 'Kode barang sudah ada!');
    } else {
        $stmt = $koneksi->prepare("UPDATE tb_barang SET barang_kode = ?, barang_jual = ? WHERE barang_id = ?");
        $stmt->bind_param("sdi", $kode, $jual, $barang_id);
        
        if ($stmt->execute()) {
            setAlert('success', 'Data berhasil diupdate');
        } else {
            setAlert('danger', 'Data gagal diupdate');
        }
    }
    
    echo "<script>window.location='index.php?page=data_barang';</script>";
    
   
}

// Modified query to properly handle FIFO batches
$query = "SELECT 
    tb_barang.*,
    tb_kategori.kategori_nama,
    tb_stok_batch.batch_id,
    tb_stok_batch.jumlah_tersisa,
    tb_stok_batch.harga_beli,
    tb_stok_batch.tanggal_masuk,
    tb_barang_msk.msk_faktur
FROM tb_barang 
LEFT JOIN tb_kategori ON tb_barang.kategori_id = tb_kategori.kategori_id
LEFT JOIN tb_stok_batch ON tb_barang.barang_id = tb_stok_batch.barang_id
LEFT JOIN tb_barang_msk ON tb_stok_batch.msk_id = tb_barang_msk.msk_id
WHERE tb_stok_batch.jumlah_tersisa > 0
ORDER BY tb_barang.barang_nama ASC, tb_stok_batch.tanggal_masuk ASC";

$result = $koneksi->query($query);
$unpriced_items = getUnpricedItems($koneksi);
?>

<style>
    .batch-group:nth-child(odd) {
        background-color: #f8f9fa;
    }
    .batch-group:nth-child(even) {
        background-color: #ffffff;
    }
    .batch-header {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .table-hover .batch-group:hover {
        background-color: #f5f5f5;
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Data Barang</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-tambah">
                + Pilih Barang
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php displayAlert(); ?>
        <p class="text-danger"><i>* Data diurutkan menggunakan metode FIFO (First In First Out) berdasarkan tanggal masuk barang yang paling awal</i></p>
        <table id="example1" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="10">No</th>
                    <th>Batch ID</th>
                    <th>Faktur</th>
                    <th>Nama Kategori</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok Batch</th>
                    <th>Tanggal Masuk</th>
                    <th width="110">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $current_barang = '';
                $total_stok = 0;
                
                while ($pecah = $result->fetch_object()) {
                    $new_barang = ($current_barang !== $pecah->barang_nama);
                    if ($new_barang && $current_barang !== '') {
                        // Print total row for previous group
                        echo "<tr class='batch-header'>";
                        echo "<td colspan='8'><strong>Total Stok {$current_barang}</strong></td>";
                        echo "<td><strong>{$total_stok}</strong></td>";
                        echo "<td colspan='4'></td>";
                        echo "</tr>";
                        $total_stok = 0;
                    }
                    
                    $current_barang = $pecah->barang_nama;
                    $total_stok += $pecah->jumlah_tersisa;
                    ?>
                    <tr class="batch-group">
                        <td><?php echo $no++ ?></td>
                        <td><?php echo $pecah->batch_id ?></td>
                        <td><?php echo htmlspecialchars($pecah->msk_faktur) ?></td>
                        <td><?php echo htmlspecialchars($pecah->kategori_nama ?: 'Belum diatur') ?></td>
                        <td><?php echo htmlspecialchars($pecah->barang_kode ?: 'Belum diatur') ?></td>
                        <td><?php echo htmlspecialchars($pecah->barang_nama) ?></td>
                        <td><?php echo rupiah($pecah->harga_beli) ?></td>
                        <td><?php echo $pecah->barang_jual ? rupiah($pecah->barang_jual) : 'Belum diatur' ?></td>
                        <td><?php echo $pecah->jumlah_tersisa ?></td>
                        <td><?php echo tgl_indo($pecah->tanggal_masuk) ?></td>
                        <td>
                            <a href="index.php?page=aksi_barang/barang_edit&id=<?php echo $pecah->barang_id ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="index.php?page=aksi_barang/barang_hapus&id=<?php echo $pecah->barang_id ?>" class="btn btn-danger btn-sm delete-confirm">Hapus</a>
                        </td>
                    </tr>
                <?php 
                }
                // Print total for last group
                if ($current_barang !== '') {
                    echo "<tr class='batch-header'>";
                    echo "<td colspan='8'><strong>Total Stok {$current_barang}</strong></td>";
                    echo "<td><strong>{$total_stok}</strong></td>";
                    echo "<td colspan='4'></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modal-tambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Tambah Data Barang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <select name="barang_id" class="form-control select2" required>
    <option value="">Pilih Barang</option>
    <?php foreach ($unpriced_items as $item): ?>
        <option value="<?php echo $item['barang_id']; ?>">
            <?php echo htmlspecialchars($item['barang_nama']) . " (Total Stok: " . ($item['barang_stok'] ?: '0') . ")"; ?>
        </option>
    <?php endforeach; ?>
</select>
                    </div>
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text" name="kode" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input type="number" name="jual" class="form-control" step="0.01" required>
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
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
        "order": [[5, 'asc'], [9, 'asc']], // Sort by nama barang then tanggal
        "rowGroup": {
            dataSrc: 5 // Group by nama barang
        }
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

