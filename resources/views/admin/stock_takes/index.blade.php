@extends('admin.layouts.master_layout')

@section('pageTitle', 'Stock Takes')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between">
      <h4 class="card-title mb-0">Stock Takes (Cycle Counts)</h4>
      <a href="{{ route('admin.stock-takes.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Cycle Count</a>
    </div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-3">
          <select name="status" class="form-control">
            <option value="">Any status</option>
            @foreach(['draft','in_progress','completed','cancelled'] as $s)
              <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table" id="stockTakesTable">
          <thead><tr><th>Code</th><th>Date</th><th>Warehouse</th><th class="text-end">Items</th><th>Status</th><th>Created</th><th></th></tr></thead>
          <tbody>
            @forelse($stockTakes as $t)
              <tr>
                <td><code>{{ $t->code }}</code></td>
                <td>{{ $t->count_date->format('Y-m-d') }}</td>
                <td>{{ optional($t->warehouse)->name }}</td>
                <td class="text-end">{{ $t->items_count }}</td>
                <td><span class="badge badge-secondary">{{ $t->status }}</span></td>
                <td>{{ optional($t->creator)->name ?? '-' }}</td>
                <td>
                  <a href="{{ route('admin.stock-takes.show', $t) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                  @if(!$t->isCompleted())<a href="{{ route('admin.stock-takes.edit', $t) }}" class="btn btn-sm btn-warning"><i class="fas fa-clipboard-list"></i></a>@endif
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($stockTakes, 'links')) {{ $stockTakes->links() }} @endif
    </div>
  </div>
@endsection
