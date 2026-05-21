@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create Attribute Value')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create Attribute Value</h4>
        <div class="card-tools">
          @if (Route::has('admin.attribute_values.index'))
<a href="{{ route('admin.attribute_values.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.attribute-values.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}">
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Save
            </button>
            @if (Route::has('admin.attribute_values.index'))
<a href="{{ route('admin.attribute_values.index') }}" class="btn btn-secondary ml-2">
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
