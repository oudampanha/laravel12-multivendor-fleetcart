@extends('admin.layouts.master_layout')

@section('pageTitle', 'Blog Post Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Blog Post Details: {{ $blogPost->title }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.blog-posts.edit', $blogPost->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            @if($blogPost->featured_image)
              <div class="mb-4">
                <img src="{{ Storage::url($blogPost->featured_image) }}" alt="{{ $blogPost->title }}" 
                     class="img-fluid rounded shadow-sm">
              </div>
            @endif
            
            <div class="mb-4">
              <h5>Content</h5>
              <div class="border p-3 rounded bg-light">
                {!! nl2br(e($blogPost->content)) !!}
              </div>
            </div>
            
            @if($blogPost->excerpt)
            <div class="mb-4">
              <h5>Excerpt</h5>
              <div class="border p-3 rounded bg-light">
                {{ $blogPost->excerpt }}
              </div>
            </div>
            @endif
          </div>
          
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">
                <h5>Post Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-sm">
                  <tbody>
                    <tr>
                      <th width="40%">ID</th>
                      <td>{{ $blogPost->id }}</td>
                    </tr>
                    <tr>
                      <th>Title</th>
                      <td>{{ $blogPost->title }}</td>
                    </tr>
                    <tr>
                      <th>Slug</th>
                      <td>
                        <code>{{ $blogPost->slug ?: 'Not set' }}</code>
                      </td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($blogPost->status === 'published')
                          <span class="badge badge-success">Published</span>
                        @elseif($blogPost->status === 'draft')
                          <span class="badge badge-warning">Draft</span>
                        @else
                          <span class="badge badge-secondary">Archived</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Category</th>
                      <td>
                        @if($blogPost->category)
                          <span class="badge badge-info">{{ $blogPost->category->name }}</span>
                        @else
                          <span class="text-muted">Uncategorized</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Author</th>
                      <td>
                        @if($blogPost->user)
                          {{ $blogPost->user->first_name }} {{ $blogPost->user->last_name }}<br>
                          <small class="text-muted">{{ $blogPost->user->email }}</small>
                        @else
                          <span class="text-muted">N/A</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Featured</th>
                      <td>
                        @if($blogPost->is_featured)
                          <span class="badge badge-primary">
                            <i class="fas fa-star"></i> Featured
                          </span>
                        @else
                          <span class="text-muted">No</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Published At</th>
                      <td>
                        {{ $blogPost->published_at ? $blogPost->published_at->format('Y-m-d H:i:s') : 'Not published' }}
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $blogPost->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $blogPost->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            @if(isset($blogPost->tags) && $blogPost->tags->count() > 0)
            <div class="card mt-3">
              <div class="card-header">
                <h5>Tags ({{ $blogPost->tags->count() }})</h5>
              </div>
              <div class="card-body">
                @foreach($blogPost->tags as $tag)
                  <span class="badge badge-secondary mr-1 mb-1">{{ $tag->name }}</span>
                @endforeach
              </div>
            </div>
            @endif
            
            @if($blogPost->meta_title || $blogPost->meta_description)
            <div class="card mt-3">
              <div class="card-header">
                <h5>SEO Information</h5>
              </div>
              <div class="card-body">
                @if($blogPost->meta_title)
                  <div class="mb-2">
                    <strong>Meta Title:</strong><br>
                    <small>{{ $blogPost->meta_title }}</small>
                  </div>
                @endif
                
                @if($blogPost->meta_description)
                  <div class="mb-2">
                    <strong>Meta Description:</strong><br>
                    <small>{{ $blogPost->meta_description }}</small>
                  </div>
                @endif
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection