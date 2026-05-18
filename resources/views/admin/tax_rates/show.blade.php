@extends('admin.layouts.master_layout')

@section('pageTitle', 'Tax Rate Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Tax Rate Details: {{ $taxRate->id }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.tax-rates.edit', $taxRate->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.tax-rates.index') }}" class="btn btn-secondary">
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
                  <td>{{ $taxRate->id }}</td>
                </tr>
                <tr>
                  <th>Tax Class</th>
                  <td>
                    @if($taxRate->taxClass)
                      {{ ucfirst(str_replace('_', ' ', $taxRate->taxClass->based_on)) }}
                    @else
                      <span class="text-muted">N/A</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>Rate</th>
                  <td><strong class="text-success">{{ $taxRate->rate }}%</strong></td>
                </tr>
                <tr>
                  <th>Country</th>
                  <td>{{ $taxRate->country }}</td>
                </tr>
                <tr>
                  <th>State/Province</th>
                  <td>{{ $taxRate->state ?: 'Any' }}</td>
                </tr>
                <tr>
                  <th>City</th>
                  <td>{{ $taxRate->city ?: 'Any' }}</td>
                </tr>
                <tr>
                  <th>ZIP/Postal Code</th>
                  <td>{{ $taxRate->zip ?: 'Any' }}</td>
                </tr>
                <tr>
                  <th>Priority Position</th>
                  <td>
                    <span class="badge badge-info">{{ $taxRate->position }}</span>
                    <small class="text-muted ml-2">Lower numbers have higher priority</small>
                  </td>
                </tr>
                <tr>
                  <th>Created At</th>
                  <td>{{ $taxRate->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                  <th>Updated At</th>
                  <td>{{ $taxRate->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Tax Coverage</h5>
              </div>
              <div class="card-body">
                <p><strong>This tax rate applies to:</strong></p>
                <ul class="list-unstyled">
                  <li><i class="fas fa-globe text-primary"></i> <strong>Country:</strong> {{ $taxRate->country == '*' ? 'All Countries' : $taxRate->country }}</li>
                  <li><i class="fas fa-map-marker-alt text-info"></i> <strong>State:</strong> {{ $taxRate->state == '*' || !$taxRate->state ? 'All States' : $taxRate->state }}</li>
                  <li><i class="fas fa-city text-warning"></i> <strong>City:</strong> {{ $taxRate->city == '*' || !$taxRate->city ? 'All Cities' : $taxRate->city }}</li>
                  <li><i class="fas fa-mail-bulk text-secondary"></i> <strong>ZIP:</strong> {{ $taxRate->zip == '*' || !$taxRate->zip ? 'All ZIP Codes' : $taxRate->zip }}</li>
                </ul>
                
                <div class="mt-4">
                  <h6>Tax Calculation</h6>
                  <div class="alert alert-info">
                    <i class="fas fa-calculator"></i>
                    For a $100 item, the tax would be <strong>${{ number_format(100 * $taxRate->rate / 100, 2) }}</strong>
                  </div>
                </div>
              </div>
            </div>
            
            @if($taxRate->taxClass && $taxRate->taxClass->taxRates && $taxRate->taxClass->taxRates->count() > 1)
            <div class="card mt-3">
              <div class="card-header">
                <h5>Other Rates in Same Tax Class</h5>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Country</th>
                        <th>State</th>
                        <th>Rate</th>
                        <th>Position</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($taxRate->taxClass->taxRates->where('id', '!=', $taxRate->id) as $otherRate)
                      <tr>
                        <td>{{ $otherRate->country }}</td>
                        <td>{{ $otherRate->state ?: 'Any' }}</td>
                        <td>{{ $otherRate->rate }}%</td>
                        <td>{{ $otherRate->position }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection