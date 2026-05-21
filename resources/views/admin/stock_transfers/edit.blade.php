@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Stock Transfer')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">Edit Transfer: {{ $stockTransfer->code }}</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.stock-transfers.update', $stockTransfer) }}">
        @method('PUT')
        @include('admin.stock_transfers._form')
        <div class="mt-3">
          <button class="btn btn-primary">Update</button>
          <a href="{{ route('admin.stock-transfers.show', $stockTransfer) }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
