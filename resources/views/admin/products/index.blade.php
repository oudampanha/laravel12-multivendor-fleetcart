@extends('admin.layouts.master_layout')

@section('pageTitle', 'Products Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Products Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-2">
            <select class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control" id="vendorStatusFilter">
              <option value="">All Vendor Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control" id="stockFilter">
              <option value="">All Stock</option>
              <option value="in_stock">In Stock</option>
              <option value="out_of_stock">Out of Stock</option>
            </select>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="productsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Vendor</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Vendor Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($products as $product)
              <tr>
                <td>{{ $product->id }}</td>
                <td>
                  <div class="d-flex align-items-center">
                    <img src="{{ $product->featured_image ?? '/assets/backend/images/product-placeholder.png' }}" 
                         alt="{{ $product->name ?? 'Product' }}" 
                         class="rounded mr-2" width="50" height="50">
                    <div>
                      <strong>{{ $product->name ?? 'Untitled Product' }}</strong><br>
                      <small class="text-muted">{{ $product->sku }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  @if($product->vendor)
                    <a href="{{ route('admin.vendors.show', $product->vendor->id) }}">
                      {{ $product->vendor->store_slug }}
                    </a>
                  @else
                    <span class="badge badge-secondary">Admin</span>
                  @endif
                </td>
                <td>
                  @if($product->special_price)
                    <span class="text-danger">${{ number_format($product->special_price, 2) }}</span><br>
                    <small class="text-muted"><s>${{ number_format($product->price, 2) }}</s></small>
                  @else
                    ${{ number_format($product->price, 2) }}
                  @endif
                </td>
                <td>
                  @if($product->manage_stock)
                    @if($product->in_stock && $product->qty > 0)
                      <span class="badge badge-success">{{ $product->qty }} in stock</span>
                    @else
                      <span class="badge badge-danger">Out of stock</span>
                    @endif
                  @else
                    <span class="badge badge-info">Not managed</span>
                  @endif
                </td>
                <td>
                  @if($product->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  @switch($product->vendor_status)
                    @case('pending')
                      <span class="badge badge-warning">Pending</span>
                      @break
                    @case('approved')
                      <span class="badge badge-success">Approved</span>
                      @break
                    @case('rejected')
                      <span class="badge badge-danger">Rejected</span>
                      @break
                    @default
                      <span class="badge badge-secondary">N/A</span>
                  @endswitch
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    @if($product->vendor_status === 'pending')
                    <div class="btn-group">
                      <form action="{{ route('admin.products.approve', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this product?')">
                          <i class="fas fa-check"></i>
                        </button>
                      </form>
                      <form action="{{ route('admin.products.reject', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Reject this product?')">
                          <i class="fas fa-times"></i>
                        </button>
                      </form>
                    </div>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($products, 'links'))
          <div class="d-flex justify-content-center">
            {{ $products->links() }}
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
  const table = $('#productsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
  
  // Filters
  $('#statusFilter').on('change', function() {
    const status = $(this).val();
    if (status === 'active') {
      table.column(5).search('Active').draw();
    } else if (status === 'inactive') {
      table.column(5).search('Inactive').draw();
    } else {
      table.column(5).search('').draw();
    }
  });
  
  $('#vendorStatusFilter').on('change', function() {
    const status = $(this).val();
    table.column(6).search(status).draw();
  });
  
  $('#stockFilter').on('change', function() {
    const stock = $(this).val();
    if (stock === 'in_stock') {
      table.column(4).search('in stock').draw();
    } else if (stock === 'out_of_stock') {
      table.column(4).search('Out of stock').draw();
    } else {
      table.column(4).search('').draw();
    }
  });
});
</script>
@endpush