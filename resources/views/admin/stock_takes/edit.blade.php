@extends('admin.layouts.master_layout')

@section('pageTitle', 'Enter Counts')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">Stock Take: {{ $stockTake->code }} — Enter Counts</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <p><strong>Warehouse:</strong> {{ optional($stockTake->warehouse)->name }} · <strong>Date:</strong> {{ $stockTake->count_date->format('Y-m-d') }}</p>
      <form method="POST" action="{{ route('admin.stock-takes.update', $stockTake) }}">
        @csrf @method('PUT')
        <div class="table-responsive">
          <table class="table table-sm">
            <thead><tr><th>Product</th><th>Variant</th><th class="text-end">Expected</th><th class="text-end" style="width:160px">Counted</th><th>Notes</th></tr></thead>
            <tbody>
              @forelse($stockTake->items as $i => $item)
                <tr>
                  <td>
                    <input type="hidden" name="items[{{ $i }}][id]" value="{{ $item->id }}">
                    {{ optional($item->product)->name ?? '#'.$item->product_id }}
                  </td>
                  <td>{{ optional($item->variant)->id ? '#'.$item->variant->id : '-' }}</td>
                  <td class="text-end">{{ $item->expected_quantity }}</td>
                  <td><input type="number" name="items[{{ $i }}][counted_quantity]" value="{{ $item->counted_quantity }}" class="form-control text-end" min="0"></td>
                  <td><input type="text" name="items[{{ $i }}][notes]" value="{{ $item->notes }}" class="form-control"></td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted">No items.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2">{{ $stockTake->notes }}</textarea></div>
        <button class="btn btn-primary">Save Counts</button>
        <a href="{{ route('admin.stock-takes.show', $stockTake) }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
