@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Adjustment')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Adjustment {{ $stockAdjustment->code }}
        <span class="badge badge-secondary ms-2">{{ $stockAdjustment->status }}</span>
      </h4>
      <div>
        @if($stockAdjustment->isDraft())
          <form action="{{ route('admin.stock-adjustments.post', $stockAdjustment) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success" onclick="return confirm('Post this adjustment to stock? This cannot be undone.')"><i class="fas fa-check"></i> Post to Stock</button>
          </form>
          <a href="{{ route('admin.stock-adjustments.edit', $stockAdjustment) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
          <form action="{{ route('admin.stock-adjustments.cancel', $stockAdjustment) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-danger"><i class="fas fa-times"></i> Cancel</button>
          </form>
        @endif
        <a href="{{ route('admin.stock-adjustments.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($stockAdjustment->warehouse)->name }}</dd>
        <dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $stockAdjustment->adjustment_date->format('Y-m-d') }}</dd>
        <dt class="col-sm-3">Reason</dt><dd class="col-sm-9">{{ ucfirst($stockAdjustment->reason) }}</dd>
        <dt class="col-sm-3">Created By</dt><dd class="col-sm-9">{{ optional($stockAdjustment->creator)->name ?? '-' }}</dd>
        @if($stockAdjustment->isPosted())
          <dt class="col-sm-3">Posted By</dt><dd class="col-sm-9">{{ optional($stockAdjustment->poster)->name ?? '-' }} on {{ optional($stockAdjustment->posted_at)->format('Y-m-d H:i') }}</dd>
        @endif
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $stockAdjustment->notes ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Items</h5></div>
    <div class="card-body table-responsive">
      <table class="table">
        <thead>
          <tr><th>Product</th><th>Variant</th><th class="text-end">System Qty</th><th class="text-end">Actual Qty</th><th class="text-end">Difference</th><th class="text-end">Unit Cost</th><th>Notes</th></tr>
        </thead>
        <tbody>
          @forelse($stockAdjustment->items as $item)
            <tr>
              <td>{{ optional($item->product)->name ?? '#'.$item->product_id }}</td>
              <td>{{ optional($item->variant)->id ? '#'.$item->variant->id : '-' }}</td>
              <td class="text-end">{{ $item->system_quantity }}</td>
              <td class="text-end">{{ $item->actual_quantity }}</td>
              <td class="text-end {{ $item->difference >= 0 ? 'text-success' : 'text-danger' }}">{{ $item->difference }}</td>
              <td class="text-end">{{ number_format((float) $item->unit_cost, 4) }}</td>
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
