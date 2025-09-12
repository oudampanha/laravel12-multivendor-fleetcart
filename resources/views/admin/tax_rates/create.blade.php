@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Tax Rate')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Tax Rate</h4>
        <div class="card-tools">
          <a href="{{ route('admin.tax_rates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.tax_rates.store') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="tax_class_id">Tax Class <span class="text-danger">*</span></label>
                <select class="form-control @error('tax_class_id') is-invalid @enderror" id="tax_class_id" name="tax_class_id" required>
                  <option value="">Select Tax Class</option>
                  @if(isset($taxClasses))
                    @foreach($taxClasses as $taxClass)
                      <option value="{{ $taxClass->id }}" {{ old('tax_class_id') == $taxClass->id ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $taxClass->based_on)) }}
                      </option>
                    @endforeach
                  @endif
                </select>
                @error('tax_class_id')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="rate">Tax Rate (%) <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('rate') is-invalid @enderror" 
                       id="rate" name="rate" value="{{ old('rate') }}" step="0.0001" min="0" max="100" required>
                @error('rate')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="country">Country <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                       id="country" name="country" value="{{ old('country') }}" required>
                @error('country')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="state">State</label>
                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                       id="state" name="state" value="{{ old('state') }}">
                @error('state')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="city">City</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                       id="city" name="city" value="{{ old('city') }}">
                @error('city')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="zip">ZIP Code</label>
                <input type="text" class="form-control @error('zip') is-invalid @enderror" 
                       id="zip" name="zip" value="{{ old('zip') }}">
                @error('zip')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="position">Priority Position <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('position') is-invalid @enderror" 
                       id="position" name="position" value="{{ old('position', 0) }}" min="0" required>
                @error('position')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Lower numbers have higher priority</small>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Tax Rate
            </button>
            <a href="{{ route('admin.tax_rates.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection