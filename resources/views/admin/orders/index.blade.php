@extends('admin.layouts.master_layout')

@section('pageTitle', 'Orders Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Orders Management</h4>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-2">
            <select class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="processing">Processing</option>
              <option value="shipped">Shipped</option>
              <option value="delivered">Delivered</option>
              <option value="canceled">Canceled</option>
              <option value="refunded">Refunded</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-control" id="paymentFilter">
              <option value="">All Payment Methods</option>
              <option value="credit_card">Credit Card</option>
              <option value="paypal">PayPal</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cash_on_delivery">Cash on Delivery</option>
            </select>
          </div>
          <div class="col-md-3">
            <div class="input-group">
              <input type="date" class="form-control" id="dateFrom" placeholder="From Date">
              <div class="input-group-append">
                <input type="date" class="form-control" id="dateTo" placeholder="To Date">
              </div>
            </div>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="ordersTable">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $order)
              <tr>
                <td>
                  <strong>#{{ $order->id }}</strong>
                  @if($order->tracking_reference)
                    <br><small class="text-muted">{{ $order->tracking_reference }}</small>
                  @endif
                </td>
                <td>
                  <strong>{{ $order->customer_first_name }} {{ $order->customer_last_name }}</strong><br>
                  <small class="text-muted">{{ $order->customer_email }}</small>
                  @if($order->customer_phone)
                    <br><small class="text-muted">{{ $order->customer_phone }}</small>
                  @endif
                </td>
                <td>
                  <span class="badge badge-info">{{ $order->order_products_count ?? 0 }} items</span>
                  @if($order->vendor_orders_count ?? 0 > 1)
                    <br><small class="text-muted">{{ $order->vendor_orders_count }} vendors</small>
                  @endif
                </td>
                <td>
                  <strong>${{ number_format($order->total, 2) }}</strong>
                  @if($order->discount > 0)
                    <br><small class="text-success">-${{ number_format($order->discount, 2) }} discount</small>
                  @endif
                </td>
                <td>
                  <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                </td>
                <td>
                  @switch($order->status)
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
                      <span class="badge badge-dark">Refunded</span>
                      @break
                    @default
                      <span class="badge badge-secondary">{{ ucfirst($order->status) }}</span>
                  @endswitch
                </td>
                <td>
                  {{ $order->created_at->format('M d, Y') }}<br>
                  <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown">
                        <i class="fas fa-edit"></i>
                      </button>
                      <div class="dropdown-menu">
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                          @csrf
                          <input type="hidden" name="status" value="processing">
                          <button type="submit" class="dropdown-item">Mark as Processing</button>
                        </form>
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                          @csrf
                          <input type="hidden" name="status" value="shipped">
                          <button type="submit" class="dropdown-item">Mark as Shipped</button>
                        </form>
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                          @csrf
                          <input type="hidden" name="status" value="delivered">
                          <button type="submit" class="dropdown-item">Mark as Delivered</button>
                        </form>
                        <div class="dropdown-divider"></div>
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                          @csrf
                          <input type="hidden" name="status" value="canceled">
                          <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Cancel this order?')">Cancel Order</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($orders, 'links'))
          <div class="d-flex justify-content-center">
            {{ $orders->links() }}
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
  const table = $('#ordersTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
  
  // Status filter
  $('#statusFilter').on('change', function() {
    const status = $(this).val();
    table.column(5).search(status).draw();
  });
  
  // Payment method filter
  $('#paymentFilter').on('change', function() {
    const payment = $(this).val();
    table.column(4).search(payment.replace('_', ' ')).draw();
  });
  
  // Date range filter
  $('#dateFrom, #dateTo').on('change', function() {
    table.draw();
  });
});
</script>
@endpush