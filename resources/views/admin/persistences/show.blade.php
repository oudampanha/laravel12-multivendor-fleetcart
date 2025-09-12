@extends('admin.layouts.master_layout')

@section('pageTitle', 'Persistence Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Persistence Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.persistences.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          <a href="{{ route('admin.persistences.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Persistence
          </a>
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
                <th>User:</th>
                <td>{{ $item->user ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Code:</th>
                <td>{{ $item->code ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Created At:</th>
                <td>{{ $item->created_at ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Expires At:</th>
                <td>{{ $item->expires_at ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              <a href="{{ route('admin.persistences.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Persistence
              </a>
              <form action="{{ route('admin.persistences.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Persistence
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