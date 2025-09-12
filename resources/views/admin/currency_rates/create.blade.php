@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Currency Rate')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Currency Rate</h4>
        <div class="card-tools">
          <a href="{{ route('admin.currency_rates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.currency_rates.store') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="currency_name">Currency Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('currency_name') is-invalid @enderror" 
                       id="currency_name" name="currency_name" value="{{ old('currency_name') }}" required>
                @error('currency_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="currency_code">Currency Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('currency_code') is-invalid @enderror" 
                       id="currency_code" name="currency_code" value="{{ old('currency_code') }}" 
                       placeholder="USD, EUR, GBP" required>
                @error('currency_code')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="currency_symbol">Currency Symbol</label>
                <input type="text" class="form-control @error('currency_symbol') is-invalid @enderror" 
                       id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol') }}" 
                       placeholder="$, €, £">
                @error('currency_symbol')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="exchange_rate">Exchange Rate <span class="text-danger">*</span></label>
                <input type="number" step="0.0001" class="form-control @error('exchange_rate') is-invalid @enderror" 
                       id="exchange_rate" name="exchange_rate" value="{{ old('exchange_rate') }}" required>
                @error('exchange_rate')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="base_currency">Base Currency</label>
                <input type="text" class="form-control @error('base_currency') is-invalid @enderror" 
                       id="base_currency" name="base_currency" value="{{ old('base_currency', 'USD') }}">
                @error('base_currency')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="is_active">Status</label>
                <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                  <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Currency Rate
            </button>
            <a href="{{ route('admin.currency_rates.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection