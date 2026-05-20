@extends('admin.layouts.master_layout')

@section('pageTitle', 'Search Term Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Search Term Details: {{ $search_term->name ?? $search_term->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          @if (Route::has('admin.search-terms.edit'))
<a href="{{ route('admin.search-terms.edit', $search_term->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
@endif
          <a href="{{ route('admin.search-terms.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Search Term Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ $search_term->id }}</td>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <td><strong>{{ $search_term->name ?? $search_term->title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($search_term->status ?? $search_term->is_active ?? true)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $search_term->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $search_term->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            @if($search_term->description)
            <div class="card">
              <div class="card-header">
                <h5>Description</h5>
              </div>
              <div class="card-body">
                <p>{{ $search_term->description }}</p>
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
