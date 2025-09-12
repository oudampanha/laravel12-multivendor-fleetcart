@extends('admin.layouts.master_layout')

@section('pageTitle', 'Flash Sales Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Flash Sales Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.flash_sales.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Flash Sale
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-3">
            <select class="form-control" id="statusFilter">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="upcoming">Upcoming</option>
              <option value="ended">Ended</option>
            </select>
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="flashSalesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Discount</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Products Count</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($flashSales ?? [] as $sale)
              <tr>
                <td>{{ $sale->id }}</td>
                <td>
                  <strong>{{ $sale->name }}</strong><br>
                  <small class="text-muted">{{ Str::limit($sale->description, 50) }}</small>
                </td>
                <td>
                  @if($sale->discount_type === 'percentage')
                    <span class="badge badge-success">{{ $sale->discount_value }}%</span>
                  @else
                    <span class="badge badge-info">${{ number_format($sale->discount_value, 2) }}</span>
                  @endif
                </td>
                <td>{{ $sale->start_date ? $sale->start_date->format('Y-m-d H:i') : '-' }}</td>
                <td>{{ $sale->end_date ? $sale->end_date->format('Y-m-d H:i') : '-' }}</td>
                <td>
                  <span class="badge badge-info">{{ $sale->products_count ?? 0 }}</span>
                </td>
                <td>
                  @php
                    $now = now();
                    if ($sale->start_date && $sale->end_date) {
                      if ($now < $sale->start_date) {
                        $status = 'upcoming';
                      } elseif ($now > $sale->end_date) {
                        $status = 'ended';
                      } else {
                        $status = 'active';
                      }
                    } else {
                      $status = $sale->is_active ? 'active' : 'inactive';
                    }
                  @endphp
                  
                  @if($status === 'active')
                    <span class="badge badge-success">Active</span>
                  @elseif($status === 'upcoming')
                    <span class="badge badge-warning">Upcoming</span>
                  @elseif($status === 'ended')
                    <span class="badge badge-secondary">Ended</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.flash_sales.show', $sale->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.flash_sales.edit', $sale->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.flash_sales.destroy', $sale->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this flash sale?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">No flash sales found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(isset($flashSales) && method_exists($flashSales, 'links'))
          <div class="d-flex justify-content-center">
            {{ $flashSales->links() }}
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
  const table = $('#flashSalesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
  
  // Status filter
  $('#statusFilter').on('change', function() {
    const status = $(this).val();
    if (status === 'active') {
      table.column(6).search('Active').draw();
    } else if (status === 'upcoming') {
      table.column(6).search('Upcoming').draw();
    } else if (status === 'ended') {
      table.column(6).search('Ended').draw();
    } else {
      table.column(6).search('').draw();
    }
  });
});
</script>
@endpush