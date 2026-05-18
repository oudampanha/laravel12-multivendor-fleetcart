@extends('admin.layouts.master_layout')

@section('pageTitle', 'Tax Class Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Tax Class Details: {{ $taxClass->id }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.tax-classes.edit', $taxClass->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.tax-classes.index') }}" class="btn btn-secondary">
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
                  <td>{{ $taxClass->id }}</td>
                </tr>
                <tr>
                  <th>Based On</th>
                  <td>{{ ucfirst(str_replace('_', ' ', $taxClass->based_on)) }}</td>
                </tr>
                <tr>
                  <th>Tax Rates Count</th>
                  <td>
                    <span class="badge badge-info">{{ $taxClass->tax_rates_count ?? 0 }}</span>
                  </td>
                </tr>
                <tr>
                  <th>Created At</th>
                  <td>{{ $taxClass->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                  <th>Updated At</th>
                  <td>{{ $taxClass->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-md-6">
            @if(isset($taxClass->taxRates) && $taxClass->taxRates->count() > 0)
            <div class="card">
              <div class="card-header">
                <h5>Tax Rates ({{ $taxClass->taxRates->count() }})</h5>
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
                      @foreach($taxClass->taxRates as $taxRate)
                      <tr>
                        <td>{{ $taxRate->country }}</td>
                        <td>{{ $taxRate->state }}</td>
                        <td>{{ $taxRate->rate }}%</td>
                        <td>{{ $taxRate->position }}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            @else
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i>
              No tax rates defined for this tax class.
            </div>
            @endif
            
            @if(isset($taxClass->products) && $taxClass->products->count() > 0)
            <div class="card mt-3">
              <div class="card-header">
                <h5>Products Using This Tax Class ({{ $taxClass->products->count() }})</h5>
              </div>
              <div class="card-body">
                @foreach($taxClass->products->take(10) as $product)
                <div class="mb-2">
                  <strong>{{ $product->name ?? 'Product #' . $product->id }}</strong><br>
                  <small class="text-muted">{{ $product->slug }}</small>
                </div>
                @endforeach
                @if($taxClass->products->count() > 10)
                <small class="text-muted">... and {{ $taxClass->products->count() - 10 }} more products</small>
                @endif
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