@extends('admin.layouts.master_layout')

@section('pageTitle', 'Entity Media Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">
          Entity Media &mdash;
          <small class="text-muted">{{ $entityType ?? '' }} #{{ $entityId ?? '' }}</small>
        </h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="dataTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>File</th>
                <th>Original name</th>
                <th>Zone</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($entityMedia ?? [] as $item)
              <tr>
                <td>{{ $item->id }}</td>
                <td>
                  @if ($item->file && $item->file->file_url)
                    <a href="{{ $item->file->file_url }}" target="_blank">
                      {{ $item->file->file_name ?? 'file' }}
                    </a>
                  @else
                    N/A
                  @endif
                </td>
                <td>{{ optional($item->file)->original_name ?? 'N/A' }}</td>
                <td>{{ $item->zone ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group">
                    <form action="{{ route('admin.entity-media.destroy', $item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Detach this media from the entity?')">
                        <i class="fas fa-trash"></i> Detach
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center text-muted">No media attached to this entity.</td>
              </tr>
              @endforelse
            </tbody>
          </table>

        @if (! empty($entityMedia) && method_exists($entityMedia, 'links'))
          {{ $entityMedia->links() }}
        @endif
        </div>
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
  $('#dataTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush