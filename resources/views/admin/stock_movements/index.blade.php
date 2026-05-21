@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Movements')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">Stock Movements Ledger</h4></div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-3">
          <select name="warehouse_id" class="form-control">
            <option value="">All warehouses</option>
            @foreach($warehouses as $w)
              <option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="type" class="form-control">
            <option value="">All types</option>
            @foreach(['opening','receipt','issue','adjustment_in','adjustment_out','transfer_in','transfer_out','sale','return','reservation','release'] as $t)
              <option value="{{ $t }}" @selected(request('type') == $t)>{{ $t }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2"><input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control"></div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-sm" id="stockMovementsTable">
          <thead>
            <tr>
              <th>Date</th><th>Type</th><th>Product</th><th>Warehouse</th>
              <th class="text-end">Qty</th><th class="text-end">Balance</th>
              <th class="text-end">Unit Cost</th><th>Reference</th><th>User</th><th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($movements as $m)
              <tr>
                <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                <td><span class="badge badge-secondary">{{ $m->type }}</span></td>
                <td>{{ optional($m->product)->name ?? '#'.$m->product_id }}</td>
                <td>{{ optional($m->warehouse)->name }}</td>
                <td class="text-end {{ $m->quantity >= 0 ? 'text-success' : 'text-danger' }}">{{ $m->quantity }}</td>
                <td class="text-end">{{ $m->balance_after }}</td>
                <td class="text-end">{{ number_format((float) $m->unit_cost, 4) }}</td>
                <td>
                  @if($m->reference_type)
                    <small class="text-muted">{{ class_basename($m->reference_type) }} #{{ $m->reference_id }}</small>
                  @endif
                </td>
                <td>{{ optional($m->user)->name ?? '-' }}</td>
                <td><a href="{{ route('admin.stock-movements.show', $m) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($movements, 'links')) {{ $movements->links() }} @endif
    </div>
  </div>
@endsection
