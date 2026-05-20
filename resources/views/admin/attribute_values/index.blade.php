@extends('admin.layouts.master_layout')

@section('pageTitle', 'Attribute Values Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Attribute Values Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.attribute-values.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Attribute Value
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="attributeValuesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Attribute</th>
                <th>Value</th>
                <th>Position</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attributeValues as $attributeValue)
              <tr>
                <td>{{ $attributeValue->id }}</td>
                <td>{{ $attributeValue->attribute->slug ?? 'N/A' }}</td>
                <td>{{ $attributeValue->value ?? 'No value' }}</td>
                <td>{{ $attributeValue->position }}</td>
                <td>{{ $attributeValue->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    @if (Route::has('admin.attribute_values.show'))
<a href="{{ route('admin.attribute_values.show', $attributeValue->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
@endif
                    <a href="{{ route('admin.attribute-values.edit', $attributeValue->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.attribute-values.destroy', $attributeValue->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this attribute value?')">
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
                <td colspan="6" class="text-center">No attribute values found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($attributeValues, 'links'))
          <div class="d-flex justify-content-center">
            {{ $attributeValues->links() }}
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
  $('#attributeValuesTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush