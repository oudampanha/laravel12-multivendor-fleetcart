@extends('admin.layouts.master_layout')

@section('pageTitle', 'Supplier Details')

@section('content')
  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">{{ $supplier->name }} <small class="text-muted">({{ $supplier->code }})</small></h4>
      <div>
        <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">@if($supplier->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif</dd>
        <dt class="col-sm-3">Contact</dt><dd class="col-sm-9">{{ $supplier->contact_person }} · {{ $supplier->phone }} · {{ $supplier->email }}</dd>
        <dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $supplier->address }}, {{ $supplier->city }}, {{ $supplier->state }}, {{ $supplier->country }} {{ $supplier->zip }}</dd>
        <dt class="col-sm-3">Tax Number</dt><dd class="col-sm-9">{{ $supplier->tax_number ?: '-' }}</dd>
        <dt class="col-sm-3">Payment Terms</dt><dd class="col-sm-9">{{ $supplier->payment_terms ?: '-' }}</dd>
      </dl>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5>Recent Purchase Orders</h5></div>
    <div class="card-body table-responsive">
      <table class="table table-sm">
        <thead><tr><th>Code</th><th>Date</th><th>Status</th><th class="text-end">Total</th></tr></thead>
        <tbody>
          @forelse($supplier->purchaseOrders as $po)
            <tr>
              <td><a href="{{ route('admin.purchase-orders.show', $po) }}">{{ $po->code }}</a></td>
              <td>{{ $po->order_date->format('Y-m-d') }}</td>
              <td><span class="badge badge-secondary">{{ $po->status }}</span></td>
              <td class="text-end">{{ number_format((float) $po->total_amount, 2) }} {{ $po->currency_code }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">No purchase orders yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
