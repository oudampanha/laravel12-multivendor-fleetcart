@extends('admin.layouts.master_layout')

@section('pageTitle', 'Customer Addresses Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Customer Addresses Management</h4>
        <div class="card-tools">
          @if (Route::has('admin.addresses.create'))
<a href="{{ route('admin.addresses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Address
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="addressesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Name</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>ZIP</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($addresses as $address)
              <tr>
                <td>{{ $address->id }}</td>
                <td>
                  <a href="{{ route('admin.users.show', $address->customer_id) }}" class="text-primary">
                    {{ $address->customer->first_name ?? 'N/A' }} {{ $address->customer->last_name ?? '' }}
                  </a>
                </td>
                <td>{{ $address->first_name }} {{ $address->last_name }}</td>
                <td>
                  {{ $address->address_1 }}
                  @if($address->address_2)
                    <br><small class="text-muted">{{ $address->address_2 }}</small>
                  @endif
                </td>
                <td>{{ $address->city }}</td>
                <td>{{ $address->state }}</td>
                <td>{{ $address->country }}</td>
                <td>{{ $address->zip }}</td>
                <td>{{ $address->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.addresses.show', $address->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.addresses.edit', $address->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.addresses.destroy', $address->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this address?')">
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
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($addresses, 'links'))
          <div class="d-flex justify-content-center">
            {{ $addresses->links() }}
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
  $('#addressesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush