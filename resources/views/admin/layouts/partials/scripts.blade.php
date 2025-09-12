  <!-- jQuery -->
  <script src="{{ assetUrl() }}assets/backend/lib/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 JS -->
  <script src="{{ assetUrl() }}assets/backend/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- MetisMenu JS -->
  <script src="{{ assetUrl() }}assets/backend/lib/metismenu/js/metisMenu.min.js"></script>
  <!-- Custom Admin Scripts -->
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>

  <script src="{{ assetUrl() }}assets/backend/lib/select2/js/select2.min.js"></script>

  <script src="{{ assetUrl() }}assets/backend/lib/sweetalert2/sweetalert2.min.js"></script>

  <script src="{{ assetUrl() }}assets/backend/js/admin-sidebar.js"></script>
  <script src="{{ assetUrl() }}assets/backend/js/MediaManager.js"></script>

  @stack('scripts')

  <!-- PHP Flasher Scripts and Styles -->
  @flasher_render
