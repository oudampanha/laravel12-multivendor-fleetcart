@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Product Variant')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Product Variant</h4>
        <div class="card-tools">
          @if (Route::has('admin.product_variants.index'))
<a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.product-variants.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
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
            <label for="sku">Sku</label>
            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                   id="sku" name="sku" value="{{ old('sku', $item->sku ?? '') }}">
            @error('sku')
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
            <label for="stock">Stock</label>
            <input type="text" class="form-control @error('stock') is-invalid @enderror" 
                   id="stock" name="stock" value="{{ old('stock', $item->stock ?? '') }}">
            @error('stock')
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
              <i class="fas fa-save"></i> Update Product Variant
            </button>
            @if (Route::has('admin.product_variants.index'))
<a href="{{ route('admin.product_variants.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
@endif
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection