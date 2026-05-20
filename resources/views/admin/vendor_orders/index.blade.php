@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Orders Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Vendor Orders Management</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="vendorOrdersTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Order ID</th>
                <th>Sub Total</th>
                <th>Commission</th>
                <th>Vendor Amount</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vendorOrders as $vendorOrder)
              <tr>
                <td>{{ $vendorOrder->id }}</td>
                <td>
                  <a href="{{ route('admin.vendors.show', $vendorOrder->vendor_id) }}" class="text-primary">
                    {{ $vendorOrder->vendor->user->first_name ?? 'N/A' }} {{ $vendorOrder->vendor->user->last_name ?? '' }}
                  </a>
                </td>
                <td>
                  <a href="{{ route('admin.orders.show', $vendorOrder->order_id) }}" class="text-primary">
                    #{{ $vendorOrder->order_id }}
                  </a>
                </td>
                <td>${{ number_format($vendorOrder->sub_total, 2) }}</td>
                <td>${{ number_format($vendorOrder->commission_amount, 2) }}</td>
                <td>${{ number_format($vendorOrder->vendor_amount, 2) }}</td>
                <td>
                  @switch($vendorOrder->status)
                    @case('pending')
                      <span class="badge badge-warning">Pending</span>
                      @break
                    @case('processing')
                      <span class="badge badge-info">Processing</span>
                      @break
                    @case('shipped')
                      <span class="badge badge-primary">Shipped</span>
                      @break
                    @case('delivered')
                      <span class="badge badge-success">Delivered</span>
                      @break
                    @case('canceled')
                      <span class="badge badge-danger">Canceled</span>
                      @break
                    @case('refunded')
                      <span class="badge badge-secondary">Refunded</span>
                      @break
                  @endswitch
                </td>
                <td>{{ $vendorOrder->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.vendor-orders.show', $vendorOrder->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    @if (Route::has('admin.vendor_orders.edit'))
<a href="{{ route('admin.vendor_orders.edit', $vendorOrder->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
@endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center">No vendor orders found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($vendorOrders, 'links'))
          <div class="d-flex justify-content-center">
            {{ $vendorOrders->links() }}
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
  $('#vendorOrdersTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush