@extends('admin.layouts.master_layout')

@section('pageTitle', 'Warehouses')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">Warehouses</h4>
      <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Warehouse</a>
    </div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search code, name, city..."></div>
        <div class="col-md-3">
          <select name="is_active" class="form-control">
            <option value="">Any status</option>
            <option value="1" @selected(request('is_active') === '1')>Active</option>
            <option value="0" @selected(request('is_active') === '0')>Inactive</option>
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-secondary w-100">Filter</button></div>
      </form>
      <div class="table-responsive">
        <table class="table table-striped" id="warehousesTable">
          <thead>
            <tr><th>Code</th><th>Name</th><th>City</th><th>SKUs</th><th>Default</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($warehouses as $w)
              <tr>
                <td><code>{{ $w->code }}</code></td>
                <td>{{ $w->name }}</td>
                <td>{{ $w->city ?? '-' }}</td>
                <td>{{ $w->stocks_count }}</td>
                <td>@if($w->is_default)<span class="badge badge-info">Default</span>@endif</td>
                <td>
                  @if($w->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.warehouses.show', $w) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                  <a href="{{ route('admin.warehouses.edit', $w) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.warehouses.destroy', $w) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this warehouse?')"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($warehouses, 'links')) {{ $warehouses->links() }} @endif
    </div>
  </div>
@endsection
