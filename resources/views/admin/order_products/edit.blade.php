@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Order Product')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Order Product</h4>
        <div class="card-tools">
          <a href="{{ route('admin.order_products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.order_products.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="order">Order</label>
            <input type="text" class="form-control @error('order') is-invalid @enderror" 
                   id="order" name="order" value="{{ old('order', $item->order ?? '') }}">
            @error('order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="product">Product</label>
            <input type="text" class="form-control @error('product') is-invalid @enderror" 
                   id="product" name="product" value="{{ old('product', $item->product ?? '') }}">
            @error('product')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="text" class="form-control @error('quantity') is-invalid @enderror" 
                   id="quantity" name="quantity" value="{{ old('quantity', $item->quantity ?? '') }}">
            @error('quantity')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="unit_price">Unit Price</label>
            <input type="text" class="form-control @error('unit_price') is-invalid @enderror" 
                   id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price ?? '') }}">
            @error('unit_price')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="total_price">Total Price</label>
            <input type="text" class="form-control @error('total_price') is-invalid @enderror" 
                   id="total_price" name="total_price" value="{{ old('total_price', $item->total_price ?? '') }}">
            @error('total_price')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Order Product
            </button>
            <a href="{{ route('admin.order_products.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection