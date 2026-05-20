@extends('admin.layouts.master_layout')

@section('pageTitle', 'Order Products Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Order Products Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.order_products.create'))
<a href="{{ route('admin.order_products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Order Product
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
                <th>Order</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->order ?? 'N/A' }}</td>
                <td>{{ $item->product ?? 'N/A' }}</td>
                <td>{{ $item->quantity ?? 'N/A' }}</td>
                <td>{{ $item->unit_price ?? 'N/A' }}</td>
                <td>{{ $item->total_price ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.order-products.show', $item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    @if (Route::has('admin.order_products.edit'))
<a href="{{ route('admin.order_products.edit', $item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                    @if (Route::has('admin.order_products.destroy'))
<form action="{{ route('admin.order_products.destroy', $item->id) }}" method="POST" class="d-inline">
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