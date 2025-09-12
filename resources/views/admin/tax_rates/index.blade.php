@extends('admin.layouts.master_layout')

@section('pageTitle', 'Tax Rates Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Tax Rates Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.tax_rates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Tax Rate
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="taxRatesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tax Class</th>
                <th>Location</th>
                <th>Rate (%)</th>
                <th>Position</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($taxRates as $taxRate)
              <tr>
                <td>{{ $taxRate->id }}</td>
                <td>
                  <span class="badge badge-info">{{ $taxRate->taxClass->based_on ?? 'N/A' }}</span>
                </td>
                <td>
                  {{ $taxRate->country }}
                  @if($taxRate->state), {{ $taxRate->state }}@endif
                  @if($taxRate->city), {{ $taxRate->city }}@endif
                  @if($taxRate->zip), {{ $taxRate->zip }}@endif
                </td>
                <td>{{ number_format($taxRate->rate, 4) }}%</td>
                <td>{{ $taxRate->position }}</td>
                <td>{{ $taxRate->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.tax_rates.show', $taxRate->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.tax_rates.edit', $taxRate->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tax_rates.destroy', $taxRate->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax rate?')">
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
                <td colspan="7" class="text-center">No tax rates found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($taxRates, 'links'))
          <div class="d-flex justify-content-center">
            {{ $taxRates->links() }}
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
  $('#taxRatesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush