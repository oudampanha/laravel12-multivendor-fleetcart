@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Movement')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Stock Movement #{{ $stockMovement->id }}</h4>
      <a href="{{ route('admin.stock-movements.index') }}" class="btn btn-secondary">Back</a>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Date</dt><dd class="col-sm-9">{{ $stockMovement->created_at->format('Y-m-d H:i:s') }}</dd>
        <dt class="col-sm-3">Type</dt><dd class="col-sm-9"><span class="badge badge-secondary">{{ $stockMovement->type }}</span></dd>
        <dt class="col-sm-3">Product</dt><dd class="col-sm-9">{{ optional($stockMovement->product)->name ?? '#'.$stockMovement->product_id }}</dd>
        <dt class="col-sm-3">Variant</dt><dd class="col-sm-9">{{ optional($stockMovement->variant)->id ? '#'.$stockMovement->variant->id : '-' }}</dd>
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($stockMovement->warehouse)->name }}</dd>
        <dt class="col-sm-3">Quantity</dt><dd class="col-sm-9">{{ $stockMovement->quantity }}</dd>
        <dt class="col-sm-3">Balance After</dt><dd class="col-sm-9">{{ $stockMovement->balance_after }}</dd>
        <dt class="col-sm-3">Unit Cost</dt><dd class="col-sm-9">{{ number_format((float) $stockMovement->unit_cost, 4) }}</dd>
        <dt class="col-sm-3">Total Cost</dt><dd class="col-sm-9">{{ number_format((float) $stockMovement->total_cost, 4) }}</dd>
        <dt class="col-sm-3">Batch / Expiry</dt><dd class="col-sm-9">{{ $stockMovement->batch_number ?: '-' }} / {{ optional($stockMovement->expiry_date)->format('Y-m-d') ?: '-' }}</dd>
        <dt class="col-sm-3">Reference</dt><dd class="col-sm-9">{{ $stockMovement->reference_type ? class_basename($stockMovement->reference_type).' #'.$stockMovement->reference_id : '-' }}</dd>
        <dt class="col-sm-3">User</dt><dd class="col-sm-9">{{ optional($stockMovement->user)->name ?? '-' }}</dd>
        <dt class="col-sm-3">Notes</dt><dd class="col-sm-9">{{ $stockMovement->notes ?: '-' }}</dd>
      </dl>
    </div>
  </div>
@endsection
