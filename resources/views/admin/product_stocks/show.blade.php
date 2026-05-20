@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Detail')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Stock Detail</h4>
      <a href="{{ route('admin.product-stocks.index') }}" class="btn btn-secondary">Back</a>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Product</dt><dd class="col-sm-9">{{ optional($productStock->product)->name ?? '#'.$productStock->product_id }}</dd>
        <dt class="col-sm-3">Variant</dt><dd class="col-sm-9">{{ optional($productStock->variant)->id ? '#'.$productStock->variant->id : '-' }}</dd>
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($productStock->warehouse)->name }}</dd>
        <dt class="col-sm-3">Quantity</dt><dd class="col-sm-9">{{ $productStock->quantity }}</dd>
        <dt class="col-sm-3">Reserved</dt><dd class="col-sm-9">{{ $productStock->reserved_quantity }}</dd>
        <dt class="col-sm-3">Available</dt><dd class="col-sm-9">{{ $productStock->available_quantity }}</dd>
        <dt class="col-sm-3">Reorder Level</dt><dd class="col-sm-9">{{ $productStock->reorder_level }}</dd>
        <dt class="col-sm-3">Reorder Qty</dt><dd class="col-sm-9">{{ $productStock->reorder_quantity }}</dd>
        <dt class="col-sm-3">Average Cost</dt><dd class="col-sm-9">{{ number_format((float) $productStock->average_cost, 4) }}</dd>
        <dt class="col-sm-3">Last Movement</dt><dd class="col-sm-9">{{ optional($productStock->last_movement_at)->format('Y-m-d H:i') ?? '-' }}</dd>
      </dl>
      <a href="{{ route('admin.stock-movements.index', ['product_id' => $productStock->product_id, 'warehouse_id' => $productStock->warehouse_id]) }}" class="btn btn-outline-primary"><i class="fas fa-history"></i> View Movement History</a>
      <a href="{{ route('admin.product-stocks.edit', $productStock) }}" class="btn btn-warning"><i class="fas fa-cog"></i> Edit Reorder Settings</a>
    </div>
  </div>
@endsection
