@extends('admin.layouts.master_layout')

@section('pageTitle', 'Order Product Option Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Order Product Option Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.order-product-options.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          <a href="{{ route('admin.order-product-options.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Order Product Option
          </a>
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
                <th>Order Product:</th>
                <td>{{ $item->order_product ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Option:</th>
                <td>{{ $item->option ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Option Value:</th>
                <td>{{ $item->option_value ?? 'N/A' }}</td>
              </tr>
              <tr>
                <th>Price:</th>
                <td>{{ $item->price ?? 'N/A' }}</td>
              </tr>
            </table>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-12">
            <div class="btn-group">
              <a href="{{ route('admin.order-product-options.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Order Product Option
              </a>
              <form action="{{ route('admin.order-product-options.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Order Product Option
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