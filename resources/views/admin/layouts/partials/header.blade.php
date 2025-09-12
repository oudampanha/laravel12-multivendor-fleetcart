<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('pageTitle', 'Admin Pro')</title>
  <!-- Bootstrap 4 CSS -->
  <link href="{{ assetUrl() }}assets/backend/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link href="{{ assetUrl() }}assets/backend/fontawesome/css/all.min.css" rel="stylesheet">
  <!-- MetisMenu CSS -->
  <link href="{{ assetUrl() }}assets/backend/lib/metismenu/css/metisMenu.min.css" rel="stylesheet">

  <!-- SweetAlert2 CSS -->
  <link href="{{ assetUrl() }}assets/backend/lib/sweetalert2/sweetalert2.min.css" rel="stylesheet">
  <link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="{{ assetUrl() }}assets/backend/lib/select2/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
    rel="stylesheet">
  <!-- Custom Admin Styles -->
  <link href="{{ assetUrl() }}assets/backend/css/admin-styles.css" rel="stylesheet">
  <link href="{{ assetUrl() }}assets/backend/css/media-selector.css" rel="stylesheet">
  @stack('styles')
</head>
