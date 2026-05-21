@extends('admin.layouts.master_layout')

@section('pageTitle', 'Throttles Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Throttles Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.throttles.create'))
<a href="{{ route('admin.throttles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Throttle
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
                <th>User</th>
                <th>Type</th>
                <th>Ip</th>
                <th>Created At</th>
                <th>Last Activity At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->user ?? 'N/A' }}</td>
                <td>{{ $item->type ?? 'N/A' }}</td>
                <td>{{ $item->ip ?? 'N/A' }}</td>
                <td>{{ $item->created_at ?? 'N/A' }}</td>
                <td>{{ $item->last_activity_at ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    @if (Route::has('admin.throttles.show'))
<a href="{{ route('admin.throttles.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
@endif
                    @if (Route::has('admin.throttles.edit'))
<a href="{{ route('admin.throttles.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                    @if (Route::has('admin.throttles.destroy'))
<form action="{{ route('admin.throttles.destroy', $item->id) }}" method="POST" class="d-inline">
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