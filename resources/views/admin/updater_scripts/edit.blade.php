@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Updater Script')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Updater Script</h4>
        <div class="card-tools">
          <a href="{{ route('admin.updater-scripts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.updater_scripts.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="version">Version</label>
            <input type="text" class="form-control @error('version') is-invalid @enderror" 
                   id="version" name="version" value="{{ old('version', $item->version ?? '') }}">
            @error('version')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="script_name">Script Name</label>
            <input type="text" class="form-control @error('script_name') is-invalid @enderror" 
                   id="script_name" name="script_name" value="{{ old('script_name', $item->script_name ?? '') }}">
            @error('script_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="executed_at">Executed At</label>
            <input type="text" class="form-control @error('executed_at') is-invalid @enderror" 
                   id="executed_at" name="executed_at" value="{{ old('executed_at', $item->executed_at ?? '') }}">
            @error('executed_at')
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
            <label for="output">Output</label>
            <input type="text" class="form-control @error('output') is-invalid @enderror" 
                   id="output" name="output" value="{{ old('output', $item->output ?? '') }}">
            @error('output')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Updater Script
            </button>
            <a href="{{ route('admin.updater-scripts.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection