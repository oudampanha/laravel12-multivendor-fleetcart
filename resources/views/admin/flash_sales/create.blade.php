@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Flash Sale')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Flash Sale</h4>
        <div class="card-tools">
          <a href="{{ route('admin.flash_sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.flash_sales.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Flash Sale Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                       id="slug" name="slug" value="{{ old('slug') }}">
                @error('slug')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="discount_type">Discount Type</label>
                <select class="form-control @error('discount_type') is-invalid @enderror" id="discount_type" name="discount_type">
                  <option value="percentage" {{ old('discount_type', 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                  <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
                @error('discount_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-4">
              <div class="form-group">
                <label for="discount_value">Discount Value <span class="text-danger">*</span></label>
                <input type="number" step="0.01" class="form-control @error('discount_value') is-invalid @enderror" 
                       id="discount_value" name="discount_value" value="{{ old('discount_value') }}" required>
                @error('discount_value')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-4">
              <div class="form-group">
                <label for="max_discount">Max Discount Amount</label>
                <input type="number" step="0.01" class="form-control @error('max_discount') is-invalid @enderror" 
                       id="max_discount" name="max_discount" value="{{ old('max_discount') }}">
                @error('max_discount')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="start_date">Start Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                @error('start_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="end_date">End Date <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                @error('end_date')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="banner_image">Banner Image</label>
                <input type="file" class="form-control-file @error('banner_image') is-invalid @enderror" 
                       id="banner_image" name="banner_image" accept="image/*">
                @error('banner_image')
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
              <i class="fas fa-save"></i> Create Flash Sale
            </button>
            <a href="{{ route('admin.flash_sales.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
  // Auto-generate slug from name
  $('#name').on('keyup', function() {
    const name = $(this).val();
    const slug = name.toLowerCase()
      .replace(/[^\w\s-]/g, '') // Remove special characters
      .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with hyphens
      .replace(/^-+|-+$/g, ''); // Remove leading/trailing hyphens
    $('#slug').val(slug);
  });
  
  // Update discount value label based on type
  $('#discount_type').on('change', function() {
    const type = $(this).val();
    const label = type === 'percentage' ? 'Discount Percentage (%)' : 'Discount Amount ($)';
    $('label[for="discount_value"]').html(label + ' <span class="text-danger">*</span>');
    
    // Show/hide max discount based on type
    if (type === 'percentage') {
      $('#max_discount').closest('.col-md-4').show();
    } else {
      $('#max_discount').closest('.col-md-4').hide();
    }
  });
  
  // Trigger change on page load
  $('#discount_type').trigger('change');
});
</script>
@endpush