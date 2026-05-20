@extends('admin.layouts.master_layout')

@section('pageTitle', 'Purchase Orders')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Purchase Orders</h4>
      <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New PO</a>
    </div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-3">
          <select name="status" class="form-control">
            <option value="">Any status</option>
            @foreach(['draft','sent','confirmed','partial','received','cancelled'] as $s)
              <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="supplier_id" class="form-control">
            <option value="">Any supplier</option>
            @foreach($suppliers as $s)<option value="{{ $s->id }}" @selected(request('supplier_id') == $s->id)>{{ $s->name }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="warehouse_id" class="form-control">
            <option value="">Any warehouse</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table" id="purchaseOrdersTable">
          <thead><tr><th>Code</th><th>Date</th><th>Supplier</th><th>Warehouse</th><th class="text-end">Items</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
          <tbody>
            @forelse($purchaseOrders as $po)
              <tr>
                <td><code>{{ $po->code }}</code></td>
                <td>{{ $po->order_date->format('Y-m-d') }}</td>
                <td>{{ optional($po->supplier)->name }}</td>
                <td>{{ optional($po->warehouse)->name }}</td>
                <td class="text-end">{{ $po->items_count }}</td>
                <td class="text-end">{{ number_format((float) $po->total_amount, 2) }} {{ $po->currency_code }}</td>
                <td><span class="badge badge-secondary">{{ $po->status }}</span></td>
                <td>
                  <a href="{{ route('admin.purchase-orders.show', $po) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                  @if($po->isEditable())<a href="{{ route('admin.purchase-orders.edit', $po) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>@endif
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($purchaseOrders, 'links')) {{ $purchaseOrders->links() }} @endif
    </div>
  </div>
@endsection
