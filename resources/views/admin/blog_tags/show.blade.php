@extends('admin.layouts.master_layout')

@section('pageTitle', 'Blog Tag Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Blog Tag Details: {{ $blogTag->name }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.blog-tags.edit', $blogTag->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Tag Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ $blogTag->id }}</td>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <td>
                        <strong>{{ $blogTag->name }}</strong>
                        @if($blogTag->color)
                          <span class="badge ml-2" style="background-color: {{ $blogTag->color }}; color: #fff;">
                            {{ $blogTag->name }}
                          </span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Slug</th>
                      <td><code>{{ $blogTag->slug }}</code></td>
                    </tr>
                    <tr>
                      <th>Color</th>
                      <td>
                        @if($blogTag->color)
                          <div class="d-flex align-items-center">
                            <span class="color-box" style="background-color: {{ $blogTag->color }}; width: 30px; height: 30px; border-radius: 5px; display: inline-block; margin-right: 10px; border: 1px solid #ddd;"></span>
                            <code>{{ $blogTag->color }}</code>
                          </div>
                        @else
                          <span class="text-muted">No color set</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($blogTag->status ?? true)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Posts Count</th>
                      <td>
                        <span class="badge badge-info">{{ $blogTag->posts_count ?? 0 }} posts</span>
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $blogTag->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $blogTag->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            @if($blogTag->description)
            <div class="card mt-3">
              <div class="card-header">
                <h5>Description</h5>
              </div>
              <div class="card-body">
                <p>{{ $blogTag->description }}</p>
              </div>
            </div>
            @endif
          </div>
          
          <div class="col-md-6">
            @if(isset($blogTag->posts) && $blogTag->posts->count() > 0)
            <div class="card">
              <div class="card-header">
                <h5>Tagged Posts ({{ $blogTag->posts->count() }})</h5>
              </div>
              <div class="card-body">
                <div class="list-group">
                  @foreach($blogTag->posts->take(10) as $post)
                  <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                      <h6 class="mb-1">
                        <a href="{{ route('admin.blog-posts.show', $post->id) }}" class="text-decoration-none">
                          {{ $post->title }}
                        </a>
                      </h6>
                      <small>
                        @if($post->status === 'published')
                          <span class="badge badge-success">Published</span>
                        @elseif($post->status === 'draft')
                          <span class="badge badge-warning">Draft</span>
                        @else
                          <span class="badge badge-secondary">Archived</span>
                        @endif
                      </small>
                    </div>
                    <p class="mb-1">{{ Str::limit($post->excerpt ?: strip_tags($post->content), 100) }}</p>
                    <small class="text-muted">
                      {{ $post->published_at ? $post->published_at->format('M j, Y') : $post->created_at->format('M j, Y') }}
                    </small>
                  </div>
                  @endforeach
                </div>
                
                @if($blogTag->posts->count() > 10)
                <div class="text-center mt-3">
                  <small class="text-muted">... and {{ $blogTag->posts->count() - 10 }} more posts</small>
                </div>
                @endif
              </div>
            </div>
            @else
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i>
              No posts are tagged with this tag yet.
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection