@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Persistence')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Persistence</h4>
        <div class="card-tools">
          <a href="{{ route('admin.persistences.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.persistences.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="user">User</label>
            <input type="text" class="form-control @error('user') is-invalid @enderror" 
                   id="user" name="user" value="{{ old('user', $item->user ?? '') }}">
            @error('user')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="code">Code</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" 
                   id="code" name="code" value="{{ old('code', $item->code ?? '') }}">
            @error('code')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="expires_at">Expires At</label>
            <input type="text" class="form-control @error('expires_at') is-invalid @enderror" 
                   id="expires_at" name="expires_at" value="{{ old('expires_at', $item->expires_at ?? '') }}">
            @error('expires_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Persistence
            </button>
            <a href="{{ route('admin.persistences.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection