<!-- jQuery -->
<script src="assets/adminlte/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="assets/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="assets/adminlte/dist/js/adminlte.js"></script>
<!-- DataTables -->
<script src="assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- ChartJS -->
<script src="assets/adminlte/plugins/chart.js/Chart.min.js"></script>

<!-- Page specific script -->
<script>
  $(function () {
    // Enable sidebar toggle
    $('[data-widget="pushmenu"]').on('click', function() {
      $('body').toggleClass('sidebar-collapse');
    });

    // Initialize DataTables
    $('.dataTable').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>

