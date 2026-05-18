@extends('admin.layouts.master_layout')

@section('pageTitle', 'Tax Classes Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Tax Classes Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.tax-classes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Tax Class
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="taxClassesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Based On</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($taxClasses as $taxClass)
              <tr>
                <td>{{ $taxClass->id }}</td>
                <td>
                  <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $taxClass->based_on)) }}</span>
                </td>
                <td>{{ $taxClass->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.tax-classes.show', $taxClass->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.tax-classes.edit', $taxClass->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.tax-classes.destroy', $taxClass->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax class?')">
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
                <td colspan="4" class="text-center">No tax classes found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($taxClasses, 'links'))
          <div class="d-flex justify-content-center">
            {{ $taxClasses->links() }}
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
  $('#taxClassesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush