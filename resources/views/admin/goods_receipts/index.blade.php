@extends('admin.layouts.master_layout')

@section('pageTitle', 'Goods Receipts')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Goods Receipts (GRN)</h4>
      <a href="{{ route('admin.goods-receipts.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Receipt</a>
    </div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-3">
          <select name="status" class="form-control">
            <option value="">Any status</option>
            @foreach(['draft','posted','cancelled'] as $s)
              <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table" id="goodsReceiptsTable">
          <thead><tr><th>Code</th><th>Date</th><th>PO</th><th>Supplier</th><th>Warehouse</th><th class="text-end">Items</th><th>Status</th><th></th></tr></thead>
          <tbody>
            @forelse($goodsReceipts as $g)
              <tr>
                <td><code>{{ $g->code }}</code></td>
                <td>{{ $g->receipt_date->format('Y-m-d') }}</td>
                <td>{{ $g->purchaseOrder ? $g->purchaseOrder->code : '-' }}</td>
                <td>{{ optional($g->supplier)->name }}</td>
                <td>{{ optional($g->warehouse)->name }}</td>
                <td class="text-end">{{ $g->items_count }}</td>
                <td><span class="badge badge-secondary">{{ $g->status }}</span></td>
                <td>
                  <a href="{{ route('admin.goods-receipts.show', $g) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                  @if($g->isDraft())<a href="{{ route('admin.goods-receipts.edit', $g) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>@endif
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($goodsReceipts, 'links')) {{ $goodsReceipts->links() }} @endif
    </div>
  </div>
@endsection
