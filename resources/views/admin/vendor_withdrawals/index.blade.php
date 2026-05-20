@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Withdrawals Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Vendor Withdrawals Management</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="vendorWithdrawalsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Processed Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($vendorWithdrawals as $withdrawal)
              <tr>
                <td>{{ $withdrawal->id }}</td>
                <td>
                  <a href="{{ route('admin.vendors.show', $withdrawal->vendor_id) }}" class="text-primary">
                    {{ $withdrawal->vendor->user->first_name ?? 'N/A' }} {{ $withdrawal->vendor->user->last_name ?? '' }}
                  </a>
                </td>
                <td>${{ number_format($withdrawal->amount, 2) }}</td>
                <td>
                  <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $withdrawal->method)) }}</span>
                </td>
                <td>
                  @switch($withdrawal->status)
                    @case('pending')
                      <span class="badge badge-warning">Pending</span>
                      @break
                    @case('processing')
                      <span class="badge badge-info">Processing</span>
                      @break
                    @case('completed')
                      <span class="badge badge-success">Completed</span>
                      @break
                    @case('rejected')
                      <span class="badge badge-danger">Rejected</span>
                      @break
                  @endswitch
                </td>
                <td>{{ $withdrawal->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  @if($withdrawal->processed_at)
                    {{ $withdrawal->processed_at->format('Y-m-d H:i:s') }}
                  @else
                    <span class="text-muted">Not processed</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.vendor-withdrawals.show', $withdrawal->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    @if($withdrawal->status === 'pending')
                    <a href="{{ route('admin.vendor-withdrawals.edit', $withdrawal->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($vendorWithdrawals, 'links'))
          <div class="d-flex justify-content-center">
            {{ $vendorWithdrawals->links() }}
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
  $('#vendorWithdrawalsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush