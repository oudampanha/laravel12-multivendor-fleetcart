@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Reminder')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Reminder</h4>
        <div class="card-tools">
          <a href="{{ route('admin.reminders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        @if (Route::has('admin.reminders.update'))
<form action="{{ route('admin.reminders.update', $item->id ?? 0) }}" method="POST" enctype="multipart/form-data">
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
            <label for="completed">Completed</label>
            <input type="text" class="form-control @error('completed') is-invalid @enderror" 
                   id="completed" name="completed" value="{{ old('completed', $item->completed ?? '') }}">
            @error('completed')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <label for="completed_at">Completed At</label>
            <input type="text" class="form-control @error('completed_at') is-invalid @enderror" 
                   id="completed_at" name="completed_at" value="{{ old('completed_at', $item->completed_at ?? '') }}">
            @error('completed_at')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update Reminder
            </button>
            <a href="{{ route('admin.reminders.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
@endif
      </div>
    </div>
  </div>
</div>
@endsection