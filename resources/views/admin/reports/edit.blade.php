@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Report')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Report</h4>
        <div class="card-tools">
          @if (Route::has('admin.reports.index'))
<a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        @if (Route::has('admin.reports.update'))
<form action="{{ route('admin.reports.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                   id="name" name="name" value="{{ old('name', $item->name ?? '') }}">
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="type">Type</label>
            <input type="text" class="form-control @error('type') is-invalid @enderror" 
                   id="type" name="type" value="{{ old('type', $item->type ?? '') }}">
            @error('type')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="data">Data</label>
            <input type="text" class="form-control @error('data') is-invalid @enderror" 
                   id="data" name="data" value="{{ old('data', $item->data ?? '') }}">
            @error('data')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="generated_at">Generated At</label>
            <input type="text" class="form-control @error('generated_at') is-invalid @enderror" 
                   id="generated_at" name="generated_at" value="{{ old('generated_at', $item->generated_at ?? '') }}">
            @error('generated_at')
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
              <i class="fas fa-save"></i> Update Report
            </button>
            @if (Route::has('admin.reports.index'))
<a href="{{ route('admin.reports.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
@endif
          </div>
        </form>
@endif
      </div>
    </div>
  </div>
</div>
@endsection