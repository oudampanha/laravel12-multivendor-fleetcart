@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Translation')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Translation</h4>
        <div class="card-tools">
          <a href="{{ route('admin.translations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.translations.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="entity_type">Entity Type</label>
            <input type="text" class="form-control @error('entity_type') is-invalid @enderror" 
                   id="entity_type" name="entity_type" value="{{ old('entity_type', $item->entity_type ?? '') }}">
            @error('entity_type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="entity_id">Entity Id</label>
            <input type="text" class="form-control @error('entity_id') is-invalid @enderror" 
                   id="entity_id" name="entity_id" value="{{ old('entity_id', $item->entity_id ?? '') }}">
            @error('entity_id')
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
            <label for="key">Key</label>
            <input type="text" class="form-control @error('key') is-invalid @enderror" 
                   id="key" name="key" value="{{ old('key', $item->key ?? '') }}">
            @error('key')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="value">Value</label>
            <input type="text" class="form-control @error('value') is-invalid @enderror" 
                   id="value" name="value" value="{{ old('value', $item->value ?? '') }}">
            @error('value')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Translation
            </button>
            <a href="{{ route('admin.translations.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection