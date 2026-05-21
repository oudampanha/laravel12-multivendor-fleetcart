@extends('admin.layouts.master_layout')

@section('pageTitle', 'Translation Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Translation Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.translations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          @if (Route::has('admin.translations.edit'))
<a href="{{ route('admin.translations.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Translation
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-borderless">
              <tr>
                <th width="150">ID:</th>
                <td>{{ $item->id ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Entity Type:</th>
                <td>{{ $item->entity_type ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Entity Id:</th>
                <td>{{ $item->entity_id ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Locale:</th>
                <td>{{ $item->locale ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Key:</th>
                <td>{{ $item->key ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Value:</th>
                <td>{{ $item->value ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              @if (Route::has('admin.translations.edit'))
<a href="{{ route('admin.translations.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Translation
              </a>
@endif
              <form action="{{ route('admin.translations.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Translation
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection