@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Default Address')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Default Address</h4>
        <div class="card-tools">
          <a href="{{ route('admin.default-addresses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.default-addresses.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="form-group">
            <label for="user">User</label>
            <input type="text" class="form-control @error('user') is-invalid @enderror" 
                   id="user" name="user" value="{{ old('user') }}">
            @error('user')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="type">Type</label>
            <input type="text" class="form-control @error('type') is-invalid @enderror" 
                   id="type" name="type" value="{{ old('type') }}">
            @error('type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                   id="address" name="address" value="{{ old('address') }}">
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="city">City</label>
            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                   id="city" name="city" value="{{ old('city') }}">
            @error('city')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="state">State</label>
            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                   id="state" name="state" value="{{ old('state') }}">
            @error('state')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="postal_code">Postal Code</label>
            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                   id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
            @error('postal_code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Default Address
            </button>
            <a href="{{ route('admin.default-addresses.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection