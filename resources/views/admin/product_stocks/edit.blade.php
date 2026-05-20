@extends('admin.layouts.master_layout')

@section('pageTitle', 'Reorder Settings')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">Reorder Settings</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <dl class="row">
        <dt class="col-sm-3">Product</dt><dd class="col-sm-9">{{ optional($productStock->product)->name ?? '#'.$productStock->product_id }}</dd>
        <dt class="col-sm-3">Variant</dt><dd class="col-sm-9">{{ optional($productStock->variant)->id ? '#'.$productStock->variant->id : '-' }}</dd>
        <dt class="col-sm-3">Warehouse</dt><dd class="col-sm-9">{{ optional($productStock->warehouse)->name }}</dd>
        <dt class="col-sm-3">Current Quantity</dt><dd class="col-sm-9">{{ $productStock->quantity }}</dd>
      </dl>
      <form method="POST" action="{{ route('admin.product-stocks.update', $productStock) }}">
        @csrf @method('PUT')
        <div class="row">
          <div class="col-md-4"><div class="form-group">
            <label>Reorder Level</label>
            <input type="number" name="reorder_level" value="{{ old('reorder_level', $productStock->reorder_level) }}" class="form-control" min="0">
            <small class="text-muted">Stock at or below this level is flagged as low.</small>
          </div></div>
          <div class="col-md-4"><div class="form-group">
            <label>Reorder Quantity</label>
            <input type="number" name="reorder_quantity" value="{{ old('reorder_quantity', $productStock->reorder_quantity) }}" class="form-control" min="0">
            <small class="text-muted">Suggested PO quantity when reordering.</small>
          </div></div>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.product-stocks.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
