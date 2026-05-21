@extends('admin.layouts.master_layout')

@section('pageTitle', 'Related Product Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Related Product Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.related-products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
          @if (Route::has('admin.related_products.edit'))
<a href="{{ route('admin.related_products.edit', $item->id ?? 0) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Related Product
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
                <th>Related Product:</th>
                <td>{{ $item->related_product ?? 'N/A' }}</td>
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
              @if (Route::has('admin.related_products.edit'))
<a href="{{ route('admin.related_products.edit', $item->id ?? 0) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Related Product
              </a>
@endif
              <form action="{{ route('admin.related-products.destroy', $item->id ?? 0) }}" method="POST" class="d-inline ml-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                  <i class="fas fa-trash"></i> Delete Related Product
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