@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Tax Class')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Tax Class</h4>
        <div class="card-tools">
          <a href="{{ route('admin.tax_classes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.tax_classes.store') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="based_on">Based On <span class="text-danger">*</span></label>
                <select class="form-control @error('based_on') is-invalid @enderror" id="based_on" name="based_on" required>
                  <option value="">Select Tax Base</option>
                  <option value="shipping_address" {{ old('based_on') == 'shipping_address' ? 'selected' : '' }}>Shipping Address</option>
                  <option value="billing_address" {{ old('based_on') == 'billing_address' ? 'selected' : '' }}>Billing Address</option>
                  <option value="store_address" {{ old('based_on') == 'store_address' ? 'selected' : '' }}>Store Address</option>
                </select>
                @error('based_on')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Choose the address type to use for tax calculation</small>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Tax Class
            </button>
            <a href="{{ route('admin.tax_classes.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection