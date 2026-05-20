@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Notifications Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Vendor Notifications Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.vendor-notifications.create'))
<a href="{{ route('admin.vendor-notifications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="vendor_notificationsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vendor_notifications ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>
                  <strong>{{ $item->name ?? $item->title ?? 'N/A' }}</strong>
                </td>
                <td>
                  @if($item->is_active ?? $item->status ?? true)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.vendor-notifications.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.vendor-notifications.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.vendor-notifications.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">No records found</td>
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
  $('#vendor_notificationsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush
