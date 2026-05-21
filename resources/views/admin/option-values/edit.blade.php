@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Option Value')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Option Value: {{ $option_value->name ?? $option_value->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          @if (Route::has('admin.option-values.index'))
<a href="{{ route('admin.option-values.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.option-values.update', $option_value->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $option_value->name ?? $option_value->title) }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                  <option value="1" {{ old('status', $option_value->status ?? $option_value->is_active ?? '1') == '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', $option_value->status ?? $option_value->is_active ?? '1') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3">{{ old('description', $option_value->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Option Value
            </button>
            @if (Route::has('admin.option-values.index'))
<a href="{{ route('admin.option-values.index') }}" class="btn btn-secondary ml-2">
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
