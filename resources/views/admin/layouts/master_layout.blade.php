@include('admin.layouts.partials.header')

<body>
  <!-- Sidebar -->
  @include('admin.layouts.partials.left_sidebar')

  <!-- Main Content -->
  <div class="main-content" id="mainContent">
    <!-- Header -->
    @include('admin.layouts.partials.top_navbar')
    <!-- Content Area -->
    <div class="content-area">
      <!-- Breadcrumb -->
      @include('admin.layouts.partials.breadcrumb')

      <div class="container-fluid">
        @yield('content')
      </div>

    </div>

    <!-- Footer -->
    <footer class="footer">
      Copyright@2025 by <strong>Samnang Tech</strong>
    </footer>
  </div>

  @include('admin.layouts.partials.scripts')
</body>

</html>
