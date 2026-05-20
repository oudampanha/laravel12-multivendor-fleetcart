@extends('admin.layouts.master_layout')

@section('pageTitle', 'New Goods Receipt')

@section('content')
  <div class="card">
    <div class="card-header"><h4 class="card-title">New Goods Receipt</h4></div>
    <div class="card-body">
      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <form method="POST" action="{{ route('admin.goods-receipts.store') }}">
        @include('admin.goods_receipts._form')
        <div class="mt-3">
          <button class="btn btn-primary">Save Draft</button>
          <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
@endsection
