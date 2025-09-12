@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create Product')

@section('content')
  <div class="row">
    <div class="col-12">
      <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="mb-0">Create Product</h4>
          <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save & Exit</button>
          </div>
        </div>

        <div class="row">
          <!-- Left Column - Main Content -->
          <div class="col-md-8">

            <!-- General Tab -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">General</h5>
              </div>
              <div class="card-body">
                <!-- Name -->
                <div class="form-group mb-3">
                  <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    name="name" value="{{ old('name') }}" required>
                  @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Description -->
                <div class="form-group mb-3">
                  <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                  <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                    rows="8">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Brand -->
                <div class="form-group mb-3">
                  <label for="brand_id" class="form-label">Brand</label>
                  <select class="form-control @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                    <option value="">Please Select</option>
                    @foreach ($brands as $brand)
                      <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('brand_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Categories -->
                <div class="form-group mb-3">
                  <label for="categories" class="form-label">Categories</label>
                  <select class="form-control @error('categories') is-invalid @enderror" id="categories"
                    name="categories[]" multiple>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}"
                        {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                        {{ $category->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('categories')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Tax Class -->
                <div class="form-group mb-3">
                  <label for="tax_class_id" class="form-label">Tax Class</label>
                  <select class="form-control @error('tax_class_id') is-invalid @enderror" id="tax_class_id"
                    name="tax_class_id">
                    <option value="">Please Select</option>
                    @foreach ($taxClasses as $taxClass)
                      <option value="{{ $taxClass->id }}" {{ old('tax_class_id') == $taxClass->id ? 'selected' : '' }}>
                        {{ $taxClass->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('tax_class_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Tags -->
                <div class="form-group mb-3">
                  <label for="tags" class="form-label">Tags</label>
                  <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags"
                    name="tags" value="{{ old('tags') }}" placeholder="Enter tags separated by commas">
                  @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Virtual Product Checkbox -->
                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="is_virtual" name="is_virtual" value="1"
                    {{ old('is_virtual') ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_virtual">
                    The product won't be shipped
                  </label>
                </div>

                <!-- Status -->
                <div class="form-check mb-3">
                  <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    Enable the product
                  </label>
                </div>
              </div>
            </div>

            <!-- Attributes Section -->
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Attributes</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addAttributeBtn">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
              <div class="card-body">
                <div id="attributesContainer">
                  <!-- Attributes will be dynamically added here -->
                  <div class="row mb-3 attribute-row">
                    <div class="col-md-5">
                      <select class="form-control" name="attributes[0][name]">
                        <option value="">Please Select</option>
                        <!-- Add attribute options here -->
                      </select>
                    </div>
                    <div class="col-md-5">
                      <input type="text" class="form-control" name="attributes[0][value]" placeholder="Values">
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="btn btn-sm btn-outline-danger remove-attribute">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="addAttribute">Add Attribute</button>
              </div>
            </div>

            <!-- Variations Section -->
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Variations</h5>
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addVariationBtn">
                    <i class="fas fa-plus"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="generateVariationsBtn">
                    <i class="fas fa-cogs"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i> Please add some variations to generate variants.
                </div>
                <div id="variationsContainer">
                  <!-- Variations will be dynamically added here -->
                  <div class="row mb-3 variation-row">
                    <div class="col-md-5">
                      <input type="text" class="form-control" name="variations[0][name]" placeholder="Name">
                    </div>
                    <div class="col-md-2">
                      <select class="form-control" name="variations[0][type]">
                        <option value="">Please Select</option>
                        <option value="text">Text</option>
                        <option value="color">Color</option>
                        <option value="image">Image</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <button type="button" class="btn btn-sm btn-outline-secondary">Select Options</button>
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="btn btn-sm btn-outline-danger remove-variation">
                        <i class="fas fa-trash"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-secondary move-variation-up">
                        <i class="fas fa-arrow-up"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="addVariation">Add Variation</button>
              </div>
            </div>

            <!-- Variants Section -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">Variants</h5>
              </div>
              <div class="card-body">
                <div class="alert alert-info">
                  <i class="fas fa-info-circle"></i> Please add some variations to generate variants.
                </div>
              </div>
            </div>

            <!-- Options Section -->
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Options</h5>
                <button type="button" class="btn btn-sm btn-outline-primary" id="addOptionBtn">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
              <div class="card-body">
                <div id="optionsContainer">
                  <!-- Options will be dynamically added here -->
                  <div class="row mb-3 option-row">
                    <div class="col-md-5">
                      <input type="text" class="form-control" name="options[0][name]" placeholder="Name">
                    </div>
                    <div class="col-md-2">
                      <select class="form-control" name="options[0][type]">
                        <option value="">Please Select</option>
                        <option value="text">Text</option>
                        <option value="select">Select</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="date">Date</option>
                        <option value="time">Time</option>
                        <option value="datetime">Date & Time</option>
                        <option value="file">File</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <button type="button" class="btn btn-sm btn-outline-secondary">Select Options</button>
                    </div>
                    <div class="col-md-2">
                      <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                        <i class="fas fa-trash"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-secondary move-option-up">
                        <i class="fas fa-arrow-up"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="addOption">Add Option</button>
              </div>
            </div>

          </div>

          <!-- Right Column - Sidebar -->
          <div class="col-md-4">
            <!-- Media -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">Media</h5>
              </div>
              <div class="card-body">
                <div class="product-media-container">
                  <!-- Main Featured Image -->
                  <div class="main-media-wrapper">
                    <div class="main-image-holder" id="mainImageHolder" data-media-picker>
                      <img src="{{ asset('assets/images/placeholder_image.png') }}" alt="Featured product image"
                        class="main-product-image" id="mainProductImage">
                      <div class="image-overlay">
                        <div class="overlay-content">
                          <i class="fas fa-camera mb-2"></i>
                          <span>Click to select image</span>
                        </div>
                      </div>
                      <button type="button" class="btn remove-main-image" id="removeMainImage" title="Remove image"
                        style="display: none;">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>

                  <!-- Thumbnail Grid - Hidden initially, shown after main image is loaded -->
                  <div class="media-thumbnails-grid" id="mediaThumbnailsGrid" style="display: none;">
                    <!-- Add New Image Placeholder -->
                    <div class="media-thumbnail-item add-new-media" data-media-picker-multiple>
                      <div class="thumbnail-holder add-media-placeholder">
                        <div class="add-media-content">
                          <i class="fas fa-plus text-muted mb-2"></i>
                          <span class="text-muted small">Add Images</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Hidden inputs to store selected media IDs -->
                <input type="hidden" name="featured_image" id="featuredImageInput" value="">
                <input type="hidden" name="gallery_images" id="galleryImagesInput" value="">
              </div>
            </div>

            <!-- Pricing -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">Pricing</h5>
              </div>
              <div class="card-body">
                <!-- Price -->
                <div class="form-group mb-3">
                  <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                      id="price" name="price" value="{{ old('price') }}" required>
                    @error('price')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Special Price -->
                <div class="form-group mb-3">
                  <label for="special_price" class="form-label">Special Price</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01"
                      class="form-control @error('special_price') is-invalid @enderror" id="special_price"
                      name="special_price" value="{{ old('special_price') }}">
                    @error('special_price')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Special Price Type -->
                <div class="form-group mb-3">
                  <label for="special_price_type" class="form-label">Special Price Type</label>
                  <select class="form-control @error('special_price_type') is-invalid @enderror" id="special_price_type"
                    name="special_price_type">
                    <option value="fixed" {{ old('special_price_type', 'fixed') == 'fixed' ? 'selected' : '' }}>Fixed
                    </option>
                    <option value="percent" {{ old('special_price_type') == 'percent' ? 'selected' : '' }}>Percent
                    </option>
                  </select>
                  @error('special_price_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Special Price Start -->
                <div class="form-group mb-3">
                  <label for="special_price_start" class="form-label">Special Price Start</label>
                  <input type="date" class="form-control @error('special_price_start') is-invalid @enderror"
                    id="special_price_start" name="special_price_start" value="{{ old('special_price_start') }}">
                  @error('special_price_start')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Special Price End -->
                <div class="form-group mb-3">
                  <label for="special_price_end" class="form-label">Special Price End</label>
                  <input type="date" class="form-control @error('special_price_end') is-invalid @enderror"
                    id="special_price_end" name="special_price_end" value="{{ old('special_price_end') }}">
                  @error('special_price_end')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Inventory -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">Inventory</h5>
              </div>
              <div class="card-body">
                <!-- SKU -->
                <div class="form-group mb-3">
                  <label for="sku" class="form-label">SKU</label>
                  <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku"
                    name="sku" value="{{ old('sku') }}">
                  @error('sku')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Inventory Management -->
                <div class="form-group mb-3">
                  <label for="inventory_management" class="form-label">Inventory Management</label>
                  <select class="form-control @error('manage_stock') is-invalid @enderror" id="inventory_management"
                    name="manage_stock">
                    <option value="0" {{ old('manage_stock', '1') == '0' ? 'selected' : '' }}>Don't track inventory
                    </option>
                    <option value="1" {{ old('manage_stock', '1') == '1' ? 'selected' : '' }}>Track inventory
                    </option>
                  </select>
                  @error('manage_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Stock Availability -->
                <div class="form-group mb-3" id="stockAvailabilityGroup">
                  <label for="stock_availability" class="form-label">Stock Availability</label>
                  <select class="form-control @error('in_stock') is-invalid @enderror" id="stock_availability"
                    name="in_stock">
                    <option value="1" {{ old('in_stock', '1') == '1' ? 'selected' : '' }}>In Stock</option>
                    <option value="0" {{ old('in_stock') == '0' ? 'selected' : '' }}>Out of Stock</option>
                  </select>
                  @error('in_stock')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Quantity -->
                <div class="form-group mb-3" id="quantityGroup">
                  <label for="qty" class="form-label">Quantity</label>
                  <input type="number" class="form-control @error('qty') is-invalid @enderror" id="qty"
                    name="qty" value="{{ old('qty', 0) }}" min="0">
                  @error('qty')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- SEO -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">SEO</h5>
              </div>
              <div class="card-body">
                <!-- URL -->
                <div class="form-group mb-3">
                  <label for="slug" class="form-label">URL</label>
                  <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug"
                    name="slug" value="{{ old('slug') }}" placeholder="Auto-generated from name">
                  @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Meta Title -->
                <div class="form-group mb-3">
                  <label for="meta_title" class="form-label">Meta Title</label>
                  <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title"
                    name="meta_title" value="{{ old('meta_title') }}">
                  @error('meta_title')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Meta Description -->
                <div class="form-group mb-3">
                  <label for="meta_description" class="form-label">Meta Description</label>
                  <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description"
                    name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                  @error('meta_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Additional -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">Additional</h5>
              </div>
              <div class="card-body">
                <!-- Vendor -->
                @if (auth()->user()->hasRole('admin'))
                  <div class="form-group mb-3">
                    <label for="vendor_id" class="form-label">Vendor</label>
                    <select class="form-control @error('vendor_id') is-invalid @enderror" id="vendor_id"
                      name="vendor_id">
                      <option value="">Admin Product</option>
                      @foreach ($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                          {{ $vendor->store_name }}
                        </option>
                      @endforeach
                    </select>
                    @error('vendor_id')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                @endif

                <!-- Short Description -->
                <div class="form-group mb-3">
                  <label for="short_description" class="form-label">Short Description</label>
                  <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description"
                    name="short_description" rows="3">{{ old('short_description') }}</textarea>
                  @error('short_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- New From -->
                <div class="form-group mb-3">
                  <label for="new_from" class="form-label">New From</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="date" class="form-control @error('new_from') is-invalid @enderror" id="new_from"
                      name="new_from" value="{{ old('new_from') }}">
                    @error('new_from')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- New To -->
                <div class="form-group mb-3">
                  <label for="new_to" class="form-label">New To</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="date" class="form-control @error('new_to') is-invalid @enderror" id="new_to"
                      name="new_to" value="{{ old('new_to') }}">
                    @error('new_to')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

              </div>
            </div>

            <!-- Linked Products -->
            <div class="card mb-4">
              <div class="card-header">
                <h5 class="card-title mb-0">Linked Products</h5>
              </div>
              <div class="card-body">
                <!-- Up-Sells -->
                <div class="form-group mb-3">
                  <label for="up_sells" class="form-label">Up-Sells</label>
                  <select multiple="multiple" name="up_sells[]" id="up_sells"
                    class="form-control @error('up_sells') is-invalid @enderror">
                    <option value="">Search and select products...</option>
                  </select>
                  <small class="form-text text-muted">Products to suggest for upgrade or higher value items</small>
                  @error('up_sells')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Cross-Sells -->
                <div class="form-group mb-3">
                  <label for="cross_sells" class="form-label">Cross-Sells</label>
                  <select multiple="multiple" name="cross_sells[]" id="cross_sells"
                    class="form-control @error('cross_sells') is-invalid @enderror">
                    <option value="">Search and select products...</option>
                  </select>
                  <small class="form-text text-muted">Complementary products to suggest alongside this item</small>
                  @error('cross_sells')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Related Products -->
                <div class="form-group mb-3">
                  <label for="related_products" class="form-label">Related Products</label>
                  <select multiple="multiple" name="related_products[]" id="related_products"
                    class="form-control @error('related_products') is-invalid @enderror">
                    <option value="">Search and select products...</option>
                  </select>
                  <small class="form-text text-muted">Similar or alternative products customers might be interested
                    in</small>
                  @error('related_products')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>

    </form>
  </div>
  </div>
@endsection

@push('styles')
  <link href="{{ asset('assets/backend/lib/select2/css/select2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/backend/lib/summernote/summernote-bs4.min.css') }}" rel="stylesheet">
  <style>
    /* Product Media Styles */
    .product-media-container {
      display: flex;
      gap: 20px;
      align-items: flex-start;
    }

    .main-media-wrapper {
      flex: 1;
      max-width: 150px;
    }

    .main-image-holder {
      position: relative;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      overflow: hidden;
      background-color: #f8f9fa;
      aspect-ratio: 1;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .image-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      opacity: 0;
      transition: opacity 0.2s ease;
    }

    .main-image-holder:hover .image-overlay {
      opacity: 1;
    }

    .overlay-content {
      text-align: center;
      color: white;
      font-size: 12px;
    }

    .overlay-content i {
      font-size: 20px;
      margin-bottom: 5px;
    }

    .overlay-content span {
      display: block;
      font-weight: 500;
    }

    /* Hide overlay when image is selected */
    .main-image-holder.has-image .image-overlay {
      opacity: 0;
    }

    .main-image-holder.has-image:hover .image-overlay {
      opacity: 0.8;
    }

    .main-image-holder.has-image .overlay-content span {
      display: none;
    }

    .main-image-holder.has-image .overlay-content i:before {
      content: '\f021';
      /* Change to edit icon */
    }

    .main-image-holder:hover {
      border-color: #007bff;
    }

    .main-product-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .remove-main-image {
      position: absolute;
      top: 8px;
      right: 8px;
      width: 24px;
      height: 24px;
      border: none;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 50%;
      color: #dc3545;
      font-size: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0;
      transition: opacity 0.2s ease;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .main-image-holder:hover .remove-main-image {
      opacity: 1;
    }

    .remove-main-image:hover {
      background-color: #dc3545;
      color: white;
    }

    .media-thumbnails-grid {
      flex: 2;
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      transition: opacity 0.3s ease;
      max-height: 200px;
      overflow-y: auto;
    }

    /* Adjust grid for optimal 8 image display */
    .media-thumbnails-grid .media-thumbnail-item {
      aspect-ratio: 1;
      min-width: 0;
    }

    /* Hidden state - initially not visible */
    .media-thumbnails-grid.hidden {
      display: none;
    }

    /* Visible state with fade in animation */
    .media-thumbnails-grid.visible {
      display: grid;
      opacity: 1;
      animation: fadeInGrid 0.3s ease-in-out;
    }

    @keyframes fadeInGrid {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .media-thumbnail-item {
      position: relative;
      cursor: pointer;
    }

    .thumbnail-holder {
      position: relative;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      overflow: hidden;
      background-color: #f8f9fa;
      aspect-ratio: 1;
      transition: all 0.2s ease;
    }

    .media-thumbnail-item:hover .thumbnail-holder {
      border-color: #007bff;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .thumbnail-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .remove-thumbnail {
      position: absolute;
      top: 4px;
      right: 4px;
      width: 20px;
      height: 20px;
      border: none;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 50%;
      color: #dc3545;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      opacity: 0;
      transition: opacity 0.2s ease;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .media-thumbnail-item:hover .remove-thumbnail {
      opacity: 1;
    }

    .remove-thumbnail:hover {
      background-color: #dc3545;
      color: white;
    }

    .add-new-media .thumbnail-holder {
      border-style: dashed;
      border-color: #cbd3da;
      background-color: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .add-new-media:hover .thumbnail-holder {
      border-color: #007bff;
      background-color: #f0f7ff;
    }

    .add-new-media.disabled .thumbnail-holder {
      border-color: #e9ecef;
      background-color: #f8f9fa;
      opacity: 0.7;
    }

    .add-new-media.disabled:hover .thumbnail-holder {
      border-color: #e9ecef;
      background-color: #f8f9fa;
    }

    .add-media-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 10px;
    }

    .add-media-content i {
      font-size: 18px;
      margin-bottom: 4px;
    }

    .add-media-content span {
      font-size: 11px;
      font-weight: 500;
    }

    /* Draggable states */
    .media-thumbnail-item[data-draggable="true"] {
      cursor: grab;
    }

    .media-thumbnail-item[data-draggable="true"]:active {
      cursor: grabbing;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .product-media-container {
        flex-direction: column;
      }

      .main-media-wrapper {
        max-width: 100%;
        margin-bottom: 20px;
      }

      .media-thumbnails-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 480px) {
      .media-thumbnails-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
  </style>
@endpush

@push('scripts')
  <script src="{{ asset('assets/backend/lib/select2/js/select2.min.js') }}"></script>
  <script src="{{ asset('assets/backend/lib/summernote/summernote-bs4.min.js') }}"></script>
  <script src="{{ asset('assets/backend/js/MediaManager.js') }}"></script>
  <script>
    $(document).ready(function() {
      // Initialize Select2
      $('#categories, #brand_id, #tax_class_id, #vendor_id').select2({
        placeholder: 'Please Select',
        allowClear: true
      });

      // Initialize Select2 for linked products
      $('#up_sells, #cross_sells, #related_products').select2({
        placeholder: 'Search and select products...',
        allowClear: true,
        multiple: true,
        ajax: {
          url: '{{ route('admin.products.search') }}',
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              q: params.term,
              page: params.page
            };
          },
          processResults: function(data, params) {
            params.page = params.page || 1;
            return {
              results: data.items,
              pagination: {
                more: (params.page * 30) < data.total_count
              }
            };
          },
          cache: true
        },
        templateResult: function(product) {
          if (product.loading) return product.text;
          return $('<span>' + product.name + ' <small class="text-muted">($' + product.price +
            ')</small></span>');
        },
        templateSelection: function(product) {
          return product.name || product.text;
        }
      });

      // Initialize Summernote for description
      $('#description').summernote({
        height: 250,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['fontname', ['fontname']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });

      // Auto-generate slug from name
      $('#name').on('keyup', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
          .replace(/[^\w ]+/g, '')
          .replace(/ +/g, '-');
        $('#slug').val(slug);
      });

      // Show/hide inventory fields based on management selection
      $('#inventory_management').on('change', function() {
        const trackInventory = $(this).val() == '1';
        $('#stockAvailabilityGroup, #quantityGroup').toggle(trackInventory);
      });

      // Add/Remove Attributes
      let attributeIndex = 1;
      $('#addAttribute').on('click', function() {
        const attributeHtml = `
      <div class="row mb-3 attribute-row">
        <div class="col-md-5">
          <select class="form-control" name="attributes[${attributeIndex}][name]">
            <option value="">Please Select</option>
          </select>
        </div>
        <div class="col-md-5">
          <input type="text" class="form-control" name="attributes[${attributeIndex}][value]" placeholder="Values">
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-sm btn-outline-danger remove-attribute">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    `;
        $('#attributesContainer').append(attributeHtml);
        attributeIndex++;
      });

      $(document).on('click', '.remove-attribute', function() {
        $(this).closest('.attribute-row').remove();
      });

      // Add/Remove Variations
      let variationIndex = 1;
      $('#addVariation').on('click', function() {
        const variationHtml = `
      <div class="row mb-3 variation-row">
        <div class="col-md-5">
          <input type="text" class="form-control" name="variations[${variationIndex}][name]" placeholder="Name">
        </div>
        <div class="col-md-2">
          <select class="form-control" name="variations[${variationIndex}][type]">
            <option value="">Please Select</option>
            <option value="text">Text</option>
            <option value="color">Color</option>
            <option value="image">Image</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-sm btn-outline-secondary">Select Options</button>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-sm btn-outline-danger remove-variation">
            <i class="fas fa-trash"></i>
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary move-variation-up">
            <i class="fas fa-arrow-up"></i>
          </button>
        </div>
      </div>
    `;
        $('#variationsContainer').append(variationHtml);
        variationIndex++;
      });

      $(document).on('click', '.remove-variation', function() {
        $(this).closest('.variation-row').remove();
      });

      // Add/Remove Options
      let optionIndex = 1;
      $('#addOption').on('click', function() {
        const optionHtml = `
      <div class="row mb-3 option-row">
        <div class="col-md-5">
          <input type="text" class="form-control" name="options[${optionIndex}][name]" placeholder="Name">
        </div>
        <div class="col-md-2">
          <select class="form-control" name="options[${optionIndex}][type]">
            <option value="">Please Select</option>
            <option value="text">Text</option>
            <option value="select">Select</option>
            <option value="radio">Radio</option>
            <option value="checkbox">Checkbox</option>
            <option value="date">Date</option>
            <option value="time">Time</option>
            <option value="datetime">Date & Time</option>
            <option value="file">File</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="button" class="btn btn-sm btn-outline-secondary">Select Options</button>
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-sm btn-outline-danger remove-option">
            <i class="fas fa-trash"></i>
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary move-option-up">
            <i class="fas fa-arrow-up"></i>
          </button>
        </div>
      </div>
    `;
        $('#optionsContainer').append(optionHtml);
        optionIndex++;
      });

      $(document).on('click', '.remove-option', function() {
        $(this).closest('.option-row').remove();
      });

      // Form validation
      $('#productForm').on('submit', function(e) {
        let valid = true;

        // Check required fields
        if (!$('#name').val().trim()) {
          valid = false;
          $('#name').addClass('is-invalid');
        }

        if (!$('#price').val() || parseFloat($('#price').val()) <= 0) {
          valid = false;
          $('#price').addClass('is-invalid');
        }

        if (!valid) {
          e.preventDefault();
          alert('Please fill in all required fields.');
        }
      });

      // Remove validation errors on input
      $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
      });

      // Media management with MediaManager.js
      let featuredImageId = null;
      let galleryImages = [];
      let currentMediaManager = null;
      let currentMediaCallback = null;
      const MAX_GALLERY_IMAGES = 8;

      // Function to create and show media manager
      function createMediaManager(options) {
        // Create modal container
        const modalHtml = `
          <div class="modal fade" id="mediaManagerModal" tabindex="-1" style="z-index: 9999;">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Media Manager</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div id="mediaManagerContainer" style="min-height: 500px;"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" id="selectMediaFiles">Select</button>
                </div>
              </div>
            </div>
          </div>
        `;

        // Remove existing modal
        $('#mediaManagerModal').remove();

        // Add modal to page
        $('body').append(modalHtml);

        // Initialize MediaManager in container mode with ListView as default
        const mediaManager = new MediaManager({
          container: document.getElementById('mediaManagerContainer'),
          multiple: options.multiple || false,
          defaultView: 'list',
          currentView: 'list',
          endpoints: {
            list: '{{ route('admin.media.list') }}',
            upload: '{{ route('admin.media.upload') }}',
            bulkUpload: '{{ route('admin.media.bulk-upload') }}',
            createFolder: '{{ route('admin.media.create-folder') }}',
            renameFolder: '{{ route('admin.media.rename-folder') }}',
            deleteFolder: '{{ route('admin.media.delete-folder') }}',
            renameFile: '{{ route('admin.media.rename-file') }}',
            deleteFile: '{{ route('admin.media.delete', ':id') }}',
            moveToFolder: '{{ route('admin.media.move-to-folder') }}',
            copyToFolder: '{{ route('admin.media.copy-to-folder') }}',
            bulkMoveToFolder: '{{ route('admin.media.bulk-move-to-folder') }}',
            bulkCopyToFolder: '{{ route('admin.media.bulk-copy-to-folder') }}',
            getFolders: '{{ route('admin.media.folders') }}'
          },
          onSelect: options.onSelect || function() {}
        });

        // Force set to list view after initialization
        setTimeout(() => {
          // Set current view to list
          mediaManager.currentView = 'list';

          // Update view buttons
          const viewBtns = document.querySelectorAll('#mediaManagerContainer .view-btn');
          viewBtns.forEach(btn => {
            if (btn.dataset.view === 'list') {
              btn.classList.add('active');
            } else {
              btn.classList.remove('active');
            }
          });

          // Update container classes
          const container = document.getElementById('mediaManagerContainer');
          if (container) {
            container.classList.add('list-view');
          }

          // Try to call the switchView method if it exists
          if (typeof mediaManager.switchView === 'function') {
            mediaManager.switchView('list');
          }

          // Re-render if method exists
          if (mediaManager.render) {
            mediaManager.render();
          }

          // Also try to trigger a click on the list view button
          const listBtn = document.querySelector('#mediaManagerContainer .view-btn[data-view="list"]');
          if (listBtn) {
            listBtn.click();
          }
        }, 300);

        // Show modal
        $('#mediaManagerModal').modal('show');

        // Handle select button
        $('#selectMediaFiles').off('click').on('click', function() {
          const selectedFiles = mediaManager.selectedFiles || [];
          if (options.onSelect) {
            options.onSelect(selectedFiles);
          }
          $('#mediaManagerModal').modal('hide');
        });

        return mediaManager;
      }

      // Handle clicking on main image
      $('#mainImageHolder').on('click', function(e) {
        e.preventDefault();
        createMediaManager({
          multiple: false,
          onSelect: function(files) {
            if (files.length > 0) {
              const file = files[0];
              featuredImageId = file.id;

              // Update the main image
              $('#mainProductImage').attr('src', file.url);
              $('#mainImageHolder').addClass('has-image');
              $('#removeMainImage').show();
              $('#featuredImageInput').val(file.id);

              // Show thumbnail grid after main image is loaded
              $('#mediaThumbnailsGrid').fadeIn(300);
            }
          }
        });
      });

      // Handle clicking on add images button
      $('[data-media-picker-multiple]').on('click', function(e) {
        e.preventDefault();

        // Check if we've reached the maximum limit
        if (galleryImages.length >= MAX_GALLERY_IMAGES) {
          alert(`You can only add up to ${MAX_GALLERY_IMAGES} gallery images.`);
          return;
        }

        createMediaManager({
          multiple: true,
          onSelect: function(files) {
            let addedCount = 0;
            const availableSlots = MAX_GALLERY_IMAGES - galleryImages.length;

            // Add new images to gallery (up to the limit)
            files.forEach((file, index) => {
              if (addedCount >= availableSlots) {
                return; // Stop adding if we reach the limit
              }

              if (!galleryImages.find(img => img.id === file.id)) {
                galleryImages.push(file);
                addThumbnailToGrid(file);
                addedCount++;
              }
            });

            // Show warning if we couldn't add all selected files
            if (files.length > addedCount && addedCount > 0) {
              alert(
                `Only ${addedCount} image(s) were added. Maximum ${MAX_GALLERY_IMAGES} gallery images allowed.`
              );
            } else if (files.length > availableSlots) {
              alert(
                `Maximum ${MAX_GALLERY_IMAGES} gallery images allowed. Please remove some images first.`);
            }

            updateGalleryInput();
            updateAddImageButton();
          }
        });
      });

      // Handle remove main image
      $('#removeMainImage').on('click', function(e) {
        e.stopPropagation();
        if (confirm('Are you sure you want to remove the featured image?')) {
          $('#mainProductImage').attr('src', "{{ asset('assets/images/placeholder_image.png') }}");
          $('#mainImageHolder').removeClass('has-image');
          $(this).hide();
          featuredImageId = null;
          $('#featuredImageInput').val('');

          // Hide thumbnail grid when main image is removed
          $('#mediaThumbnailsGrid').fadeOut(300);

          // Clear gallery images as well
          galleryImages = [];
          $('.media-thumbnail-item:not(.add-new-media)').remove();
          updateGalleryInput();
          updateAddImageButton();
        }
      });

      // Function to add thumbnail to grid
      function addThumbnailToGrid(file) {
        const thumbnailHtml = `
          <div class="media-thumbnail-item" data-file-id="${file.id}" data-draggable="true">
            <div class="thumbnail-holder">
              <img src="${file.url}" alt="Product thumbnail" class="thumbnail-image">
              <button type="button" class="btn remove-thumbnail" title="Remove image">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        `;

        // Insert before the add-new-media button
        $('.add-new-media').before(thumbnailHtml);
      }

      // Handle remove thumbnail
      $(document).on('click', '.remove-thumbnail', function(e) {
        e.stopPropagation();
        const $item = $(this).closest('.media-thumbnail-item');
        const fileId = $item.data('file-id');

        if (confirm('Are you sure you want to remove this image?')) {
          // Remove from gallery array
          galleryImages = galleryImages.filter(img => img.id !== fileId);
          // Remove from DOM
          $item.remove();
          updateGalleryInput();
          updateAddImageButton();
        }
      });

      // Handle thumbnail click to set as main image
      $(document).on('click', '.media-thumbnail-item:not(.add-new-media)', function(e) {
        if (!$(e.target).hasClass('remove-thumbnail') && !$(e.target).closest('.remove-thumbnail').length) {
          const fileId = $(this).data('file-id');
          const thumbnailSrc = $(this).find('.thumbnail-image').attr('src');

          // Find the file object
          const file = galleryImages.find(img => img.id === fileId);
          if (file) {
            // Set as featured image
            const currentMainSrc = $('#mainProductImage').attr('src');
            const currentMainId = featuredImageId;

            // Update main image
            $('#mainProductImage').attr('src', thumbnailSrc);
            $('#mainImageHolder').addClass('has-image');
            $('#removeMainImage').show();
            featuredImageId = fileId;
            $('#featuredImageInput').val(fileId);

            // Ensure thumbnail grid remains visible
            $('#mediaThumbnailsGrid').show();

            // If there was a previous main image that wasn't placeholder, swap it
            if (currentMainId && !currentMainSrc.includes('placeholder_image.png')) {
              $(this).find('.thumbnail-image').attr('src', currentMainSrc);
              // Update the gallery array
              const currentMainFile = galleryImages.find(img => img.id === currentMainId);
              if (currentMainFile) {
                // Remove the new main image from gallery
                galleryImages = galleryImages.filter(img => img.id !== fileId);
                // Add the previous main image to gallery if not already there
                if (!galleryImages.find(img => img.id === currentMainId)) {
                  galleryImages.push(currentMainFile);
                }
              }
            } else {
              // Just remove from gallery since it's now the main image
              galleryImages = galleryImages.filter(img => img.id !== fileId);
              $(this).remove();
            }

            updateGalleryInput();
            updateAddImageButton();
          }
        }
      });

      // Update gallery input with comma-separated IDs
      function updateGalleryInput() {
        const imageIds = galleryImages.map(img => img.id).join(',');
        $('#galleryImagesInput').val(imageIds);
      }

      // Update add image button state based on current count
      function updateAddImageButton() {
        const $addButton = $('.add-new-media');
        const remainingSlots = MAX_GALLERY_IMAGES - galleryImages.length;

        if (remainingSlots <= 0) {
          alert(`You have reached the maximum of ${MAX_GALLERY_IMAGES} gallery images.`);
          // $addButton.addClass('disabled').find('.add-media-content').html(`
        //   <i class="fas fa-check text-success mb-2"></i>
        //   <span class="text-success small">Maximum reached</span>
        //   <span class="text-muted small d-block">${MAX_GALLERY_IMAGES} images</span>
        // `);
          $addButton.css('pointer-events', 'none');
        } else {
          $addButton.removeClass('disabled').find('.add-media-content').html(`
            <i class="fas fa-plus text-muted mb-2"></i>
            <span class="text-muted small">Add Images</span>
            <span class="text-muted small d-block">${remainingSlots} remaining</span>
          `);
          $addButton.css('pointer-events', 'auto');
        }
      }

      // Check if thumbnail grid should be visible
      function checkThumbnailGridVisibility() {
        const mainImageSrc = $('#mainProductImage').attr('src');
        const hasMainImage = mainImageSrc && !mainImageSrc.includes('placeholder_image.png');

        if (hasMainImage) {
          $('#mediaThumbnailsGrid').fadeIn(300);
        } else {
          $('#mediaThumbnailsGrid').fadeOut(300);
        }
      }

      // Initialize - check if there's already a main image on page load
      $(document).ready(function() {
        checkThumbnailGridVisibility();
        updateAddImageButton();
      });

      // Make thumbnails sortable (requires jQuery UI)
      if (typeof $.fn.sortable !== 'undefined') {
        $('#mediaThumbnailsGrid').sortable({
          items: '.media-thumbnail-item:not(.add-new-media)',
          cursor: 'grabbing',
          tolerance: 'pointer',
          placeholder: 'thumbnail-placeholder',
          start: function(e, ui) {
            ui.placeholder.height(ui.item.height());
            ui.placeholder.width(ui.item.width());
          },
          update: function(e, ui) {
            // Update gallery array order based on new DOM order
            const newOrder = [];
            $('.media-thumbnail-item:not(.add-new-media)').each(function() {
              const fileId = $(this).data('file-id');
              const file = galleryImages.find(img => img.id === fileId);
              if (file) newOrder.push(file);
            });
            galleryImages = newOrder;
            updateGalleryInput();
          }
        });
      }
    });
  </script>
@endpush
