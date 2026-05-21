@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendors Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Vendors Management</h4>
          <div class="card-tools">
            <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add New Vendor
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-3">
              <select class="form-control" id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-control" id="verificationFilter">
                <option value="">All Verification</option>
                <option value="verified">Verified</option>
                <option value="unverified">Unverified</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="vendorsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Store Info</th>
                  <th>Owner</th>
                  <th>Contact</th>
                  <th>Commission Rate</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Verification</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($vendors as $vendor)
                  <tr>
                    <td>{{ $vendor->id }}</td>
                    <td>
                      <strong>{{ $vendor->store_slug }}</strong><br>
                      <small class="text-muted">{{ $vendor->store_email }}</small>
                    </td>
                    <td>
                      @if ($vendor->user)
                        {{ $vendor->user->first_name }} {{ $vendor->user->last_name }}<br>
                        <small class="text-muted">{{ $vendor->user->email }}</small>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      {{ $vendor->store_phone }}<br>
                      <small class="text-muted">{{ $vendor->store_city }}, {{ $vendor->store_country }}</small>
                    </td>
                    <td>{{ $vendor->commission_rate }}%</td>
                    <td>${{ number_format($vendor->balance, 2) }}</td>
                    <td>
                      @if ($vendor->is_active)
                        <span class="badge badge-success">Active</span>
                      @else
                        <span class="badge badge-danger">Inactive</span>
                      @endif
                    </td>
                    <td>
                      @if ($vendor->is_verified)
                        <span class="badge badge-success">
                          <i class="fas fa-check-circle"></i> Verified
                        </span>
                      @else
                        <span class="badge badge-warning">
                          <i class="fas fa-clock"></i> Pending
                        </span>
                      @endif
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="btn btn-sm btn-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        @if (!$vendor->is_verified)
                          <form action="{{ route('admin.vendors.verify', $vendor->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success"
                              onclick="return confirm('Verify this vendor?')">
                              <i class="fas fa-check"></i>
                            </button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          @if (method_exists($vendors, 'links'))
            <div class="d-flex justify-content-center">
              {{ $vendors->links() }}
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
      const table = $('#vendorsTable').DataTable({
        responsive: true,
        order: [
          [0, 'desc']
        ],
        pageLength: 25,
        language: {
          emptyTable: 'No vendors found'
        }
      });

      // Status filter
      $('#statusFilter').on('change', function() {
        const status = $(this).val();
        if (status === 'active') {
          table.column(6).search('Active').draw();
        } else if (status === 'inactive') {
          table.column(6).search('Inactive').draw();
        } else {
          table.column(6).search('').draw();
        }
      });

      // Verification filter
      $('#verificationFilter').on('change', function() {
        const verification = $(this).val();
        if (verification === 'verified') {
          table.column(7).search('Verified').draw();
        } else if (verification === 'unverified') {
          table.column(7).search('Pending').draw();
        } else {
          table.column(7).search('').draw();
        }
      });
    });
  </script>
@endpush
