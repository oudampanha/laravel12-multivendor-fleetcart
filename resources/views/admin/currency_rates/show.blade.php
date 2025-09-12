@extends('admin.layouts.master_layout')

@section('pageTitle', 'Currency Rate Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Currency Rate Details: {{ $currencyRate->currency_name }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.currency_rates.edit', $currencyRate->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.currency_rates.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Rate Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ $currencyRate->id }}</td>
                    </tr>
                    <tr>
                      <th>Currency Name</th>
                      <td><strong>{{ $currencyRate->currency_name }}</strong></td>
                    </tr>
                    <tr>
                      <th>Currency Code</th>
                      <td><code>{{ $currencyRate->currency_code }}</code></td>
                    </tr>
                    <tr>
                      <th>Currency Symbol</th>
                      <td>
                        @if($currencyRate->currency_symbol)
                          <span class="badge badge-info">{{ $currencyRate->currency_symbol }}</span>
                        @else
                          <span class="text-muted">Not set</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Exchange Rate</th>
                      <td>
                        <strong class="text-primary">{{ number_format($currencyRate->exchange_rate, 4) }}</strong>
                      </td>
                    </tr>
                    <tr>
                      <th>Base Currency</th>
                      <td><code>{{ $currencyRate->base_currency ?? 'USD' }}</code></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if($currencyRate->is_active)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ $currencyRate->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ $currencyRate->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>Conversion Examples</h5>
              </div>
              <div class="card-body">
                <div class="list-group">
                  <div class="list-group-item d-flex justify-content-between">
                    <span>1 {{ $currencyRate->base_currency ?? 'USD' }}</span>
                    <strong>{{ number_format($currencyRate->exchange_rate, 4) }} {{ $currencyRate->currency_code }}</strong>
                  </div>
                  <div class="list-group-item d-flex justify-content-between">
                    <span>10 {{ $currencyRate->base_currency ?? 'USD' }}</span>
                    <strong>{{ number_format($currencyRate->exchange_rate * 10, 2) }} {{ $currencyRate->currency_code }}</strong>
                  </div>
                  <div class="list-group-item d-flex justify-content-between">
                    <span>100 {{ $currencyRate->base_currency ?? 'USD' }}</span>
                    <strong>{{ number_format($currencyRate->exchange_rate * 100, 2) }} {{ $currencyRate->currency_code }}</strong>
                  </div>
                  <div class="list-group-item d-flex justify-content-between">
                    <span>1000 {{ $currencyRate->base_currency ?? 'USD' }}</span>
                    <strong>{{ number_format($currencyRate->exchange_rate * 1000, 2) }} {{ $currencyRate->currency_code }}</strong>
                  </div>
                </div>
              </div>
            </div>
            
            @if(isset($currencyRate->last_sync_at))
            <div class="alert alert-info mt-3">
              <i class="fas fa-info-circle"></i>
              <strong>Last Synchronized:</strong> {{ $currencyRate->last_sync_at->format('Y-m-d H:i:s') }}
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection