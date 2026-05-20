@extends('admin.layouts.master_layout')

@section('pageTitle', 'Cross Sell Product Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Cross Sell Product Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.cross-sell-products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          @if (Route::has('admin.cross_sell_products.edit'))
<a href="{{ route('admin.cross_sell_products.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Cross Sell Product
          </a>
@endif
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-borderless">
              <tr>
                <th width="150">ID:</th>
                <td>{{ $item->id ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Product:</th>
                <td>{{ $item->product ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Cross Sell Product:</th>
                <td>{{ $item->cross_sell_product ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Sort Order:</th>
                <td>{{ $item->sort_order ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Status:</th>
                <td>{{ $item->status ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              @if (Route::has('admin.cross_sell_products.edit'))
<a href="{{ route('admin.cross_sell_products.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Cross Sell Product
              </a>
@endif
              <form action="{{ route('admin.cross-sell-products.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Cross Sell Product
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection