@extends('admin.layouts.master_layout')

@section('pageTitle', 'Product Attributes Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Product Attributes</h4>
        <div class="card-tools">
          <a href="{{ route('admin.product-attributes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product Attribute
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="productAttributesTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Attribute</th>
                <th>Values</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($productAttributes as $productAttribute)
              <tr>
                <td>{{ $productAttribute->id }}</td>
                <td>{{ optional($productAttribute->product)->getTranslation('name') ?? optional($productAttribute->product)->id ?? 'N/A' }}</td>
                <td>{{ optional($productAttribute->attribute)->slug ?? 'N/A' }}</td>
                <td>
                  @foreach($productAttribute->attributeValues as $value)
                    <span class="badge badge-info">{{ $value->value }}</span>
                  @endforeach
                </td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.product-attributes.show', $productAttribute->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.product-attributes.edit', $productAttribute->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.product-attributes.destroy', $productAttribute->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>

        @if(method_exists($productAttributes, 'links'))
          <div class="d-flex justify-content-center">
            {{ $productAttributes->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
