@extends('admin.layouts.master_layout')

@section('pageTitle', 'Related Products Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Related Products Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.related_products.create'))
<a href="{{ route('admin.related_products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Related Product
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
                <th>Product</th>
                <th>Related Product</th>
                <th>Sort Order</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->product ?? 'N/A' }}</td>
                <td>{{ $item->related_product ?? 'N/A' }}</td>
                <td>{{ $item->sort_order ?? 'N/A' }}</td>
                <td>{{ $item->status ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    @if (Route::has('admin.related_products.show'))
<a href="{{ route('admin.related_products.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
@endif
                    @if (Route::has('admin.related_products.edit'))
<a href="{{ route('admin.related_products.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                    <form action="{{ route('admin.related-products.destroy', $item->id) }}" method="POST" class="d-inline">
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
                <td colspan="6" class="text-center">No records found</td>
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