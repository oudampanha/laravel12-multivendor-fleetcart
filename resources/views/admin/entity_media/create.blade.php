@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Entity Media')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Entity Media</h4>
        <div class="card-tools">
          <a href="{{ route('admin.entity-media.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.entity-media.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="form-group">
            <label for="entity_type">Entity Type</label>
            <input type="text" class="form-control @error('entity_type') is-invalid @enderror" 
                   id="entity_type" name="entity_type" value="{{ old('entity_type') }}">
            @error('entity_type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="entity_id">Entity Id</label>
            <input type="text" class="form-control @error('entity_id') is-invalid @enderror" 
                   id="entity_id" name="entity_id" value="{{ old('entity_id') }}">
            @error('entity_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="media">Media</label>
            <input type="text" class="form-control @error('media') is-invalid @enderror" 
                   id="media" name="media" value="{{ old('media') }}">
            @error('media')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="zone">Zone</label>
            <input type="text" class="form-control @error('zone') is-invalid @enderror" 
                   id="zone" name="zone" value="{{ old('zone') }}">
            @error('zone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="sort_order">Sort Order</label>
            <input type="text" class="form-control @error('sort_order') is-invalid @enderror" 
                   id="sort_order" name="sort_order" value="{{ old('sort_order') }}">
            @error('sort_order')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Entity Media
            </button>
            <a href="{{ route('admin.entity-media.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection