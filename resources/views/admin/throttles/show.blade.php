@extends('admin.layouts.master_layout')

@section('pageTitle', 'Throttle Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Throttle Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.throttles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          <a href="{{ route('admin.throttles.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Throttle
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
                <th>Type:</th>
                <td>{{ $item->type ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Ip:</th>
                <td>{{ $item->ip ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Created At:</th>
                <td>{{ $item->created_at ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Last Activity At:</th>
                <td>{{ $item->last_activity_at ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              <a href="{{ route('admin.throttles.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Throttle
              </a>
              <form action="{{ route('admin.throttles.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Throttle
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