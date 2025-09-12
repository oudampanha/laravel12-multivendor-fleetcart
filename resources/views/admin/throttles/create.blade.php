@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New Throttle')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New Throttle</h4>
        <div class="card-tools">
          <a href="{{ route('admin.throttles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.throttles.store') }}" method="POST" enctype="multipart/form-data">
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
            <label for="ip">Ip</label>
            <input type="text" class="form-control @error('ip') is-invalid @enderror" 
                   id="ip" name="ip" value="{{ old('ip') }}">
            @error('ip')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="last_activity_at">Last Activity At</label>
            <input type="text" class="form-control @error('last_activity_at') is-invalid @enderror" 
                   id="last_activity_at" name="last_activity_at" value="{{ old('last_activity_at') }}">
            @error('last_activity_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create Throttle
            </button>
            <a href="{{ route('admin.throttles.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection