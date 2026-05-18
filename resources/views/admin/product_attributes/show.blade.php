@extends('admin.layouts.master_layout')

@section('pageTitle', 'Product Attribute Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Product Attribute Details</h4>
        <div class="card-tools">
          <a href="{{ route('admin.product-attributes.edit', $productAttribute->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <tr>
            <th style="width: 200px;">ID</th>
            <td>{{ $productAttribute->id }}</td>
          </tr>
          <tr>
            <th>Product</th>
            <td>{{ optional($productAttribute->product)->getTranslation('name') ?? optional($productAttribute->product)->id ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Attribute</th>
            <td>{{ optional($productAttribute->attribute)->slug ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Values</th>
            <td>
              @foreach($productAttribute->attributeValues as $value)
                <span class="badge badge-info">{{ $value->value }}</span>
              @endforeach
            </td>
          </tr>
          <tr>
            <th>Created At</th>
            <td>{{ $productAttribute->created_at }}</td>
          </tr>
          <tr>
            <th>Updated At</th>
            <td>{{ $productAttribute->updated_at }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
