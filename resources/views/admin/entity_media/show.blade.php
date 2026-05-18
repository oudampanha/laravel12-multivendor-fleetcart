@extends('admin.layouts.master_layout')

@section('pageTitle', 'Entity Media Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Entity Media Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.entity_media.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          <a href="{{ route('admin.entity_media.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Entity Media
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
                <th>Entity Type:</th>
                <td>{{ $item->entity_type ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Entity Id:</th>
                <td>{{ $item->entity_id ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Media:</th>
                <td>{{ $item->media ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Zone:</th>
                <td>{{ $item->zone ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Sort Order:</th>
                <td>{{ $item->sort_order ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              <a href="{{ route('admin.entity_media.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Entity Media
              </a>
              <form action="{{ route('admin.entity-media.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Entity Media
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