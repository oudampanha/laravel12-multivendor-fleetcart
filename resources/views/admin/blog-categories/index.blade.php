@extends('admin.layouts.master_layout')

@section('pageTitle', 'Blog Categories Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Blog Categories Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Blog Category
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="blogCategoriesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Slug</th>
                <th>Posts Count</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($blogCategories as $blogCategory)
              <tr>
                <td>{{ $blogCategory->id }}</td>
                <td>{{ $blogCategory->slug }}</td>
                <td>{{ $blogCategory->posts_count ?? 0 }}</td>
                <td>{{ $blogCategory->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.blog_categories.show', $blogCategory->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.blog_categories.edit', $blogCategory->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.blog_categories.destroy', $blogCategory->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this blog category?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">No blog categories found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($blogCategories, 'links'))
          <div class="d-flex justify-content-center">
            {{ $blogCategories->links() }}
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
  $('#blogCategoriesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush