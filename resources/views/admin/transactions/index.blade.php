@extends('admin.layouts.master_layout')

@section('pageTitle', 'Transactions Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Transactions Management</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="transactionsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Order ID</th>
                <th>Transaction ID</th>
                <th>Payment Method</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($transactions as $transaction)
              <tr>
                <td>{{ $transaction->id }}</td>
                <td>
                  <a href="{{ route('admin.orders.show', $transaction->order_id) }}" class="text-primary">
                    #{{ $transaction->order_id }}
                  </a>
                </td>
                <td>{{ $transaction->transaction_id }}</td>
                <td>
                  <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}</span>
                </td>
                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($transactions, 'links'))
          <div class="d-flex justify-content-center">
            {{ $transactions->links() }}
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
  $('#transactionsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush