@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Flash Sale')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit Flash Sale</h4>
        <div class="card-tools">
          <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.flash-sales.update', $flashSale->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')

          @php
            $attrs = method_exists($flashSale ?? null, 'getAttributes') ? $flashSale->getAttributes() : (array)($flashSale ?? []);
          @endphp
          @foreach($attrs as $key => $value)
            @continue(in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at', 'password', 'remember_token']))
            <div class="form-group">
              <label for="{{ $key }}">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
              @if(is_array($value) || is_object($value))
                <textarea class="form-control @error($key) is-invalid @enderror"
                          id="{{ $key }}" name="{{ $key }}" rows="3">{{ old($key, is_string($value) ? $value : json_encode($value)) }}</textarea>
              @elseif(str_contains($key, 'description') || str_contains($key, 'content') || str_contains($key, 'body'))
                <textarea class="form-control @error($key) is-invalid @enderror"
                          id="{{ $key }}" name="{{ $key }}" rows="4">{{ old($key, $value) }}</textarea>
              @else
                <input type="text" class="form-control @error($key) is-invalid @enderror"
                       id="{{ $key }}" name="{{ $key }}" value="{{ old($key, $value) }}">
              @endif
              @error($key)
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          @endforeach

          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update
            </button>
            <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
