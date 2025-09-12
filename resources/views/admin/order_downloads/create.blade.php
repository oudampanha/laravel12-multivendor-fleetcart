@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Order Download')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Order Download</h4>
        <div class="card-tools">
          <a href="{{ route('admin.order_downloads.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.order_downloads.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="form-group">
            <label for="order">Order</label>
            <input type="text" class="form-control @error('order') is-invalid @enderror" 
                   id="order" name="order" value="{{ old('order') }}">
            @error('order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="product">Product</label>
            <input type="text" class="form-control @error('product') is-invalid @enderror" 
                   id="product" name="product" value="{{ old('product') }}">
            @error('product')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="download_count">Download Count</label>
            <input type="text" class="form-control @error('download_count') is-invalid @enderror" 
                   id="download_count" name="download_count" value="{{ old('download_count') }}">
            @error('download_count')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="downloaded_at">Downloaded At</label>
            <input type="text" class="form-control @error('downloaded_at') is-invalid @enderror" 
                   id="downloaded_at" name="downloaded_at" value="{{ old('downloaded_at') }}">
            @error('downloaded_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="expires_at">Expires At</label>
            <input type="text" class="form-control @error('expires_at') is-invalid @enderror" 
                   id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
            @error('expires_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Order Download
            </button>
            <a href="{{ route('admin.order_downloads.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection