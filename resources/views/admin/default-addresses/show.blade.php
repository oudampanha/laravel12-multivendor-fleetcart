@extends('admin.layouts.master_layout')

@section('pageTitle', 'Default Address Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Default Address Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.default-addresses.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          @if (Route::has('admin.default_addresses.edit'))
<a href="{{ route('admin.default_addresses.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Default Address
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
                <th>User:</th>
                <td>{{ $item->user ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Type:</th>
                <td>{{ $item->type ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Address:</th>
                <td>{{ $item->address ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>City:</th>
                <td>{{ $item->city ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>State:</th>
                <td>{{ $item->state ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Postal Code:</th>
                <td>{{ $item->postal_code ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              @if (Route::has('admin.default_addresses.edit'))
<a href="{{ route('admin.default_addresses.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Default Address
              </a>
@endif
              <form action="{{ route('admin.default-addresses.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Default Address
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