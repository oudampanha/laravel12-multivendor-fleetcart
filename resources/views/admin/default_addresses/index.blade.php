@extends('admin.layouts.master_layout')

@section('pageTitle', 'Default Addresses Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Default Addresses Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.default_addresses.create'))
<a href="{{ route('admin.default_addresses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Default Address
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
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Postal Code</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->user ?? 'N/A' }}</td>
                <td>{{ $item->type ?? 'N/A' }}</td>
                <td>{{ $item->address ?? 'N/A' }}</td>
                <td>{{ $item->city ?? 'N/A' }}</td>
                <td>{{ $item->state ?? 'N/A' }}</td>
                <td>{{ $item->postal_code ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    @if (Route::has('admin.default_addresses.show'))
<a href="{{ route('admin.default_addresses.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
@endif
                    @if (Route::has('admin.default_addresses.edit'))
<a href="{{ route('admin.default_addresses.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                    <form action="{{ route('admin.default-addresses.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">No records found</td>
              </tr>
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