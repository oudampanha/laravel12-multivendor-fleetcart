@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create Product Attribute')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create Product Attribute</h4>
        <div class="card-tools">
          <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.product-attributes.store') }}" method="POST">
          @csrf

          <div class="form-group">
            <label for="product_id">Product</label>
            <select class="form-control @error('product_id') is-invalid @enderror" id="product_id" name="product_id">
              <option value="">-- Select Product --</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                  {{ $product->getTranslation('name') ?? $product->id }}
                </option>
              @endforeach
            </select>
            @error('product_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="attribute_id">Attribute</label>
            <select class="form-control @error('attribute_id') is-invalid @enderror" id="attribute_id" name="attribute_id">
              <option value="">-- Select Attribute --</option>
              @foreach($attributes as $attribute)
                <option value="{{ $attribute->id }}" {{ old('attribute_id') == $attribute->id ? 'selected' : '' }}>
                  {{ $attribute->slug }}
                </option>
              @endforeach
            </select>
            @error('attribute_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="attribute_value_ids">Attribute Values</label>
            <select class="form-control @error('attribute_value_ids') is-invalid @enderror" id="attribute_value_ids" name="attribute_value_ids[]" multiple>
              @foreach($attributeValues as $value)
                <option value="{{ $value->id }}" {{ collect(old('attribute_value_ids', []))->contains($value->id) ? 'selected' : '' }}>
                  {{ $value->value }}
                </option>
              @endforeach
            </select>
            @error('attribute_value_ids')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Save
            </button>
            <a href="{{ route('admin.product-attributes.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
