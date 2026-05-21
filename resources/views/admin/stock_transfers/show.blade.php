@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Transfer')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Transfer {{ $stockTransfer->code }}
        <span class="badge badge-secondary ms-2">{{ $stockTransfer->status }}</span>
      </h4>
      <div>
        @if($stockTransfer->isDraft())
          <form action="{{ route('admin.stock-transfers.ship', $stockTransfer) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success" onclick="return confirm('Ship this transfer? Stock will be deducted from the source warehouse.')"><i class="fas fa-truck"></i> Ship</button>
          </form>
          <a href="{{ route('admin.stock-transfers.edit', $stockTransfer) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
          <form action="{{ route('admin.stock-transfers.cancel', $stockTransfer) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-outline-danger"><i class="fas fa-times"></i> Cancel</button>
          </form>
        @endif
        <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">From</dt><dd class="col-sm-9">{{ optional($stockTransfer->fromWarehouse)->name }}</dd>
        <dt class="col-sm-3">To</dt><dd class="col-sm-9">{{ optional($stockTransfer->toWarehouse)->name }}</dd>
        <dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $stockTransfer->transfer_date->format('Y-m-d') }}</dd>
        <dt class="col-sm-3">Shipped</dt><dd class="col-sm-9">{{ optional($stockTransfer->shipped_at)->format('Y-m-d H:i') ?: '-' }} {{ $stockTransfer->shipper ? 'by '.$stockTransfer->shipper->name : '' }}</dd>
        <dt class="col-sm-3">Received</dt><dd class="col-sm-9">{{ optional($stockTransfer->received_at)->format('Y-m-d H:i') ?: '-' }} {{ $stockTransfer->receiver ? 'by '.$stockTransfer->receiver->name : '' }}</dd>
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $stockTransfer->notes ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Items</h5></div>
    <div class="card-body table-responsive">
      <form method="POST" action="{{ route('admin.stock-transfers.receive', $stockTransfer) }}" id="receiveForm">
        @csrf
        <table class="table">
          <thead><tr><th>Product</th><th>Variant</th><th class="text-end">Qty Sent</th><th class="text-end">Qty Received</th><th class="text-end">Unit Cost</th></tr></thead>
          <tbody>
            @forelse($stockTransfer->items as $item)
              <tr>
                <td>{{ optional($item->product)->name ?? '#'.$item->product_id }}</td>
                <td>{{ optional($item->variant)->id ? '#'.$item->variant->id : '-' }}</td>
                <td class="text-end">{{ $item->quantity_sent }}</td>
                <td class="text-end" style="max-width: 150px;">
                  @if($stockTransfer->isInTransit())
                    <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                    <input type="number" name="items[{{ $loop->index }}][quantity_received]" value="{{ $item->quantity_received ?: $item->quantity_sent }}" class="form-control text-end" min="0" max="{{ $item->quantity_sent }}">
                  @else
                    {{ $item->quantity_received }}
                  @endif
                </td>
                <td class="text-end">{{ number_format((float) $item->unit_cost, 4) }}</td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted">No items.</td></tr>
            @endforelse
          </tbody>
        </table>
        @if($stockTransfer->isInTransit())
          <button class="btn btn-success" onclick="return confirm('Receive this transfer? Stock will be added to the destination warehouse.')"><i class="fas fa-check"></i> Receive Transfer</button>
        @endif
      </form>
    </div>
  </div>
@endsection
