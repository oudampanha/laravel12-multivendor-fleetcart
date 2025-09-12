@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Vendor Details: {{ $vendor->store_slug }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          @if(!$vendor->is_verified)
          <form action="{{ route('admin.vendors.verify', $vendor->id) }}" method="POST" class="d-inline ml-2">
            @csrf
            <button type="submit" class="btn btn-success" onclick="return confirm('Verify this vendor?')">
              <i class="fas fa-check-circle"></i> Verify Vendor
            </button>
          </form>
          @endif
          <a href="{{ route('admin.vendors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <!-- Basic Information -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Basic Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <th width="40%">Store Slug:</th>
                      <td>{{ $vendor->store_slug }}</td>
                    </tr>
                    <tr>
                      <th>Store Email:</th>
                      <td>{{ $vendor->store_email }}</td>
                    </tr>
                    <tr>
                      <th>Store Phone:</th>
                      <td>{{ $vendor->store_phone }}</td>
                    </tr>
                    <tr>
                      <th>Commission Rate:</th>
                      <td>{{ $vendor->commission_rate }}%</td>
                    </tr>
                    <tr>
                      <th>Current Balance:</th>
                      <td>${{ number_format($vendor->balance, 2) }}</td>
                    </tr>
                    <tr>
                      <th>Status:</th>
                      <td>
                        @if($vendor->is_active)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Verification:</th>
                      <td>
                        @if($vendor->is_verified)
                          <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> Verified
                          </span>
                          <br><small class="text-muted">Verified at: {{ $vendor->verified_at->format('Y-m-d H:i:s') }}</small>
                        @else
                          <span class="badge badge-warning">
                            <i class="fas fa-clock"></i> Pending Verification
                          </span>
                        @endif
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <!-- Address Information -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Address Information</h5>
              </div>
              <div class="card-body">
                <address>
                  {{ $vendor->store_address }}<br>
                  {{ $vendor->store_city }}, {{ $vendor->store_state }}<br>
                  {{ $vendor->store_zip }}<br>
                  {{ $vendor->store_country }}
                </address>
              </div>
            </div>
            
            <!-- Owner Information -->
            @if($vendor->user)
            <div class="card">
              <div class="card-header">
                <h5>Owner Information</h5>
              </div>
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <img src="{{ $vendor->user->avatar ?? '/assets/backend/images/avatar.png' }}" 
                       alt="{{ $vendor->user->first_name }}" 
                       class="rounded-circle mr-3" width="60">
                  <div>
                    <h6 class="mb-0">{{ $vendor->user->first_name }} {{ $vendor->user->last_name }}</h6>
                    <small class="text-muted">{{ $vendor->user->email }}</small><br>
                    <small class="text-muted">{{ $vendor->user->phone_no }}</small>
                  </div>
                </div>
                <p><strong>Joined:</strong> {{ $vendor->user->created_at->format('Y-m-d H:i:s') }}</p>
                <p><strong>Last Login:</strong> {{ $vendor->user->last_login ? $vendor->user->last_login->format('Y-m-d H:i:s') : 'Never' }}</p>
              </div>
            </div>
            @endif
          </div>
        </div>
        
        <div class="row mt-4">
          <!-- Banking Information -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Banking Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-borderless">
                  <tbody>
                    <tr>
                      <th width="40%">Bank Name:</th>
                      <td>{{ $vendor->bank_name ?? 'Not provided' }}</td>
                    </tr>
                    <tr>
                      <th>Account Name:</th>
                      <td>{{ $vendor->bank_account_name ?? 'Not provided' }}</td>
                    </tr>
                    <tr>
                      <th>Account Number:</th>
                      <td>{{ $vendor->bank_account_number ? '****' . substr($vendor->bank_account_number, -4) : 'Not provided' }}</td>
                    </tr>
                    <tr>
                      <th>Routing Number:</th>
                      <td>{{ $vendor->bank_routing_number ?? 'Not provided' }}</td>
                    </tr>
                    <tr>
                      <th>PayPal Email:</th>
                      <td>{{ $vendor->paypal_email ?? 'Not provided' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <!-- Statistics -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Statistics</h5>
              </div>
              <div class="card-body">
                <div class="row text-center">
                  <div class="col-4">
                    <h4 class="text-primary">{{ $vendor->products_count ?? 0 }}</h4>
                    <small>Products</small>
                  </div>
                  <div class="col-4">
                    <h4 class="text-success">{{ $vendor->orders_count ?? 0 }}</h4>
                    <small>Orders</small>
                  </div>
                  <div class="col-4">
                    <h4 class="text-info">{{ $vendor->reviews_count ?? 0 }}</h4>
                    <small>Reviews</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="row mt-3">
          <div class="col-12">
            <div class="card-footer">
              <small class="text-muted">
                <strong>Created:</strong> {{ $vendor->created_at->format('Y-m-d H:i:s') }} | 
                <strong>Updated:</strong> {{ $vendor->updated_at->format('Y-m-d H:i:s') }}
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection