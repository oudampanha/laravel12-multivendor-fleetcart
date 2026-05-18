@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Up Sell Product')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Up Sell Product</h4>
        <div class="card-tools">
          <a href="{{ route('admin.up-sell-products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.up_sell_products.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="product">Product</label>
            <input type="text" class="form-control @error('product') is-invalid @enderror" 
                   id="product" name="product" value="{{ old('product', $item->product ?? '') }}">
            @error('product')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="up_sell_product">Up Sell Product</label>
            <input type="text" class="form-control @error('up_sell_product') is-invalid @enderror" 
                   id="up_sell_product" name="up_sell_product" value="{{ old('up_sell_product', $item->up_sell_product ?? '') }}">
            @error('up_sell_product')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="sort_order">Sort Order</label>
            <input type="text" class="form-control @error('sort_order') is-invalid @enderror" 
                   id="sort_order" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? '') }}">
            @error('sort_order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="status">Status</label>
            <input type="text" class="form-control @error('status') is-invalid @enderror" 
                   id="status" name="status" value="{{ old('status', $item->status ?? '') }}">
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Up Sell Product
            </button>
            <a href="{{ route('admin.up-sell-products.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection