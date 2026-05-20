@extends('admin.layouts.master_layout')

@section('pageTitle', 'Purchase Order')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">PO {{ $purchaseOrder->code }}
        <span class="badge badge-secondary ms-2">{{ $purchaseOrder->status }}</span>
      </h4>
      <div>
        @if($purchaseOrder->status === \App\Models\PurchaseOrder::STATUS_DRAFT)
          <form action="{{ route('admin.purchase-orders.send', $purchaseOrder) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-info"><i class="fas fa-paper-plane"></i> Send</button>
          </form>
        @endif
        @if(in_array($purchaseOrder->status, [\App\Models\PurchaseOrder::STATUS_DRAFT, \App\Models\PurchaseOrder::STATUS_SENT]))
          <form action="{{ route('admin.purchase-orders.approve', $purchaseOrder) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success"><i class="fas fa-check"></i> Approve</button>
          </form>
        @endif
        @if($purchaseOrder->isReceivable())
          <a href="{{ route('admin.goods-receipts.create', ['purchase_order_id' => $purchaseOrder->id]) }}" class="btn btn-primary"><i class="fas fa-truck-loading"></i> Receive Stock</a>
        @endif
        @if($purchaseOrder->isEditable())
          <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
        @endif
        @if($purchaseOrder->status !== \App\Models\PurchaseOrder::STATUS_RECEIVED && $purchaseOrder->status !== \App\Models\PurchaseOrder::STATUS_CANCELLED)
          <form action="{{ route('admin.purchase-orders.cancel', $purchaseOrder) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-danger" onclick="return confirm('Cancel this PO?')"><i class="fas fa-times"></i> Cancel</button>
          </form>
        @endif
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Supplier</dt><dd class="col-sm-9">{{ optional($purchaseOrder->supplier)->name }}</dd>
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($purchaseOrder->warehouse)->name }}</dd>
        <dt class="col-sm-3">Order Date</dt><dd class="col-sm-9">{{ $purchaseOrder->order_date->format('Y-m-d') }}</dd>
        <dt class="col-sm-3">Expected Date</dt><dd class="col-sm-9">{{ optional($purchaseOrder->expected_date)->format('Y-m-d') ?: '-' }}</dd>
        <dt class="col-sm-3">Currency</dt><dd class="col-sm-9">{{ $purchaseOrder->currency_code }} @ {{ number_format((float) $purchaseOrder->exchange_rate, 4) }}</dd>
        @if($purchaseOrder->approved_at)
          <dt class="col-sm-3">Approved</dt><dd class="col-sm-9">{{ optional($purchaseOrder->approver)->name ?? '-' }} on {{ $purchaseOrder->approved_at->format('Y-m-d H:i') }}</dd>
        @endif
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $purchaseOrder->notes ?: '-' }}</dd>
        <dt class="col-sm-3">Terms</dt><dd class="col-sm-9">{{ $purchaseOrder->terms ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card mb-3">
    <div class="card-header"><h5>Items</h5></div>
    <div class="card-body table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Product</th><th>Variant</th>
            <th class="text-end">Ordered</th><th class="text-end">Received</th><th class="text-end">Remaining</th>
            <th class="text-end">Unit Cost</th><th class="text-end">Tax %</th><th class="text-end">Discount</th><th class="text-end">Line Total</th>
          </tr>
        </thead>
        <tbody>
          @forelse($purchaseOrder->items as $item)
            <tr>
              <td>{{ optional($item->product)->name ?? '#'.$item->product_id }}</td>
              <td>{{ optional($item->variant)->id ? '#'.$item->variant->id : '-' }}</td>
              <td class="text-end">{{ $item->quantity_ordered }}</td>
              <td class="text-end">{{ $item->quantity_received }}</td>
              <td class="text-end">{{ $item->remaining_quantity }}</td>
              <td class="text-end">{{ number_format((float) $item->unit_cost, 4) }}</td>
              <td class="text-end">{{ number_format((float) $item->tax_rate, 2) }}</td>
              <td class="text-end">{{ number_format((float) $item->discount, 4) }}</td>
              <td class="text-end">{{ number_format((float) $item->line_total, 4) }}</td>
            </tr>
          @empty
            <tr><td colspan="9" class="text-center text-muted">No items.</td></tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr><th colspan="8" class="text-end">Subtotal</th><th class="text-end">{{ number_format((float) $purchaseOrder->subtotal, 4) }}</th></tr>
          <tr><th colspan="8" class="text-end">Tax</th><th class="text-end">{{ number_format((float) $purchaseOrder->tax_amount, 4) }}</th></tr>
          <tr><th colspan="8" class="text-end">Shipping</th><th class="text-end">{{ number_format((float) $purchaseOrder->shipping_amount, 4) }}</th></tr>
          <tr><th colspan="8" class="text-end">Discount</th><th class="text-end">-{{ number_format((float) $purchaseOrder->discount_amount, 4) }}</th></tr>
          <tr><th colspan="8" class="text-end">Total</th><th class="text-end">{{ number_format((float) $purchaseOrder->total_amount, 4) }} {{ $purchaseOrder->currency_code }}</th></tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Goods Receipts</h5></div>
    <div class="card-body table-responsive">
      <table class="table table-sm">
        <thead><tr><th>Code</th><th>Date</th><th>Status</th><th></th></tr></thead>
        <tbody>
          @forelse($purchaseOrder->goodsReceipts as $g)
            <tr>
              <td><code>{{ $g->code }}</code></td>
              <td>{{ $g->receipt_date->format('Y-m-d') }}</td>
              <td><span class="badge badge-secondary">{{ $g->status }}</span></td>
              <td><a href="{{ route('admin.goods-receipts.show', $g) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No goods receipts yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
