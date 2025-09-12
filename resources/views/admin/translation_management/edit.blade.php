@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Translation Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Translation Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.translation_management.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.translation_management.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="key">Key</label>
            <input type="text" class="form-control @error('key') is-invalid @enderror" 
                   id="key" name="key" value="{{ old('key', $item->key ?? '') }}">
            @error('key')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="group">Group</label>
            <input type="text" class="form-control @error('group') is-invalid @enderror" 
                   id="group" name="group" value="{{ old('group', $item->group ?? '') }}">
            @error('group')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="text">Text</label>
            <input type="text" class="form-control @error('text') is-invalid @enderror" 
                   id="text" name="text" value="{{ old('text', $item->text ?? '') }}">
            @error('text')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="locale">Locale</label>
            <input type="text" class="form-control @error('locale') is-invalid @enderror" 
                   id="locale" name="locale" value="{{ old('locale', $item->locale ?? '') }}">
            @error('locale')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="status">Status</label>
            <input type="text" class="form-control @error('status') is-invalid @enderror" 
                   id="status" name="status" value="{{ old('status', $item->status ?? '') }}">
            @error('status')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Translation Management
            </button>
            <a href="{{ route('admin.translation_management.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection