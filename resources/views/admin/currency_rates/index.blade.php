@extends('admin.layouts.master_layout')

@section('pageTitle', 'Currency Rates Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Currency Rates Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.currency_rates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Rate
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
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="currencyRatesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Currency</th>
                <th>Code</th>
                <th>Rate</th>
                <th>Base Currency</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($currencyRates ?? [] as $rate)
              <tr>
                <td>{{ $rate->id }}</td>
                <td>
                  <strong>{{ $rate->currency_name }}</strong><br>
                  <small class="text-muted">{{ $rate->currency_symbol ?? '$' }}</small>
                </td>
                <td><code>{{ $rate->currency_code }}</code></td>
                <td>
                  <strong>{{ number_format($rate->exchange_rate, 4) }}</strong>
                </td>
                <td><code>{{ $rate->base_currency ?? 'USD' }}</code></td>
                <td>
                  @if($rate->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>{{ $rate->updated_at->format('Y-m-d H:i') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.currency_rates.show', $rate->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.currency_rates.edit', $rate->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.currency_rates.destroy', $rate->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this currency rate?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">No currency rates found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(isset($currencyRates) && method_exists($currencyRates, 'links'))
          <div class="d-flex justify-content-center">
            {{ $currencyRates->links() }}
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
  const table = $('#currencyRatesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
  
  // Status filter
  $('#statusFilter').on('change', function() {
    const status = $(this).val();
    if (status === 'active') {
      table.column(5).search('Active').draw();
    } else if (status === 'inactive') {
      table.column(5).search('Inactive').draw();
    } else {
      table.column(5).search('').draw();
    }
  });
});
</script>
@endpush