@extends('admin.layouts.master_layout')

@section('pageTitle', 'Option Value Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Option Value Details: {{ $option_value->name ?? $option_value->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.option-values.edit', $option_value->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.option-values.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Option Value Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ $option_value->id }}</td>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <td><strong>{{ $option_value->name ?? $option_value->title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($option_value->status ?? $option_value->is_active ?? true)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $option_value->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $option_value->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            @if($option_value->description)
            <div class="card">
              <div class="card-header">
                <h5>Description</h5>
              </div>
              <div class="card-body">
                <p>{{ $option_value->description }}</p>
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
