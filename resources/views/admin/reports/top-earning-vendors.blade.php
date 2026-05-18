@extends('admin.layouts.master_layout')

@section('pageTitle', 'Top Earning Vendors')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Top Earning Vendors</h4>
        <div class="card-tools">
          <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <p class="text-muted">Detailed report data will appear below.</p>
        <pre>{{ json_encode(compact(...array_keys(get_defined_vars())), JSON_PRETTY_PRINT) }}</pre>
      </div>
    </div>
  </div>
</div>
@endsection
