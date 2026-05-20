@extends('admin.layouts.master_layout')

@section('pageTitle', 'New Stock Transfer')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">New Stock Transfer</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.stock-transfers.store') }}">
        @include('admin.stock_transfers._form')
        <div class="mt-3">
          <button class="btn btn-primary">Save Draft</button>
          <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
