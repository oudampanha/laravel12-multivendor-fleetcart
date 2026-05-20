@extends('admin.layouts.master_layout')

@section('pageTitle', 'Inventory Dashboard')

@section('content')
  <div class="row">
    <div class="col-md-3">
      <div class="card text-white bg-primary mb-3">
        <div class="card-body">
          <h6 class="card-title">Warehouses</h6>
          <h3>{{ $activeWarehouses }} <small>/ {{ $totalWarehouses }}</small></h3>
          <small>active / total</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success mb-3">
        <div class="card-body">
          <h6 class="card-title">Stocked SKUs</h6>
          <h3>{{ number_format($totalSkus) }}</h3>
          <small>distinct products with stock</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info mb-3">
        <div class="card-body">
          <h6 class="card-title">On-Hand Quantity</h6>
          <h3>{{ number_format($totalQuantity) }}</h3>
          <small>reserved: {{ number_format($totalReserved) }}</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning mb-3">
        <div class="card-body">
          <h6 class="card-title">Stock Value</h6>
          <h3>{{ number_format($stockValue, 2) }}</h3>
          <small>at average cost</small>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="text-muted">Open Purchase Orders</h6>
          <h3>{{ $openPos }}</h3>
          <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-sm btn-outline-primary">View</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="text-muted">Pending Goods Receipts</h6>
          <h3>{{ $pendingReceipts }}</h3>
          <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-sm btn-outline-primary">View</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="text-muted">Pending Adjustments</h6>
          <h3>{{ $pendingAdjustments }}</h3>
          <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-sm btn-outline-primary">View</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="text-muted">Transfers In Transit</h6>
          <h3>{{ $inTransitTransfers }}</h3>
          <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-sm btn-outline-primary">View</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header"><h5>Low Stock</h5></div>
        <div class="card-body table-responsive">
          <table class="table table-sm">
            <thead>
              <tr><th>Product</th><th>Warehouse</th><th class="text-end">Qty</th><th class="text-end">Reorder</th></tr>
            </thead>
            <tbody>
              @forelse($lowStock as $row)
                <tr>
                  <td>{{ optional($row->product)->name ?? '#'.$row->product_id }}{{ $row->variant ? ' / '.$row->variant->id : '' }}</td>
                  <td>{{ optional($row->warehouse)->name }}</td>
                  <td class="text-end">{{ $row->quantity }}</td>
                  <td class="text-end">{{ $row->reorder_level }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted">No low stock items.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card mb-3">
        <div class="card-header"><h5>Out of Stock</h5></div>
        <div class="card-body table-responsive">
          <table class="table table-sm">
            <thead>
              <tr><th>Product</th><th>Warehouse</th><th class="text-end">Reorder Qty</th></tr>
            </thead>
            <tbody>
              @forelse($outOfStock as $row)
                <tr>
                  <td>{{ optional($row->product)->name ?? '#'.$row->product_id }}</td>
                  <td>{{ optional($row->warehouse)->name }}</td>
                  <td class="text-end">{{ $row->reorder_quantity }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted">All products are in stock.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h5>Recent Stock Movements</h5></div>
        <div class="card-body table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Date</th><th>Type</th><th>Product</th><th>Warehouse</th>
                <th class="text-end">Qty</th><th class="text-end">Balance</th><th>User</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentMovements as $m)
                <tr>
                  <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                  <td><span class="badge badge-secondary">{{ $m->type }}</span></td>
                  <td>{{ optional($m->product)->name ?? '#'.$m->product_id }}</td>
                  <td>{{ optional($m->warehouse)->name }}</td>
                  <td class="text-end {{ $m->quantity >= 0 ? 'text-success' : 'text-danger' }}">{{ $m->quantity }}</td>
                  <td class="text-end">{{ $m->balance_after }}</td>
                  <td>{{ optional($m->user)->name ?? '-' }}</td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-muted">No movements yet.</td></tr>
              @endforelse
            </tbody>
          </table>
          <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-sm btn-outline-primary">View all movements</a>
        </div>
      </div>
    </div>
  </div>
@endsection
