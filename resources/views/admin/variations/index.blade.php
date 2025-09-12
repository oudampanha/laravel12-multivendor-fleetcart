@extends('admin.layouts.master_layout')

@section('pageTitle', 'Variations')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Variations</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">🏠</a></li>
            <li class="breadcrumb-item active" aria-current="page">Variations</li>
          </ol>
        </nav>
      </div>

      <!-- Success Alert -->
      <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
        <i class="fas fa-check-circle me-2"></i>
        <span id="successMessage">Variation created</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <div class="row">
        <!-- Variations List -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addVariationBtn">
                    Add Variation
                  </button>
                </div>
              </div>

              <div id="variationsList" class="variations-list">
                <!-- Variations will be loaded here -->
              </div>
            </div>
          </div>
        </div>

        <!-- Variation Form -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <form id="variationForm">
                @csrf
                <input type="hidden" id="variationId" name="variation_id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <!-- General Section -->
                <div class="form-section mb-4">
                  <h5 class="section-title">General</h5>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="variationName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="variationName" name="name" required>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="variationType" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="variationType" name="type" required>
                          <option value="">Select Type</option>
                          <option value="text">Text</option>
                          <option value="color">Color</option>
                          <option value="image">Image</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="variationUid" class="form-label">UID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="variationUid" name="uid" required>
                        <div class="form-text">Unique identifier (e.g., size, color)</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <label for="position" class="form-label">Position</label>
                        <input type="number" class="form-control" id="position" name="position" min="0">
                        <div class="form-text">Display order</div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="mb-3">
                        <div class="form-check mt-4">
                          <input class="form-check-input" type="checkbox" id="isGlobal" name="is_global" value="1">
                          <label class="form-check-label" for="isGlobal">
                            Global variation
                          </label>
                          <div class="form-text">Available for all products</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Values Section -->
                <div class="form-section mb-4">
                  <h5 class="section-title">Values</h5>
                  <div id="variationValuesContainer">
                    <div class="variation-value-header d-flex mb-2">
                      <div class="col-1"></div>
                      <div class="col-5 text-muted">Label <span class="text-danger">*</span></div>
                      <div class="col-5 text-muted" id="valueTypeHeader">Value</div>
                      <div class="col-1"></div>
                    </div>
                    <div id="variationValuesList">
                      <!-- Dynamic rows will be added here -->
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addRowBtn">
                      <i class="fas fa-plus me-1"></i> Add Row
                    </button>
                  </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn btn-secondary me-2" id="cancelBtn">Cancel</button>
                  <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .variations-list {
      max-height: 600px;
      overflow-y: auto;
    }

    .variation-item {
      padding: 12px 15px;
      border: 1px solid #e9ecef;
      border-radius: 6px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      background-color: #fff;
    }

    .variation-item:hover {
      background-color: #f8f9fa;
      border-color: #007bff;
    }

    .variation-item.selected {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
    }

    .variation-item .item-name {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .variation-item .item-meta {
      font-size: 12px;
      opacity: 0.8;
    }

    .variation-item .item-uid {
      font-family: 'Courier New', monospace;
      font-size: 11px;
      background-color: rgba(0, 0, 0, 0.1);
      padding: 2px 6px;
      border-radius: 3px;
      margin-right: 8px;
    }

    .variation-item.selected .item-uid {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .variation-item .item-actions {
      margin-top: 8px;
    }

    .variation-item .item-actions .btn {
      padding: 2px 8px;
      font-size: 11px;
      margin-right: 5px;
    }

    .variation-item.selected .item-actions .btn {
      opacity: 0.9;
    }

    .variation-type-badge {
      font-size: 10px;
      padding: 2px 6px;
      border-radius: 3px;
      margin-right: 5px;
    }

    .type-text {
      background-color: #17a2b8;
      color: white;
    }

    .type-color {
      background-color: #28a745;
      color: white;
    }

    .type-image {
      background-color: #ffc107;
      color: #212529;
    }

    .global-badge {
      background-color: #6f42c1;
      color: white;
      font-size: 10px;
      padding: 2px 6px;
      border-radius: 3px;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #6c757d;
    }

    .empty-state i {
      font-size: 48px;
      margin-bottom: 16px;
      opacity: 0.5;
    }

    .loading-spinner {
      text-align: center;
      padding: 20px;
    }

    .loading-spinner i {
      font-size: 24px;
      animation: spin 1s linear infinite;
    }

    /* Form Section Styles */
    .form-section {
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 20px;
      background-color: #fff;
    }

    .section-title {
      font-size: 16px;
      font-weight: 600;
      color: #495057;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid #e9ecef;
    }

    /* Variation Values Styles */
    .variation-value-header {
      font-weight: 500;
      font-size: 14px;
      padding: 0 15px;
    }

    .variation-value-row {
      border: 1px solid #e9ecef;
      border-radius: 6px;
      background-color: #fff;
      margin-bottom: 10px;
      padding: 10px 15px;
      display: flex;
      align-items: center;
      transition: all 0.2s ease;
    }

    .variation-value-row:hover {
      border-color: #007bff;
      box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
    }

    .drag-handle {
      cursor: move;
      color: #6c757d;
      font-size: 16px;
      margin-right: 10px;
      padding: 5px;
    }

    .drag-handle:hover {
      color: #495057;
    }

    .delete-row {
      cursor: pointer;
      color: #dc3545;
      font-size: 16px;
      padding: 5px;
      margin-left: 10px;
    }

    .delete-row:hover {
      color: #c82333;
    }

    .image-upload-wrapper {
      position: relative;
      display: inline-block;
      width: 100%;
    }

    .image-upload-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      border: 2px dashed #e9ecef;
      border-radius: 6px;
      padding: 30px;
      cursor: pointer;
      transition: all 0.2s ease;
      background-color: #f8f9fa;
      min-height: 80px;
    }

    .image-upload-btn:hover {
      border-color: #007bff;
      background-color: #e3f2fd;
    }

    .image-upload-btn i {
      font-size: 24px;
      color: #6c757d;
      margin-bottom: 8px;
    }

    .image-preview {
      max-width: 60px;
      max-height: 60px;
      border-radius: 4px;
      object-fit: cover;
    }

    .sortable-ghost {
      opacity: 0.4;
    }

    .sortable-chosen {
      transform: rotate(2deg);
    }

    /* Type-specific input styles */
    .color-input-wrapper {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .color-picker {
      width: 50px;
      height: 38px;
      padding: 2px;
      border: 1px solid #ced4da;
      border-radius: 6px;
      cursor: pointer;
      background: none;
    }

    .color-picker::-webkit-color-swatch-wrapper {
      padding: 0;
    }

    .color-picker::-webkit-color-swatch {
      border: none;
      border-radius: 4px;
    }

    .text-input {
      width: 100%;
    }

    .image-only-upload {
      min-height: 60px;
      padding: 20px;
    }

    .image-only-upload i {
      font-size: 20px;
    }

    .values-disabled {
      opacity: 0.5;
      pointer-events: none;
    }

    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
  </style>
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <script>
    $(document).ready(function() {
      let selectedVariation = null;
      let isEditMode = false;
      let rowCounter = 0;

      // Load variations on page load
      loadVariations();
      
      // Initialize with one empty row
      addVariationValueRow();
      
      // Initialize sortable
      initializeSortable();

      // Handle type change
      $('#variationType').on('change', function() {
        const selectedType = $(this).val();
        updateValuesSection(selectedType);
        
        // Clear and rebuild existing rows with new type
        $('#variationValuesList').empty();
        rowCounter = 0;
        if (selectedType) {
          addVariationValueRow();
        }
      });

      // Add Variation
      $('#addVariationBtn').click(function() {
        clearForm();
        isEditMode = false;
        $('#saveBtn').text('Save');
        selectedVariation = null;
        $('.variation-item').removeClass('selected');
      });

      // Form submission
      $('#variationForm').submit(function(e) {
        e.preventDefault();
        saveVariation();
      });

      // Cancel button
      $('#cancelBtn').click(function() {
        clearForm();
        selectedVariation = null;
        $('.variation-item').removeClass('selected');
      });

      // Add Row button
      $('#addRowBtn').click(function() {
        addVariationValueRow();
      });

      // Delegate event for delete buttons
      $(document).on('click', '.delete-row', function() {
        if ($('.variation-value-row').length > 1) {
          $(this).closest('.variation-value-row').remove();
          updateRowNumbers();
        }
      });

      // Delegate event for image uploads
      $(document).on('change', '.image-upload-input', function() {
        handleImageUpload(this);
      });

      // Delegate event for clicking image upload area
      $(document).on('click', '.image-upload-btn', function() {
        $(this).siblings('.image-upload-input').click();
      });

      // Functions
      function loadVariations() {
        $('#variationsList').html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>');

        $.ajax({
          url: '{{ route('admin.variations.index') }}',
          method: 'GET',
          data: { ajax: true },
          success: function(response) {
            displayVariations(response);
          },
          error: function(xhr) {
            console.error('Error loading variations:', xhr);
            $('#variationsList').html('<div class="text-center text-danger">Error loading variations</div>');
          }
        });
      }

      function displayVariations(variations) {
        const container = $('#variationsList');
        
        if (variations.length === 0) {
          container.html(`
            <div class="empty-state">
              <i class="fas fa-palette"></i>
              <div class="h6">No variations found</div>
              <div class="text-muted">Click "Add Variation" to create your first one.</div>
            </div>
          `);
          return;
        }

        let html = '';
        variations.forEach(function(variation) {
          const createdDate = new Date(variation.created_at).toLocaleDateString();
          const typeClass = `type-${variation.type}`;
          const typeName = variation.type.charAt(0).toUpperCase() + variation.type.slice(1);
          
          html += `
            <div class="variation-item" data-id="${variation.id}">
              <div class="item-name">${variation.name}</div>
              <div class="item-meta">
                <span class="item-uid">${variation.uid}</span>
                <span class="variation-type-badge ${typeClass}">${typeName}</span>
                ${variation.is_global ? '<span class="global-badge">Global</span>' : ''}
              </div>
              <div class="item-meta mt-1">
                ${variation.values_count} values • Created ${createdDate}
              </div>
              <div class="item-actions">
                <button class="btn btn-sm btn-outline-primary edit-variation" data-id="${variation.id}">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger delete-variation" data-id="${variation.id}">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </div>
          `;
        });

        container.html(html);

        // Bind click events
        $('.variation-item').click(function(e) {
          if ($(e.target).hasClass('btn') || $(e.target).parent().hasClass('btn')) {
            return; // Don't select when clicking buttons
          }
          
          const variationId = $(this).data('id');
          selectVariation(variationId);
        });

        $('.edit-variation').click(function(e) {
          e.stopPropagation();
          const variationId = $(this).data('id');
          editVariation(variationId);
        });

        $('.delete-variation').click(function(e) {
          e.stopPropagation();
          const variationId = $(this).data('id');
          deleteVariation(variationId);
        });
      }

      function selectVariation(variationId) {
        $('.variation-item').removeClass('selected');
        $(`.variation-item[data-id="${variationId}"]`).addClass('selected');
        selectedVariation = variationId;
        
        // Load variation data for viewing
        loadVariationData(variationId, false);
      }

      function editVariation(variationId) {
        isEditMode = true;
        loadVariationData(variationId, true);
        $('#saveBtn').text('Update');
        
        // Select the item
        $('.variation-item').removeClass('selected');
        $(`.variation-item[data-id="${variationId}"]`).addClass('selected');
        selectedVariation = variationId;
      }

      function loadVariationData(variationId, isEdit) {
        $.ajax({
          url: '{{ route('admin.variations.edit', ':id') }}'.replace(':id', variationId),
          method: 'GET',
          data: { ajax: true },
          success: function(response) {
            if (response.success) {
              const variation = response.variation;
              
              if (isEdit) {
                // Fill form for editing
                $('#variationId').val(variation.id);
                $('#variationName').val(variation.name);
                $('#variationUid').val(variation.uid);
                $('#variationType').val(variation.type);
                $('#isGlobal').prop('checked', variation.is_global);
                $('#position').val(variation.position);
                $('#formMethod').val('PUT');
                
                // Load variation values
                $('#variationValuesList').empty();
                rowCounter = 0;
                updateValuesSection(variation.type);
                
                if (variation.values && variation.values.length > 0) {
                  variation.values.forEach(function(value) {
                    addVariationValueRow(value.value, value.image, value.color);
                  });
                } else {
                  addVariationValueRow();
                }
              } else {
                // Just display data (could show details panel later)
                $('#variationId').val('');
                $('#variationName').val('');
                $('#variationUid').val('');
                $('#variationType').val('');
                $('#isGlobal').prop('checked', false);
                $('#position').val('');
                $('#formMethod').val('POST');
                
                // Reset values
                $('#variationValuesList').empty();
                rowCounter = 0;
                updateValuesSection('');
              }
            }
          },
          error: function(xhr) {
            console.error('Error loading variation:', xhr);
            showErrorAlert('Error loading variation data');
          }
        });
      }

      function saveVariation() {
        const formData = new FormData($('#variationForm')[0]);
        const variationId = $('#variationId').val();
        const method = isEditMode ? 'PUT' : 'POST';
        let url = '{{ route('admin.variations.store') }}';

        if (isEditMode && variationId) {
          url = '{{ route('admin.variations.index') }}/' + variationId;
          formData.append('_method', 'PUT');
        }

        $.ajax({
          url: url,
          method: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response.success) {
              showSuccessAlert(response.message || 'Variation saved successfully!');
              loadVariations();
              clearForm();
            }
          },
          error: function(xhr) {
            let message = 'An error occurred';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              message = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
              const errors = Object.values(xhr.responseJSON.errors).flat();
              message = errors.join(', ');
            }
            showErrorAlert(message);
          }
        });
      }

      function deleteVariation(variationId) {
        if (confirm('Are you sure you want to delete this variation? This action cannot be undone.')) {
          $.ajax({
            url: '{{ route('admin.variations.index') }}/' + variationId,
            method: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                showSuccessAlert(response.message || 'Variation deleted successfully!');
                loadVariations();
                clearForm();
              } else {
                showErrorAlert(response.message || 'Failed to delete variation');
              }
            },
            error: function(xhr) {
              let message = 'Failed to delete variation';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
              showErrorAlert(message);
            }
          });
        }
      }

      function clearForm() {
        $('#variationForm')[0].reset();
        $('#variationId').val('');
        $('#formMethod').val('POST');
        isEditMode = false;
        selectedVariation = null;
        $('#saveBtn').text('Save');
        $('.variation-item').removeClass('selected');
        
        // Clear and reset variation values
        $('#variationValuesList').empty();
        rowCounter = 0;
        updateValuesSection('');
      }

      function addVariationValueRow(label = '', image = '', color = '') {
        const variationType = $('#variationType').val();
        if (!variationType) {
          return; // Don't add rows if no type is selected
        }

        rowCounter++;
        let valueInputHtml = '';

        // Generate value input based on type
        switch (variationType) {
          case 'text':
            valueInputHtml = `<input type="text" class="form-control text-input" name="values[${rowCounter}][value]" value="${label}" placeholder="Enter text value" required>`;
            break;
          
          case 'color':
            valueInputHtml = `
              <div class="color-input-wrapper">
                <input type="color" class="color-picker" name="values[${rowCounter}][color]" value="${color || '#000000'}" title="Choose color">
                <input type="text" class="form-control" name="values[${rowCounter}][value]" value="${label}" placeholder="Color name" required>
              </div>
            `;
            break;
          
          case 'image':
            valueInputHtml = `
              <div class="image-upload-wrapper">
                <input type="file" class="image-upload-input" name="values[${rowCounter}][image]" accept="image/*" style="display: none;" required>
                <div class="image-upload-btn image-only-upload">
                  <div class="text-center">
                    <i class="fas fa-image"></i>
                    <div class="small text-muted mt-1">Click to upload image</div>
                  </div>
                </div>
                <div class="image-preview-container mt-2" style="display: none;">
                  <img src="" alt="Preview" class="image-preview">
                </div>
              </div>
            `;
            break;
          
          default:
            valueInputHtml = `<input type="text" class="form-control" name="values[${rowCounter}][value]" value="${label}" placeholder="Enter value" required>`;
        }

        const rowHtml = `
          <div class="variation-value-row" data-row="${rowCounter}">
            <div class="col-1">
              <i class="fas fa-grip-vertical drag-handle"></i>
            </div>
            <div class="col-5">
              <input type="text" class="form-control" name="values[${rowCounter}][label]" value="${label}" placeholder="Enter label" required>
            </div>
            <div class="col-5">
              ${valueInputHtml}
            </div>
            <div class="col-1">
              <i class="fas fa-trash delete-row"></i>
            </div>
          </div>
        `;
        
        $('#variationValuesList').append(rowHtml);
        
        // Handle existing image preview
        if (image && variationType === 'image') {
          const $row = $(`.variation-value-row[data-row="${rowCounter}"]`);
          const $previewContainer = $row.find('.image-preview-container');
          const $preview = $row.find('.image-preview');
          $preview.attr('src', image);
          $previewContainer.show();
        }
        
        // Update row numbers after adding
        updateRowNumbers();
      }

      function handleImageUpload(input) {
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          const $row = $(input).closest('.variation-value-row');
          const $previewContainer = $row.find('.image-preview-container');
          const $preview = $row.find('.image-preview');
          
          reader.onload = function(e) {
            $preview.attr('src', e.target.result);
            $previewContainer.show();
          };
          
          reader.readAsDataURL(input.files[0]);
        }
      }

      function initializeSortable() {
        const valuesList = document.getElementById('variationValuesList');
        if (valuesList) {
          Sortable.create(valuesList, {
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            animation: 150,
            onEnd: function(evt) {
              updateRowNumbers();
            }
          });
        }
      }

      function updateRowNumbers() {
        $('#variationValuesList .variation-value-row').each(function(index) {
          const newRowNum = index + 1;
          $(this).attr('data-row', newRowNum);
          $(this).find('input[name*="[label]"]').attr('name', `values[${newRowNum}][label]`);
          $(this).find('input[name*="[value]"]').attr('name', `values[${newRowNum}][value]`);
          $(this).find('input[name*="[color]"]').attr('name', `values[${newRowNum}][color]`);
          $(this).find('input[name*="[image]"]').attr('name', `values[${newRowNum}][image]`);
        });
      }

      function updateValuesSection(selectedType) {
        const $container = $('#variationValuesContainer');
        const $header = $('#valueTypeHeader');
        const $addBtn = $('#addRowBtn');
        
        if (!selectedType) {
          $container.addClass('values-disabled');
          $header.text('Value');
          $addBtn.prop('disabled', true);
          return;
        }
        
        $container.removeClass('values-disabled');
        $addBtn.prop('disabled', false);
        
        // Update header text based on type
        switch (selectedType) {
          case 'text':
            $header.text('Text Value');
            break;
          case 'color':
            $header.text('Color & Name');
            break;
          case 'image':
            $header.text('Image Upload');
            break;
          default:
            $header.text('Value');
        }
      }

      function showSuccessAlert(message) {
        $('#successMessage').text(message);
        $('#successAlert').removeClass('fade').addClass('show').fadeIn();

        setTimeout(function() {
          $('#successAlert').fadeOut(function() {
            $(this).removeClass('show').addClass('fade');
          });
        }, 5000);
      }

      function showErrorAlert(message) {
        alert('Error: ' + message);
      }

      // Auto-generate UID from name
      $('#variationName').on('input', function() {
        if (!isEditMode && !$('#variationUid').val()) {
          const name = $(this).val();
          const uid = name.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
          $('#variationUid').val(uid);
        }
      });
    });
  </script>
@endpush