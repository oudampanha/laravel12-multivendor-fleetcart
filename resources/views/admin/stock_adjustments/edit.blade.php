@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit Stock Adjustment')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">Edit Adjustment: {{ $stockAdjustment->code }}</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.stock-adjustments.update', $stockAdjustment) }}">
        @method('PUT')
        @include('admin.stock_adjustments._form')
        <div class="mt-3">
          <button class="btn btn-primary">Update</button>
          <a href="{{ route('admin.stock-adjustments.show', $stockAdjustment) }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
