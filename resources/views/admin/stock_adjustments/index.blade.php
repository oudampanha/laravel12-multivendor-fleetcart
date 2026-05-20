@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Adjustments')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Stock Adjustments</h4>
      <a href="{{ route('admin.stock-adjustments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Adjustment</a>
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
        <div class="col-md-3">
          <select name="warehouse_id" class="form-control">
            <option value="">Any warehouse</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}" @selected(request('warehouse_id') == $w->id)>{{ $w->name }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="reason" class="form-control">
            <option value="">Any reason</option>
            @foreach(\App\Models\StockAdjustment::REASONS as $r)
              <option value="{{ $r }}" @selected(request('reason') == $r)>{{ ucfirst($r) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table" id="adjustmentsTable">
          <thead>
            <tr><th>Code</th><th>Date</th><th>Warehouse</th><th>Reason</th><th>Items</th><th>Status</th><th>Created</th><th></th></tr>
          </thead>
          <tbody>
            @forelse($adjustments as $a)
              <tr>
                <td><code>{{ $a->code }}</code></td>
                <td>{{ $a->adjustment_date->format('Y-m-d') }}</td>
                <td>{{ optional($a->warehouse)->name }}</td>
                <td>{{ ucfirst($a->reason) }}</td>
                <td>{{ $a->items_count }}</td>
                <td><span class="badge badge-secondary">{{ $a->status }}</span></td>
                <td>{{ optional($a->creator)->name ?? '-' }}</td>
                <td>
                  <a href="{{ route('admin.stock-adjustments.show', $a) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                  @if($a->isDraft())
                    <a href="{{ route('admin.stock-adjustments.edit', $a) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                  @endif
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($adjustments, 'links')) {{ $adjustments->links() }} @endif
    </div>
  </div>
@endsection
