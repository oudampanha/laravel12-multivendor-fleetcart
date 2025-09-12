@extends('admin.layouts.master_layout')

@section('pageTitle', 'OTP Verifications')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">OTP Verifications</h4>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="otpVerificationsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Email</th>
                <th>OTP</th>
                <th>Expires At</th>
                <th>Is Used</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($otpVerifications as $otp)
              <tr>
                <td>{{ $otp->id }}</td>
                <td>{{ $otp->email }}</td>
                <td>{{ $otp->otp }}</td>
                <td>{{ $otp->expires_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  @if($otp->is_used)
                    <span class="badge badge-success">Used</span>
                  @else
                    <span class="badge badge-warning">Unused</span>
                  @endif
                </td>
                <td>{{ $otp->created_at->format('Y-m-d H:i:s') }}</td>
                <td>
                  <a href="{{ route('admin.otp-verifications.show', $otp->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-eye"></i> View
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">No OTP verifications found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if(method_exists($otpVerifications, 'links'))
          <div class="d-flex justify-content-center">
            {{ $otpVerifications->links() }}
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
  $('#otpVerificationsTable').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush