@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Translation Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Translation Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.translation-management.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.translation-management.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="form-group">
            <label for="key">Key</label>
            <input type="text" class="form-control @error('key') is-invalid @enderror" 
                   id="key" name="key" value="{{ old('key') }}">
            @error('key')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="group">Group</label>
            <input type="text" class="form-control @error('group') is-invalid @enderror" 
                   id="group" name="group" value="{{ old('group') }}">
            @error('group')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="text">Text</label>
            <input type="text" class="form-control @error('text') is-invalid @enderror" 
                   id="text" name="text" value="{{ old('text') }}">
            @error('text')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="locale">Locale</label>
            <input type="text" class="form-control @error('locale') is-invalid @enderror" 
                   id="locale" name="locale" value="{{ old('locale') }}">
            @error('locale')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="status">Status</label>
            <input type="text" class="form-control @error('status') is-invalid @enderror" 
                   id="status" name="status" value="{{ old('status') }}">
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Translation Management
            </button>
            <a href="{{ route('admin.translation-management.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection