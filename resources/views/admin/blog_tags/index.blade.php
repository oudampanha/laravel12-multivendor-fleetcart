@extends('admin.layouts.master_layout')

@section('pageTitle', 'Blog Tags Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Blog Tags Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Tag
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="blogTagsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th>Color</th>
                <th>Posts Count</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($blogTags ?? [] as $tag)
              <tr>
                <td>{{ $tag->id }}</td>
                <td>
                  <strong>{{ $tag->name }}</strong>
                  @if($tag->color)
                    <span class="badge ml-2" style="background-color: {{ $tag->color }}; color: #fff;">
                      {{ $tag->name }}
                    </span>
                  @endif
                </td>
                <td><code>{{ $tag->slug }}</code></td>
                <td>{{ Str::limit($tag->description, 50) ?: '-' }}</td>
                <td>
                  @if($tag->color)
                    <div class="d-flex align-items-center">
                      <span class="color-box" style="background-color: {{ $tag->color }}; width: 20px; height: 20px; border-radius: 3px; display: inline-block; margin-right: 5px;"></span>
                      <code>{{ $tag->color }}</code>
                    </div>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-info">{{ $tag->posts_count ?? 0 }}</span>
                </td>
                <td>
                  @if($tag->status ?? true)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.blog-tags.show', $tag->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.blog-tags.edit', $tag->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.blog-tags.destroy', $tag->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tag?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(isset($blogTags) && method_exists($blogTags, 'links'))
          <div class="d-flex justify-content-center">
            {{ $blogTags->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
  $('#blogTagsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush