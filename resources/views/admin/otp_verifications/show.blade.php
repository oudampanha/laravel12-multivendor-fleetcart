@extends('admin.layouts.master_layout')

@section('pageTitle', 'OTP Verification Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">OTP Verification Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.otp-verifications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th width="30%">ID</th>
                  <td>{{ $otpVerification->id }}</td>
                </tr>
                <tr>
                  <th>Email</th>
                  <td>{{ $otpVerification->email }}</td>
                </tr>
                <tr>
                  <th>OTP Code</th>
                  <td><code>{{ $otpVerification->otp }}</code></td>
                </tr>
                <tr>
                  <th>Expires At</th>
                  <td>{{ $otpVerification->expires_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                  <th>Status</th>
                  <td>
                    @if($otpVerification->is_used)
                      <span class="badge badge-success">Used</span>
                    @else
                      @if($otpVerification->expires_at->isPast())
                        <span class="badge badge-danger">Expired</span>
                      @else
                        <span class="badge badge-warning">Pending</span>
                      @endif
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Created At</th>
                  <td>{{ $otpVerification->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                  <th>Updated At</th>
                  <td>{{ $otpVerification->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>OTP Status Information</h5>
              </div>
              <div class="card-body">
                @if($otpVerification->is_used)
                  <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    This OTP has been successfully used.
                  </div>
                @elseif($otpVerification->expires_at->isPast())
                  <div class="alert alert-danger">
                    <i class="fas fa-times-circle"></i>
                    This OTP has expired and can no longer be used.
                  </div>
                @else
                  <div class="alert alert-warning">
                    <i class="fas fa-clock"></i>
                    This OTP is still valid and can be used.
                  </div>
                @endif
                
                <p><strong>Time Remaining:</strong> 
                  @if($otpVerification->expires_at->isFuture())
                    {{ $otpVerification->expires_at->diffForHumans() }}
                  @else
                    Expired {{ $otpVerification->expires_at->diffForHumans() }}
                  @endif
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection