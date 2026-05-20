@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock On Hand')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Stock On Hand</h4>
      <div>
        <a href="{{ route('admin.product-stocks.low-stock') }}" class="btn btn-warning btn-sm">Low Stock</a>
        <a href="{{ route('admin.product-stocks.out-of-stock') }}" class="btn btn-danger btn-sm">Out of Stock</a>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-3"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search product SKU/slug..."></div>
        <div class="col-md-3">
          <select name="warehouse_id" class="form-control">
            <option value="">All warehouses</option>
            @foreach($warehouses as $w)
              <option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="low_stock" class="form-control">
            <option value="">All stock</option>
            <option value="1" @selected(request('low_stock'))>Low stock only</option>
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-sm" id="productStocksTable">
          <thead>
            <tr>
              <th>Product</th><th>Variant</th><th>Warehouse</th>
              <th class="text-end">Qty</th><th class="text-end">Reserved</th>
              <th class="text-end">Available</th><th class="text-end">Reorder Lvl</th>
              <th class="text-end">Avg Cost</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($stocks as $s)
              <tr class="{{ $s->needsReorder() ? 'table-warning' : '' }}">
                <td>{{ optional($s->product)->name ?? '#'.$s->product_id }}</td>
                <td>{{ optional($s->variant)->id ? '#'.$s->variant->id : '-' }}</td>
                <td>{{ optional($s->warehouse)->name }}</td>
                <td class="text-end">{{ $s->quantity }}</td>
                <td class="text-end">{{ $s->reserved_quantity }}</td>
                <td class="text-end">{{ $s->available_quantity }}</td>
                <td class="text-end">{{ $s->reorder_level }}</td>
                <td class="text-end">{{ number_format((float) $s->average_cost, 4) }}</td>
                <td>
                  <a href="{{ route('admin.product-stocks.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fas fa-cog"></i></a>
                  <a href="{{ route('admin.product-stocks.show', $s) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($stocks, 'links')) {{ $stocks->links() }} @endif
    </div>
  </div>
@endsection
