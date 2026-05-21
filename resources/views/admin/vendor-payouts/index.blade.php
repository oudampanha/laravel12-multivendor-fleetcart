@extends('admin.layouts.master_layout')

@section('pageTitle', 'Vendor Payouts Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Vendor Payouts Management</h4>
          <div class="card-tools">
            <a href="{{ route('admin.vendor-payouts.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Create New Payout
            </a>
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-3">
              <select class="form-control" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="canceled">Canceled</option>
              </select>
            </div>
            <div class="col-md-3">
              <select class="form-control" id="methodFilter">
                <option value="">All Methods</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="paypal">PayPal</option>
                <option value="stripe">Stripe</option>
              </select>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="vendorPayoutsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Vendor</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Reference</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($vendorPayouts ?? [] as $payout)
                  <tr>
                    <td>{{ $payout->id }}</td>
                    <td>
                      @if ($payout->vendor)
                        <strong>{{ $payout->vendor->store_slug }}</strong><br>
                        <small class="text-muted">{{ $payout->vendor->store_email }}</small>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <strong class="text-primary">${{ number_format($payout->amount, 2) }}</strong>
                    </td>
                    <td>
                      @if ($payout->method === 'bank_transfer')
                        <span class="badge badge-info">Bank Transfer</span>
                      @elseif($payout->method === 'paypal')
                        <span class="badge badge-primary">PayPal</span>
                      @elseif($payout->method === 'stripe')
                        <span class="badge badge-success">Stripe</span>
                      @else
                        <span class="badge badge-secondary">{{ $payout->method }}</span>
                      @endif
                    </td>
                    <td>
                      @if ($payout->reference_number)
                        <code>{{ $payout->reference_number }}</code>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                    <td>
                      @if ($payout->status === 'pending')
                        <span class="badge badge-warning">Pending</span>
                      @elseif($payout->status === 'processing')
                        <span class="badge badge-info">Processing</span>
                      @elseif($payout->status === 'completed')
                        <span class="badge badge-success">Completed</span>
                      @elseif($payout->status === 'canceled')
                        <span class="badge badge-danger">Canceled</span>
                      @else
                        <span class="badge badge-secondary">{{ $payout->status }}</span>
                      @endif
                    </td>
                    <td>{{ $payout->paid_at ? $payout->paid_at->format('Y-m-d') : $payout->created_at->format('Y-m-d') }}
                    </td>
                    <td>
                      <div class="btn-group">
                        <a href="{{ route('admin.vendor-payouts.show', $payout->id) }}" class="btn btn-sm btn-info">
                          <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.vendor-payouts.edit', $payout->id) }}" class="btn btn-sm btn-warning">
                          <i class="fas fa-edit"></i>
                        </a>
                        @if ($payout->status === 'pending')
                          <form action="{{ route('admin.vendor-payouts.approve', $payout->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success"
                              onclick="return confirm('Process this payout?')">
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

          @if (isset($vendorPayouts) && method_exists($vendorPayouts, 'links'))
            <div class="d-flex justify-content-center">
              {{ $vendorPayouts->links() }}
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
      const table = $('#vendorPayoutsTable').DataTable({
        responsive: true,
        order: [
          [0, 'desc']
        ],
        pageLength: 25,
        language: {
          emptyTable: 'No payouts found'
        }
      });

      // Status filter
      $('#statusFilter').on('change', function() {
        const status = $(this).val();
        table.column(5).search(status).draw();
      });

      // Method filter
      $('#methodFilter').on('change', function() {
        const method = $(this).val();
        table.column(3).search(method).draw();
      });
    });
  </script>
@endpush
