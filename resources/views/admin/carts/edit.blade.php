@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Cart')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Cart</h4>
        <div class="card-tools">
          <a href="{{ route('admin.carts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        @if (Route::has('admin.carts.update'))
<form action="{{ route('admin.carts.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="user">User</label>
            <input type="text" class="form-control @error('user') is-invalid @enderror" 
                   id="user" name="user" value="{{ old('user', $item->user ?? '') }}">
            @error('user')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="items_count">Items Count</label>
            <input type="text" class="form-control @error('items_count') is-invalid @enderror" 
                   id="items_count" name="items_count" value="{{ old('items_count', $item->items_count ?? '') }}">
            @error('items_count')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="total_amount">Total Amount</label>
            <input type="text" class="form-control @error('total_amount') is-invalid @enderror" 
                   id="total_amount" name="total_amount" value="{{ old('total_amount', $item->total_amount ?? '') }}">
            @error('total_amount')
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
              <i class="fas fa-save"></i> Update Cart
            </button>
            <a href="{{ route('admin.carts.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
@endif
      </div>
    </div>
  </div>
</div>
@endsection