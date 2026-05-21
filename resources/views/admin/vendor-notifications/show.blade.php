@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Notification Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Vendor Notification Details: {{ $vendor_notification->name ?? $vendor_notification->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.vendor-notifications.edit', $vendor_notification->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.vendor-notifications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Vendor Notification Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ $vendor_notification->id }}</td>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <td><strong>{{ $vendor_notification->name ?? $vendor_notification->title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($vendor_notification->status ?? $vendor_notification->is_active ?? true)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $vendor_notification->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $vendor_notification->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            @if($vendor_notification->description)
            <div class="card">
              <div class="card-header">
                <h5>Description</h5>
              </div>
              <div class="card-body">
                <p>{{ $vendor_notification->description }}</p>
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
