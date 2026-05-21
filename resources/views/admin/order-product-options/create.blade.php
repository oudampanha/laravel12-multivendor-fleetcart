@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Order Product Option')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Order Product Option</h4>
        <div class="card-tools">
          <a href="{{ route('admin.order-product-options.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.order-product-options.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="form-group">
            <label for="order_product">Order Product</label>
            <input type="text" class="form-control @error('order_product') is-invalid @enderror" 
                   id="order_product" name="order_product" value="{{ old('order_product') }}">
            @error('order_product')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="option">Option</label>
            <input type="text" class="form-control @error('option') is-invalid @enderror" 
                   id="option" name="option" value="{{ old('option') }}">
            @error('option')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="option_value">Option Value</label>
            <input type="text" class="form-control @error('option_value') is-invalid @enderror" 
                   id="option_value" name="option_value" value="{{ old('option_value') }}">
            @error('option_value')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="price">Price</label>
            <input type="text" class="form-control @error('price') is-invalid @enderror" 
                   id="price" name="price" value="{{ old('price') }}">
            @error('price')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Order Product Option
            </button>
            <a href="{{ route('admin.order-product-options.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection