@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create Page')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create Page</h4>
        <div class="card-tools">
          <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data">
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
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
