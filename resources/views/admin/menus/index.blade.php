@extends('admin.layouts.master_layout')

@section('pageTitle', 'Menus Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Menus Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Menu
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="menusTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Items Count</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($menus ?? [] as $menu)
              <tr>
                <td>{{ $menu->id }}</td>
                <td>
                  <strong>{{ $menu->name }}</strong><br>
                  <small class="text-muted">{{ $menu->description ?: 'No description' }}</small>
                </td>
                <td>
                  @if($menu->location)
                    <span class="badge badge-info">{{ $menu->location }}</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-primary">{{ $menu->menu_items_count ?? 0 }}</span>
                </td>
                <td>
                  @if($menu->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.menus.show', $menu->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.menus.edit', $menu->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this menu?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">No menus found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(isset($menus) && method_exists($menus, 'links'))
          <div class="d-flex justify-content-center">
            {{ $menus->links() }}
          </div>
        @endif
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
  $('#menusTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush