@extends('admin.layouts.master_layout')

@section('pageTitle', 'New Stock Take')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">New Stock Take</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.stock-takes.store') }}">
        @csrf
        <p class="text-muted">Creating a stock take will snapshot the current quantities for every product in the selected warehouse. You can then enter the actual counted quantities and complete it to auto-generate an adjustment.</p>
        <div class="row">
          <div class="col-md-6"><div class="form-group">
            <label>Warehouse <span class="text-danger">*</span></label>
            <select name="warehouse_id" class="form-control" required>
              <option value="">Select warehouse</option>
              @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
            </select>
          </div></div>
          <div class="col-md-6"><div class="form-group">
            <label>Count Date <span class="text-danger">*</span></label>
            <input type="date" name="count_date" class="form-control" value="{{ now()->toDateString() }}" required>
          </div></div>
        </div>
        <div class="form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
        <button class="btn btn-primary">Create &amp; Snapshot Stock</button>
        <a href="{{ route('admin.stock-takes.index') }}" class="btn btn-secondary">Cancel</a>
      </form>
    </div>
  </div>
@endsection
