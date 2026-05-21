@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Shipping Zone Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Vendor Shipping Zone Details: {{ $vendor_shipping_zone->name ?? $vendor_shipping_zone->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.vendor-shipping-zones.edit', $vendor_shipping_zone->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.vendor-shipping-zones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Vendor Shipping Zone Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ $vendor_shipping_zone->id }}</td>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <td><strong>{{ $vendor_shipping_zone->name ?? $vendor_shipping_zone->title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($vendor_shipping_zone->status ?? $vendor_shipping_zone->is_active ?? true)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $vendor_shipping_zone->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $vendor_shipping_zone->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            @if($vendor_shipping_zone->description)
            <div class="card">
              <div class="card-header">
                <h5>Description</h5>
              </div>
              <div class="card-body">
                <p>{{ $vendor_shipping_zone->description }}</p>
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
