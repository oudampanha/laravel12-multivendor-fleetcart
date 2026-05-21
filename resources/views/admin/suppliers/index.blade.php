@extends('admin.layouts.master_layout')

@section('pageTitle', 'Suppliers')

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="card-title mb-0">Suppliers</h4>
      <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Supplier</a>
    </div>
    <div class="card-body">
      <form method="GET" class="row mb-3">
        <div class="col-md-4"><input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search code, name, email..."></div>
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
        <table class="table table-striped" id="suppliersTable">
          <thead><tr><th>Code</th><th>Name</th><th>Contact</th><th>Phone</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
            @forelse($suppliers as $s)
              <tr>
                <td><code>{{ $s->code }}</code></td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->contact_person ?? '-' }}</td>
                <td>{{ $s->phone ?? '-' }}</td>
                <td>{{ $s->email ?? '-' }}</td>
                <td>@if($s->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif</td>
                <td>
                  <a href="{{ route('admin.suppliers.show', $s) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                  <a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                  <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this supplier?')"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
      @if(method_exists($suppliers, 'links')) {{ $suppliers->links() }} @endif
    </div>
  </div>
@endsection
