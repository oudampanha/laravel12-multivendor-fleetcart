@extends('admin.layouts.master_layout')

@section('pageTitle', 'Goods Receipt')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">GRN {{ $goodsReceipt->code }}
        <span class="badge badge-secondary ms-2">{{ $goodsReceipt->status }}</span>
      </h4>
      <div>
        @if($goodsReceipt->isDraft())
          <form action="{{ route('admin.goods-receipts.post', $goodsReceipt) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success" onclick="return confirm('Post this GRN to stock?')"><i class="fas fa-check"></i> Post to Stock</button>
          </form>
          <a href="{{ route('admin.goods-receipts.edit', $goodsReceipt) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
          <form action="{{ route('admin.goods-receipts.cancel', $goodsReceipt) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-danger"><i class="fas fa-times"></i> Cancel</button>
          </form>
        @endif
        <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Purchase Order</dt><dd class="col-sm-9">{{ $goodsReceipt->purchaseOrder ? $goodsReceipt->purchaseOrder->code : '-' }}</dd>
        <dt class="col-sm-3">Supplier</dt><dd class="col-sm-9">{{ optional($goodsReceipt->supplier)->name }}</dd>
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($goodsReceipt->warehouse)->name }}</dd>
        <dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $goodsReceipt->receipt_date->format('Y-m-d') }}</dd>
        @if($goodsReceipt->isPosted())
          <dt class="col-sm-3">Posted</dt><dd class="col-sm-9">{{ optional($goodsReceipt->poster)->name ?? '-' }} on {{ optional($goodsReceipt->posted_at)->format('Y-m-d H:i') }}</dd>
        @endif
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $goodsReceipt->notes ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Items</h5></div>
    <div class="card-body table-responsive">
      <table class="table">
        <thead><tr><th>Product</th><th>Variant</th><th class="text-end">Qty Received</th><th class="text-end">Unit Cost</th><th>Batch</th><th>Expiry</th><th>Notes</th></tr></thead>
        <tbody>
          @forelse($goodsReceipt->items as $item)
            <tr>
              <td>{{ optional($item->product)->name ?? '#'.$item->product_id }}</td>
              <td>{{ optional($item->variant)->id ? '#'.$item->variant->id : '-' }}</td>
              <td class="text-end">{{ $item->quantity_received }}</td>
              <td class="text-end">{{ number_format((float) $item->unit_cost, 4) }}</td>
              <td>{{ $item->batch_number ?: '-' }}</td>
              <td>{{ optional($item->expiry_date)->format('Y-m-d') ?: '-' }}</td>
              <td>{{ $item->notes }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No items.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
