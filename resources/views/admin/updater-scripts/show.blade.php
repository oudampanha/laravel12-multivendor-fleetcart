@extends('admin.layouts.master_layout')

@section('pageTitle', 'Updater Script Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Updater Script Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.updater-scripts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          @if (Route::has('admin.updater_scripts.edit'))
<a href="{{ route('admin.updater_scripts.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Updater Script
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
                <th>Version:</th>
                <td>{{ $item->version ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Script Name:</th>
                <td>{{ $item->script_name ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Executed At:</th>
                <td>{{ $item->executed_at ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Status:</th>
                <td>{{ $item->status ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Output:</th>
                <td>{{ $item->output ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              @if (Route::has('admin.updater_scripts.edit'))
<a href="{{ route('admin.updater_scripts.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Updater Script
              </a>
@endif
              @if (Route::has('admin.updater_scripts.destroy'))
<form action="{{ route('admin.updater_scripts.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Updater Script
                </button>
              </form>
@endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection