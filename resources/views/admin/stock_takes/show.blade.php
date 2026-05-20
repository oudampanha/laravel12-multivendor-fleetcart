@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Take')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Stock Take {{ $stockTake->code }}
        <span class="badge badge-secondary ms-2">{{ $stockTake->status }}</span>
      </h4>
      <div>
        @if(!$stockTake->isCompleted())
          <a href="{{ route('admin.stock-takes.edit', $stockTake) }}" class="btn btn-warning"><i class="fas fa-clipboard-list"></i> Enter Counts</a>
          <form action="{{ route('admin.stock-takes.complete', $stockTake) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success" onclick="return confirm('Complete this count? Any differences will be posted as stock adjustments.')"><i class="fas fa-check"></i> Complete</button>
          </form>
          <form action="{{ route('admin.stock-takes.cancel', $stockTake) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-danger"><i class="fas fa-times"></i> Cancel</button>
          </form>
        @endif
        <a href="{{ route('admin.stock-takes.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($stockTake->warehouse)->name }}</dd>
        <dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $stockTake->count_date->format('Y-m-d') }}</dd>
        @if($stockTake->isCompleted())
          <dt class="col-sm-3">Completed</dt><dd class="col-sm-9">{{ optional($stockTake->completer)->name ?? '-' }} on {{ optional($stockTake->completed_at)->format('Y-m-d H:i') }}</dd>
        @endif
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $stockTake->notes ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Items</h5></div>
    <div class="card-body table-responsive">
      <table class="table">
        <thead><tr><th>Product</th><th>Variant</th><th class="text-end">Expected</th><th class="text-end">Counted</th><th class="text-end">Difference</th><th>Notes</th></tr></thead>
        <tbody>
          @forelse($stockTake->items as $item)
            <tr>
              <td>{{ optional($item->product)->name ?? '#'.$item->product_id }}</td>
              <td>{{ optional($item->variant)->id ? '#'.$item->variant->id : '-' }}</td>
              <td class="text-end">{{ $item->expected_quantity }}</td>
              <td class="text-end">{{ $item->counted_quantity ?? '-' }}</td>
              <td class="text-end {{ ($item->difference ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ $item->difference ?? '-' }}</td>
              <td>{{ $item->notes }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No items.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
