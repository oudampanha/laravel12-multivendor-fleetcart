@extends('admin.layouts.master_layout')

@section('pageTitle', 'Reports Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Reports Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.reports.create'))
<a href="{{ route('admin.reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Report
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="dataTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Data</th>
                <th>Generated At</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->name ?? 'N/A' }}</td>
                <td>{{ $item->type ?? 'N/A' }}</td>
                <td>{{ $item->data ?? 'N/A' }}</td>
                <td>{{ $item->generated_at ?? 'N/A' }}</td>
                <td>{{ $item->status ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    @if (Route::has('admin.reports.show'))
<a href="{{ route('admin.reports.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
@endif
                    @if (Route::has('admin.reports.edit'))
<a href="{{ route('admin.reports.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                    @if (Route::has('admin.reports.destroy'))
<form action="{{ route('admin.reports.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
@endif
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
  $('#dataTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush