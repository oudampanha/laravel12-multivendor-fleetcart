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
                    rows="8" style="resize: vertical; min-height: 120px;">{{ old('description') }}</textarea>
                  @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Brand -->
                <div class="form-group mb-3">
                  <label for="brand_id" class="form-label d-block mb-2">Brand</label>
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
                  <label for="categories" class="form-label d-block mb-2">Categories</label>
                  <input type="text" class="form-control @error('categories') is-invalid @enderror" id="categories"
                    name="categories" value="{{ old('categories') }}" placeholder="">
                  @error('categories')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Tax Class -->
                <div class="form-group mb-3">
                  <label for="tax_class_id" class="form-label d-block mb-2">Tax Class</label>
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
                  <label for="tags" class="form-label d-block mb-2">Tags</label>
                  <input type="text" class="form-control @error('tags') is-invalid @enderror" id="tags"
                    name="tags" value="{{ old('tags') }}" placeholder="">
                  @error('tags')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Virtual Product -->
                <div class="form-group mb-3">
                  <label class="form-label d-block mb-2">Virtual</label>
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_virtual" name="is_virtual" value="1"
                      {{ old('is_virtual') ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_virtual">
                      The product won't be shipped
                    </label>
                  </div>
                </div>

                <!-- Status -->
                <div class="form-group mb-3">
                  <label class="form-label d-block mb-2">Status</label>
                  <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                      {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">
                      Enable the product
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Attributes Section -->
            <div class="card mb-4">
              <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Attributes</h5>
                <button type="button" class="btn btn-link text-dark p-0">
                  <i class="fas fa-th"></i>
                </button>
              </div>
              <div class="card-body p-3">
                <div class="mb-3">
                  <div class="row mb-2">
                    <div class="col-md-5">
                      <label class="form-label d-block mb-1">Attribute</label>
                    </div>
                    <div class="col-md-7">
                      <label class="form-label d-block mb-1">Values</label>
                    </div>
                  </div>

                  <div id="attributesContainer">
                    <!-- Attribute rows -->
                    <div class="attribute-row d-flex align-items-center mb-2" data-attribute-index="0">
                      <button type="button" class="btn btn-sm btn-link text-dark p-1 mr-2 drag-handle">
                        <i class="fas fa-grip-vertical"></i>
                      </button>
                      <div class="row flex-grow-1">
                        <div class="col-md-5">
                          <select class="form-control" name="attributes[0][attribute_id]">
                            <option value="">Please Select</option>
                            @foreach ($attributes as $attribute)
                              <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') ?? $attribute->slug }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-7">
                          <input type="text" class="form-control" name="attributes[0][values]" placeholder="">
                        </div>
                      </div>
                      <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-attribute">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <button type="button" class="btn btn-light border" id="addAttribute">
                    Add Attribute
                  </button>
                </div>
              </div>
            </div>

            <!-- Product Variations Section -->
            <div class="card mb-4">
              <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Variations</h5>
                <div class="d-flex align-items-center">
                  <button type="button" class="btn btn-link text-dark p-0 mr-3" id="toggleAllVariations">
                    <i class="fas fa-chevron-up"></i>
                  </button>
                  <button type="button" class="btn btn-link text-dark p-0">
                    <i class="fas fa-th"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-3">
                <div id="variationsContainer">
                  <!-- Variations will be dynamically added here -->
                  <div class="card border mb-3" data-variation-index="0">
                    <div class="card-header bg-light border-0 py-2" id="headingVariation0">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <button type="button" class="btn btn-link text-dark p-1 mr-2 drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                          </button>
                          <span class="variation-title font-weight-normal">New Variation</span>
                        </div>
                        <div class="d-flex align-items-center">
                          <button type="button" class="btn btn-link text-danger p-1 mr-2 remove-variation">
                            <i class="fas fa-trash"></i>
                          </button>
                          <button type="button" class="btn btn-link text-dark p-1" data-toggle="collapse"
                            data-target="#collapseVariation0" aria-expanded="true" aria-controls="collapseVariation0">
                            <i class="fas fa-chevron-up"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div id="collapseVariation0" class="collapse show" aria-labelledby="headingVariation0">
                      <div class="card-body pt-3 pb-2">
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label d-block mb-2">Name</label>
                            <input type="text" class="form-control variation-name" name="variations[0][name]"
                              placeholder="">
                          </div>
                          <div class="col-md-6 mb-3">
                            <label class="form-label d-block mb-2">Type</label>
                            <select class="form-control variation-type" name="variations[0][type]">
                              <option value="">Please Select</option>
                              <option value="text">Text</option>
                              <option value="color">Color</option>
                              <option value="image">Image</option>
                            </select>
                          </div>
                        </div>

                        <!-- Labels Section (initially hidden) -->
                        <div class="labels-section" style="display: none;">
                          <div class="form-group mb-3">
                            <label class="form-label">Label <span class="text-danger">*</span></label>

                            <!-- Text Type Layout -->
                            <div class="labels-container labels-text" style="display: none;">
                              <div class="label-row d-flex align-items-center mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                                  <i class="fas fa-grip-vertical"></i>
                                </button>
                                <input type="text" class="form-control label-input flex-grow-1"
                                  name="variations[0][labels][]" placeholder="Enter text value (e.g., Small)">
                                <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-label">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </div>

                            <!-- Color Type Layout -->
                            <div class="labels-container labels-color" style="display: none;">
                              <div class="label-row mb-2">
                                <div class="d-flex align-items-center">
                                  <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                                    <i class="fas fa-grip-vertical"></i>
                                  </button>
                                  <div class="color-input-group d-flex align-items-center flex-grow-1">
                                    <input type="color" class="form-control form-control-color color-picker mr-2"
                                      value="#000000" style="width: 50px; height: 38px;">
                                    <input type="text" class="form-control color-name mr-2 flex-grow-1"
                                      placeholder="Color name (e.g., Red)">
                                    <input type="text" class="form-control color-hex label-input"
                                      name="variations[0][labels][]" placeholder="#000000" style="width: 100px;">
                                  </div>
                                  <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-label">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </div>
                              </div>
                            </div>

                            <!-- Image Type Layout -->
                            <div class="labels-container labels-image" style="display: none;">
                              <div class="label-row mb-2">
                                <div class="d-flex align-items-center">
                                  <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                                    <i class="fas fa-grip-vertical"></i>
                                  </button>
                                  <div class="image-input-group d-flex align-items-center flex-grow-1">
                                    <div class="image-preview mr-2"
                                      style="width: 50px; height: 38px; border: 1px solid #ddd; border-radius: 4px; background: #f8f9fa; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                      <i class="fas fa-image text-muted"></i>
                                    </div>
                                    <input type="text" class="form-control image-name mr-2 flex-grow-1"
                                      placeholder="Image name (e.g., Pattern A)">
                                    <input type="hidden" class="image-id label-input" name="variations[0][labels][]"
                                      value="">
                                    <button type="button" class="btn btn-sm btn-outline-primary select-image mr-1">
                                      <i class="fas fa-image"></i>
                                    </button>
                                  </div>
                                  <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-label">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </div>
                              </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-secondary add-label-row">Add Row</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <button type="button" class="btn btn-light border" id="addVariation">
                    Add Variation
                  </button>
                  <div class="d-flex align-items-center">
                    <select class="form-control mr-2" id="variationTemplate" style="width: 200px;">
                      <option value="">Select Template</option>
                      <option value="size">Size (S, M, L, XL)</option>
                      <option value="color">Color (Red, Blue, Green)</option>
                      <option value="material">Material (Cotton, Polyester)</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="insertTemplate">Insert</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Product Variants Section -->
            <div class="card mb-4">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                  <i class="fas fa-code-branch me-2"></i>Variants
                </h5>
                <!-- Right Side: Toggle Switch and Chevron -->
                <div class="d-flex align-items-center gap-3">
                  <!-- Collapse Toggle Button -->
                  <button type="button" class="btn btn-link p-0 text-muted border-0 bg-transparent"
                    aria-expanded="false" aria-controls="variant-collapse" id="toggleAllVariantsAccordion">
                    <i class="fas fa-chevron-down fs-5"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div id="variantsPlaceholder" class="alert alert-info d-flex align-items-center">
                  <i class="fas fa-info-circle me-2"></i>
                  <span>Please add some variations to generate variants.</span>
                </div>

                <!-- Generated Variants -->
                <div id="generatedVariants" class="variants-container mt-3">
                  <!-- Generated variants will appear here -->
                </div>
              </div>
            </div>

            <!-- Product Options Section -->
            <div class="card mb-4">
              <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Options</h5>
                <div class="d-flex align-items-center">
                  <button type="button" class="btn btn-link text-dark p-0 mr-3" id="toggleAllOptions">
                    <i class="fas fa-chevron-up"></i>
                  </button>
                  <button type="button" class="btn btn-link text-dark p-0">
                    <i class="fas fa-th"></i>
                  </button>
                </div>
              </div>
              <div class="card-body p-3">
                <div id="optionsContainer">
                  <!-- Options will be dynamically added here -->
                  <div class="card border mb-3" data-option-index="0">
                    <div class="card-header bg-light border-0 py-2" id="headingOption0">
                      <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                          <button type="button" class="btn btn-link text-dark p-1 mr-2 drag-handle">
                            <i class="fas fa-grip-vertical"></i>
                          </button>
                          <span class="option-title font-weight-normal">New Option</span>
                        </div>
                        <div class="d-flex align-items-center">
                          <button type="button" class="btn btn-link text-danger p-1 mr-2 remove-option">
                            <i class="fas fa-trash"></i>
                          </button>
                          <button type="button" class="btn btn-link text-dark p-1" data-toggle="collapse"
                            data-target="#collapseOption0" aria-expanded="true" aria-controls="collapseOption0">
                            <i class="fas fa-chevron-up"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div id="collapseOption0" class="collapse show" aria-labelledby="headingOption0">
                      <div class="card-body pt-3 pb-2">
                        <div class="row">
                          <div class="col-md-6 mb-3">
                            <label class="form-label d-block mb-2">Name</label>
                            <input type="text" class="form-control option-name" name="options[0][name]"
                              placeholder="">
                          </div>
                          <div class="col-md-4 mb-3">
                            <label class="form-label d-block mb-2">Type</label>
                            <select class="form-control option-type" name="options[0][type]">
                              <option value="">Please Select</option>
                              <optgroup label="Text">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                              </optgroup>
                              <optgroup label="Select">
                                <option value="dropdown">Dropdown</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="check_custom">Custom Check</option>
                                <option value="radio">Radio Button</option>
                                <option value="radio_custom">Custom Radio Button</option>
                                <option value="multiple_select">Multiple Select</option>
                              </optgroup>
                              <optgroup label="Date">
                                <option value="date">Date</option>
                                <option value="date_time">Date & Time</option>
                                <option value="time">Time</option>
                              </optgroup>
                            </select>
                          </div>
                          <div class="col-md-2 mb-3">
                            <label class="form-label d-block mb-2">&nbsp;</label>
                            <div class="form-check">
                              <input type="checkbox" class="form-check-input" id="option0Required"
                                name="options[0][required]" value="1">
                              <label class="form-check-label" for="option0Required">
                                Required
                              </label>
                            </div>
                          </div>
                        </div>

                        <!-- Option Values Section for Text types (initially hidden) -->
                        <div class="option-values-text" style="display: none;">
                          <div class="row">
                            <div class="col-md-8 mb-3">
                              <label class="form-label d-block mb-2">Price</label>
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" class="form-control" name="options[0][price]"
                                  placeholder="0.00">
                              </div>
                            </div>
                            <div class="col-md-4 mb-3">
                              <label class="form-label d-block mb-2">Price Type</label>
                              <select class="form-control" name="options[0][price_type]">
                                <option value="fixed">Fixed</option>
                                <option value="percent">Percent</option>
                              </select>
                            </div>
                          </div>
                        </div>

                        <!-- Option Values Section for Select types (initially hidden) -->
                        <div class="option-values-select" style="display: none;">
                          <div class="mb-2">
                            <div class="row mb-1">
                              <div class="col-md-5">
                                <label class="form-label d-block mb-1">Label <span class="text-danger">*</span></label>
                              </div>
                              <div class="col-md-4">
                                <label class="form-label d-block mb-1">Price</label>
                              </div>
                              <div class="col-md-3">
                                <label class="form-label d-block mb-1">Price Type</label>
                              </div>
                            </div>
                            <div class="option-values-container">
                              <!-- Option value rows will be added here -->
                              <div class="option-value-row d-flex align-items-center mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary drag-handle mr-2">
                                  <i class="fas fa-grip-vertical"></i>
                                </button>
                                <div class="row flex-grow-1">
                                  <div class="col-md-5">
                                    <input type="text" class="form-control" name="options[0][values][0][label]"
                                      placeholder="">
                                  </div>
                                  <div class="col-md-4">
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                      </div>
                                      <input type="number" step="0.01" class="form-control"
                                        name="options[0][values][0][price]" placeholder="0.00">
                                    </div>
                                  </div>
                                  <div class="col-md-3">
                                    <select class="form-control" name="options[0][values][0][price_type]">
                                      <option value="fixed">Fixed</option>
                                      <option value="percent">Percent</option>
                                    </select>
                                  </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ml-2 remove-option-value">
                                  <i class="fas fa-trash"></i>
                                </button>
                              </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary add-option-value">Add Row</button>
                          </div>
                        </div>

                        <!-- Option Values Section for Date/Time types (initially hidden) -->
                        <div class="option-values-datetime" style="display: none;">
                          <div class="row">
                            <div class="col-md-8 mb-3">
                              <label class="form-label d-block mb-2">Price</label>
                              <div class="input-group">
                                <div class="input-group-prepend">
                                  <span class="input-group-text">$</span>
                                </div>
                                <input type="number" step="0.01" class="form-control" name="options[0][price]"
                                  placeholder="0.00">
                              </div>
                            </div>
                            <div class="col-md-4 mb-3">
                              <label class="form-label d-block mb-2">Price Type</label>
                              <select class="form-control" name="options[0][price_type]">
                                <option value="fixed">Fixed</option>
                                <option value="percent">Percent</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                  <button type="button" class="btn btn-light border" id="addOption">
                    Add Option
                  </button>
                  <div class="d-flex align-items-center">
                    <select class="form-control mr-2" id="OptionTemplate" style="width: 200px;">
                      <option value="">Select Template</option>
                      <option value="size">Size (S, M, L, XL)</option>
                      <option value="color">Color (Red, Blue, Green)</option>
                      <option value="material">Material (Cotton, Polyester)</option>
                    </select>
                    <button type="button" class="btn btn-primary" id="insertOptionTemplate">Insert</button>
                  </div>
                </div>
              </div>
            </div>

            {{-- Product download section --}}
            <div class="card mb-4">
              <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Downloads</h5>
                <button type="button" class="btn btn-link text-dark p-0">
                  <i class="fas fa-th"></i>
                </button>
              </div>
              <div class="card-body p-3">
                <div class="mb-3">
                  <label class="form-label d-block mb-2">File</label>

                  <div id="downloadsContainer">
                    <!-- Download file rows -->
                    <div class="download-row d-flex align-items-center mb-2" data-download-index="0">
                      <button type="button" class="btn btn-sm btn-link text-dark p-1 mr-2 drag-handle">
                        <i class="fas fa-grip-vertical"></i>
                      </button>
                      <input type="text" class="form-control flex-grow-1 mr-2" name="downloads[0][file_name]"
                        placeholder="" readonly>
                      <input type="hidden" name="downloads[0][file_id]" value="">
                      <button type="button" class="btn btn-outline-secondary mr-2 choose-file-btn">
                        Choose
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger remove-download">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>

                  <button type="button" class="btn btn-light border" id="addDownload">
                    Add File
                  </button>
                </div>
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
                  <label for="special_price_start" class="form-label">New From</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa-light fa-calendar-days"></i></span>
                    </div>
                    <input type="text"
                      class="form-control flatpickr-input @error('special_price_start') is-invalid @enderror"
                      id="special_price_start" name="special_price_start" value="{{ old('special_price_start') }}">
                    @error('special_price_start')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Special Price End -->
                <div class="form-group mb-3">
                  <label for="special_price_end" class="form-label">New From</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fa-light fa-calendar-days"></i></span>
                    </div>
                    <input type="text"
                      class="form-control flatpickr-input @error('special_price_end') is-invalid @enderror"
                      id="special_price_end" name="special_price_end" value="{{ old('special_price_end') }}">
                    @error('special_price_end')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
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
                    <option value="0" {{ old('manage_stock', '1') == '0' ? 'selected' : '' }}>Don't track
                      inventory
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
                      <span class="input-group-text"><i class="fa-light fa-calendar-days"></i></span>
                    </div>
                    <input type="text" class="form-control flatpickr-input @error('new_from') is-invalid @enderror"
                      id="new_from" name="new_from" value="{{ old('new_from') }}">
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
                      <span class="input-group-text"><i class="fa-light fa-calendar-days"></i></span>
                    </div>
                    <input type="text" class="form-control flatpickr-input @error('new_to') is-invalid @enderror"
                      id="new_to" name="new_to" value="{{ old('new_to') }}">
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
      </form>
    </div>

  </div>
@endsection

@push('styles')
  <link href="{{ asset('assets/backend/lib/select2/css/select2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/backend/lib/summernote/summernote-bs4.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/backend/lib/flatpickr/dist/flatpickr.min.css') }}" rel="stylesheet">
  @include('admin.products.product_css')
@endpush

@push('scripts')
  <script src="{{ asset('assets/backend/lib/select2/js/select2.min.js') }}"></script>
  <script src="{{ asset('assets/backend/lib/summernote/summernote-bs4.min.js') }}"></script>
  <script src="{{ asset('assets/backend/lib/flatpickr/dist/flatpickr.js') }}"></script>
  <script src="{{ asset('assets/backend/js/MediaManager.js') }}"></script>
  @include('admin.products.product_js')
@endpush
