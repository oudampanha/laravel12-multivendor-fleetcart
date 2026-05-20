@extends('admin.layouts.master_layout')

@section('pageTitle', 'Warehouse Details')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">{{ $warehouse->name }} <small class="text-muted">({{ $warehouse->code }})</small></h4>
      <div>
        <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">
          @if($warehouse->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif
          @if($warehouse->is_default)<span class="badge badge-info">Default</span>@endif
        </dd>
        <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $warehouse->address }}</dd>
        <dt class="col-sm-3">City / State / Country</dt><dd class="col-sm-9">{{ $warehouse->city }} / {{ $warehouse->state }} / {{ $warehouse->country }} {{ $warehouse->zip }}</dd>
        <dt class="col-sm-3">Contact</dt><dd class="col-sm-9">{{ $warehouse->contact_person }} · {{ $warehouse->phone }} · {{ $warehouse->email }}</dd>
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $warehouse->notes }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Stock On Hand</h5></div>
    <div class="card-body table-responsive">
      <table class="table table-sm">
        <thead><tr><th>Product</th><th>Variant</th><th class="text-end">Qty</th><th class="text-end">Reserved</th><th class="text-end">Reorder</th><th class="text-end">Avg Cost</th></tr></thead>
        <tbody>
          @forelse($warehouse->stocks as $s)
            <tr>
              <td>{{ optional($s->product)->name ?? '#'.$s->product_id }}</td>
              <td>{{ optional($s->variant)->id ? '#'.$s->variant->id : '-' }}</td>
              <td class="text-end">{{ $s->quantity }}</td>
              <td class="text-end">{{ $s->reserved_quantity }}</td>
              <td class="text-end">{{ $s->reorder_level }}</td>
              <td class="text-end">{{ number_format((float) $s->average_cost, 4) }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted">No stock recorded yet for this warehouse.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
