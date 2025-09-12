@extends('admin.layouts.master_layout')

@section('pageTitle', 'Coupons Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Coupons Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Coupon
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="couponsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Vendor</th>
                <th>Value</th>
                <th>Type</th>
                <th>Status</th>
                <th>Used</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($coupons as $coupon)
              <tr>
                <td>{{ $coupon->id }}</td>
                <td><strong>{{ $coupon->code }}</strong></td>
                <td>
                  @if($coupon->vendor_id)
                    <a href="{{ route('admin.vendors.show', $coupon->vendor_id) }}" class="text-primary">
                      {{ $coupon->vendor->user->first_name ?? 'N/A' }}
                    </a>
                  @else
                    <span class="badge badge-primary">Global</span>
                  @endif
                </td>
                <td>
                  @if($coupon->is_percent)
                    {{ $coupon->value }}%
                  @else
                    ${{ number_format($coupon->value, 2) }}
                  @endif
                </td>
                <td>
                  @if($coupon->is_percent)
                    <span class="badge badge-info">Percentage</span>
                  @else
                    <span class="badge badge-success">Fixed</span>
                  @endif
                </td>
                <td>
                  @if($coupon->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>{{ $coupon->used }}/{{ $coupon->usage_limit_per_coupon ?? '∞' }}</td>
                <td>{{ $coupon->start_date ? $coupon->start_date->format('Y-m-d') : 'N/A' }}</td>
                <td>{{ $coupon->end_date ? $coupon->end_date->format('Y-m-d') : 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.coupons.show', $coupon->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this coupon?')">
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
              <tr>
                <td colspan="10" class="text-center">No coupons found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($coupons, 'links'))
          <div class="d-flex justify-content-center">
            {{ $coupons->links() }}
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
  $('#couponsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush