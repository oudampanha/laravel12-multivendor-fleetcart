@extends('admin.layouts.master_layout')

@section('pageTitle', 'User Activations Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">User Activations Management</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="activationsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Code</th>
                <th>Completed</th>
                <th>Completed At</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activations as $activation)
              <tr>
                <td>{{ $activation->id }}</td>
                <td>
                  <a href="{{ route('admin.users.show', $activation->user_id) }}" class="text-primary">
                    {{ $activation->user->first_name ?? 'N/A' }} {{ $activation->user->last_name ?? '' }}
                  </a>
                </td>
                <td><code>{{ $activation->code }}</code></td>
                <td>
                  @if($activation->completed)
                    <span class="badge badge-success">Completed</span>
                  @else
                    <span class="badge badge-warning">Pending</span>
                  @endif
                </td>
                <td>
                  @if($activation->completed_at)
                    {{ $activation->completed_at->format('Y-m-d H:i:s') }}
                  @else
                    <span class="text-muted">Not completed</span>
                  @endif
                </td>
                <td>{{ $activation->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.activations.show', $activation->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <form action="{{ route('admin.activations.destroy', $activation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this activation?')">
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
                <td colspan="7" class="text-center">No activations found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($activations, 'links'))
          <div class="d-flex justify-content-center">
            {{ $activations->links() }}
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
  $('#activationsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush