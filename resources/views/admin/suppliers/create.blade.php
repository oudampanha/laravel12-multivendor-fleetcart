@extends('admin.layouts.master_layout')

@section('pageTitle', 'Add Supplier')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">Add Supplier</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.suppliers.store') }}">
        @include('admin.suppliers._form')
        <div class="mt-3">
          <button class="btn btn-primary">Save</button>
          <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
