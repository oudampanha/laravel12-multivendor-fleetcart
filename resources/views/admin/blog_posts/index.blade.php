@extends('admin.layouts.master_layout')

@section('pageTitle', 'Blog Posts Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Blog Posts Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.blog_posts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Blog Post
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-3">
            <select class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="published">Published</option>
              <option value="draft">Draft</option>
              <option value="archived">Archived</option>
            </select>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="blogPostsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Status</th>
                <th>Featured</th>
                <th>Published At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($blogPosts ?? [] as $post)
              <tr>
                <td>{{ $post->id }}</td>
                <td>
                  <strong>{{ $post->title }}</strong><br>
                  <small class="text-muted">{{ Str::limit($post->excerpt, 50) }}</small>
                </td>
                <td>
                  @if($post->user)
                    {{ $post->user->first_name }} {{ $post->user->last_name }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($post->category)
                    <span class="badge badge-info">{{ $post->category->name }}</span>
                  @else
                    <span class="text-muted">Uncategorized</span>
                  @endif
                </td>
                <td>
                  @if($post->status === 'published')
                    <span class="badge badge-success">Published</span>
                  @elseif($post->status === 'draft')
                    <span class="badge badge-warning">Draft</span>
                  @else
                    <span class="badge badge-secondary">Archived</span>
                  @endif
                </td>
                <td>
                  @if($post->is_featured)
                    <span class="badge badge-primary">
                      <i class="fas fa-star"></i> Featured
                    </span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>{{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : '-' }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.blog_posts.show', $post->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.blog_posts.edit', $post->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.blog_posts.destroy', $post->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this blog post?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">No blog posts found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(isset($blogPosts) && method_exists($blogPosts, 'links'))
          <div class="d-flex justify-content-center">
            {{ $blogPosts->links() }}
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
  const table = $('#blogPostsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
  
  // Status filter
  $('#statusFilter').on('change', function() {
    const status = $(this).val();
    if (status === 'published') {
      table.column(4).search('Published').draw();
    } else if (status === 'draft') {
      table.column(4).search('Draft').draw();
    } else if (status === 'archived') {
      table.column(4).search('Archived').draw();
    } else {
      table.column(4).search('').draw();
    }
  });
});
</script>
@endpush