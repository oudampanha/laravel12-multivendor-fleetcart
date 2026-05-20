@extends('admin.layouts.master_layout')

@section('pageTitle', 'Entity Media Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Entity Media Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.entity_media.create'))
<a href="{{ route('admin.entity_media.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Entity Media
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
                <th>Entity Type</th>
                <th>Entity Id</th>
                <th>Media</th>
                <th>Zone</th>
                <th>Sort Order</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->entity_type ?? 'N/A' }}</td>
                <td>{{ $item->entity_id ?? 'N/A' }}</td>
                <td>{{ $item->media ?? 'N/A' }}</td>
                <td>{{ $item->zone ?? 'N/A' }}</td>
                <td>{{ $item->sort_order ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    @if (Route::has('admin.entity_media.show'))
<a href="{{ route('admin.entity_media.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
@endif
                    @if (Route::has('admin.entity_media.edit'))
<a href="{{ route('admin.entity_media.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                    <form action="{{ route('admin.entity-media.destroy', $item->id) }}" method="POST" class="d-inline">
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
                <td colspan="7" class="text-center">No records found</td>
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