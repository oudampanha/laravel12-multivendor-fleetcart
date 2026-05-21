@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Order Product Variation')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Order Product Variation</h4>
        <div class="card-tools">
          <a href="{{ route('admin.order_product_variations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.order_product_variations.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="order_product">Order Product</label>
            <input type="text" class="form-control @error('order_product') is-invalid @enderror" 
                   id="order_product" name="order_product" value="{{ old('order_product', $item->order_product ?? '') }}">
            @error('order_product')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="variation">Variation</label>
            <input type="text" class="form-control @error('variation') is-invalid @enderror" 
                   id="variation" name="variation" value="{{ old('variation', $item->variation ?? '') }}">
            @error('variation')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="variation_value">Variation Value</label>
            <input type="text" class="form-control @error('variation_value') is-invalid @enderror" 
                   id="variation_value" name="variation_value" value="{{ old('variation_value', $item->variation_value ?? '') }}">
            @error('variation_value')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="price">Price</label>
            <input type="text" class="form-control @error('price') is-invalid @enderror" 
                   id="price" name="price" value="{{ old('price', $item->price ?? '') }}">
            @error('price')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Order Product Variation
            </button>
            <a href="{{ route('admin.order_product_variations.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection