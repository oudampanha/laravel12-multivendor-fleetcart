@extends('admin.layouts.master_layout')

@section('pageTitle', 'Attribute Sets')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Attribute Sets</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">🏠</a></li>
            <li class="breadcrumb-item active" aria-current="page">Attribute Sets</li>
          </ol>
        </nav>
      </div>

      <!-- Success Alert -->
      <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
        <i class="fas fa-check-circle me-2"></i>
        <span id="successMessage">Attribute set created</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <div class="row">
        <!-- Attribute Sets List -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addAttributeSetBtn">
                    Add Attribute Set
                  </button>
                </div>
              </div>

              <div id="attributeSetsList" class="attribute-sets-list">
                <!-- Attribute sets will be loaded here -->
              </div>
            </div>
          </div>
        </div>

        <!-- Attribute Set Form -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <form id="attributeSetForm">
                @csrf
                <input type="hidden" id="attributeSetId" name="attribute_set_id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <!-- General Section -->
                <div class="form-section mb-4">
                  <h5 class="section-title">General</h5>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="attributeSetName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="attributeSetName" name="name" required>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="mb-3">
                        <label for="attributeSetType" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="attributeSetType" name="type" required>
                          <option value="">Select Type</option>
                          <option value="text">Text</option>
                          <option value="image">Image</option>
                          <option value="color">Color</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Values Section -->
                <div class="form-section mb-4">
                  <h5 class="section-title">Values</h5>
                  <div id="attributeValuesContainer">
                    <div class="attribute-value-header d-flex mb-2">
                      <div class="col-1"></div>
                      <div class="col-5 text-muted">Label <span class="text-danger">*</span></div>
                      <div class="col-5 text-muted">Image</div>
                      <div class="col-1"></div>
                    </div>
                    <div id="attributeValuesList">
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
    .attribute-sets-list {
      max-height: 600px;
      overflow-y: auto;
    }

    .attribute-set-item {
      padding: 12px 15px;
      border: 1px solid #e9ecef;
      border-radius: 6px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      background-color: #fff;
    }

    .attribute-set-item:hover {
      background-color: #f8f9fa;
      border-color: #007bff;
    }

    .attribute-set-item.selected {
      background-color: #007bff;
      border-color: #007bff;
      color: white;
    }

    .attribute-set-item .item-name {
      font-weight: 600;
      margin-bottom: 5px;
    }

    .attribute-set-item .item-meta {
      font-size: 12px;
      opacity: 0.8;
    }

    .attribute-set-item .item-actions {
      margin-top: 8px;
    }

    .attribute-set-item .item-actions .btn {
      padding: 2px 8px;
      font-size: 11px;
      margin-right: 5px;
    }

    .attribute-set-item.selected .item-actions .btn {
      opacity: 0.9;
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

    /* Attribute Values Styles */
    .attribute-value-header {
      font-weight: 500;
      font-size: 14px;
      padding: 0 15px;
    }

    .attribute-value-row {
      border: 1px solid #e9ecef;
      border-radius: 6px;
      background-color: #fff;
      margin-bottom: 10px;
      padding: 10px 15px;
      display: flex;
      align-items: center;
      transition: all 0.2s ease;
    }

    .attribute-value-row:hover {
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
      let selectedAttributeSet = null;
      let isEditMode = false;
      let rowCounter = 0;

      // Load attribute sets on page load
      loadAttributeSets();
      
      // Initialize with one empty row
      addAttributeValueRow();
      
      // Initialize sortable
      initializeSortable();

      // Add Attribute Set
      $('#addAttributeSetBtn').click(function() {
        clearForm();
        isEditMode = false;
        $('#saveBtn').text('Save');
        selectedAttributeSet = null;
        $('.attribute-set-item').removeClass('selected');
      });

      // Form submission
      $('#attributeSetForm').submit(function(e) {
        e.preventDefault();
        saveAttributeSet();
      });

      // Cancel button
      $('#cancelBtn').click(function() {
        clearForm();
        selectedAttributeSet = null;
        $('.attribute-set-item').removeClass('selected');
      });

      // Add Row button
      $('#addRowBtn').click(function() {
        addAttributeValueRow();
      });

      // Delegate event for delete buttons
      $(document).on('click', '.delete-row', function() {
        if ($('.attribute-value-row').length > 1) {
          $(this).closest('.attribute-value-row').remove();
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
      function loadAttributeSets() {
        $('#attributeSetsList').html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>');

        $.ajax({
          url: '{{ route('admin.attribute-sets.index') }}',
          method: 'GET',
          data: { ajax: true },
          success: function(response) {
            displayAttributeSets(response);
          },
          error: function(xhr) {
            console.error('Error loading attribute sets:', xhr);
            $('#attributeSetsList').html('<div class="text-center text-danger">Error loading attribute sets</div>');
          }
        });
      }

      function displayAttributeSets(attributeSets) {
        const container = $('#attributeSetsList');
        
        if (attributeSets.length === 0) {
          container.html(`
            <div class="empty-state">
              <i class="fas fa-tags"></i>
              <div class="h6">No attribute sets found</div>
              <div class="text-muted">Click "Add Attribute Set" to create your first one.</div>
            </div>
          `);
          return;
        }

        let html = '';
        attributeSets.forEach(function(attributeSet) {
          const createdDate = new Date(attributeSet.created_at).toLocaleDateString();
          html += `
            <div class="attribute-set-item" data-id="${attributeSet.id}">
              <div class="item-name">${attributeSet.name}</div>
              <div class="item-meta">
                ${attributeSet.attributes_count} attributes • Created ${createdDate}
              </div>
              <div class="item-actions">
                <button class="btn btn-sm btn-outline-primary edit-attribute-set" data-id="${attributeSet.id}">
                  <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger delete-attribute-set" data-id="${attributeSet.id}">
                  <i class="fas fa-trash"></i> Delete
                </button>
              </div>
            </div>
          `;
        });

        container.html(html);

        // Bind click events
        $('.attribute-set-item').click(function(e) {
          if ($(e.target).hasClass('btn') || $(e.target).parent().hasClass('btn')) {
            return; // Don't select when clicking buttons
          }
          
          const attributeSetId = $(this).data('id');
          selectAttributeSet(attributeSetId);
        });

        $('.edit-attribute-set').click(function(e) {
          e.stopPropagation();
          const attributeSetId = $(this).data('id');
          editAttributeSet(attributeSetId);
        });

        $('.delete-attribute-set').click(function(e) {
          e.stopPropagation();
          const attributeSetId = $(this).data('id');
          deleteAttributeSet(attributeSetId);
        });
      }

      function selectAttributeSet(attributeSetId) {
        $('.attribute-set-item').removeClass('selected');
        $(`.attribute-set-item[data-id="${attributeSetId}"]`).addClass('selected');
        selectedAttributeSet = attributeSetId;
        
        // Load attribute set data for viewing
        loadAttributeSetData(attributeSetId, false);
      }

      function editAttributeSet(attributeSetId) {
        isEditMode = true;
        loadAttributeSetData(attributeSetId, true);
        $('#saveBtn').text('Update');
        
        // Select the item
        $('.attribute-set-item').removeClass('selected');
        $(`.attribute-set-item[data-id="${attributeSetId}"]`).addClass('selected');
        selectedAttributeSet = attributeSetId;
      }

      function loadAttributeSetData(attributeSetId, isEdit) {
        $.ajax({
          url: '{{ route('admin.attribute-sets.edit', ':id') }}'.replace(':id', attributeSetId),
          method: 'GET',
          data: { ajax: true },
          success: function(response) {
            if (response.success) {
              const attributeSet = response.attribute_set;
              
              if (isEdit) {
                // Fill form for editing
                $('#attributeSetId').val(attributeSet.id);
                $('#attributeSetName').val(attributeSet.name);
                $('#formMethod').val('PUT');
              } else {
                // Just display data (could show details panel later)
                $('#attributeSetId').val('');
                $('#attributeSetName').val('');
                $('#formMethod').val('POST');
              }
            }
          },
          error: function(xhr) {
            console.error('Error loading attribute set:', xhr);
            showErrorAlert('Error loading attribute set data');
          }
        });
      }

      function saveAttributeSet() {
        const formData = new FormData($('#attributeSetForm')[0]);
        const attributeSetId = $('#attributeSetId').val();
        const method = isEditMode ? 'PUT' : 'POST';
        let url = '{{ route('admin.attribute-sets.store') }}';

        if (isEditMode && attributeSetId) {
          url = '{{ route('admin.attribute-sets.index') }}/' + attributeSetId;
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
              showSuccessAlert(response.message || 'Attribute set saved successfully!');
              loadAttributeSets();
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

      function deleteAttributeSet(attributeSetId) {
        if (confirm('Are you sure you want to delete this attribute set? This action cannot be undone.')) {
          $.ajax({
            url: '{{ route('admin.attribute-sets.index') }}/' + attributeSetId,
            method: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                showSuccessAlert(response.message || 'Attribute set deleted successfully!');
                loadAttributeSets();
                clearForm();
              } else {
                showErrorAlert(response.message || 'Failed to delete attribute set');
              }
            },
            error: function(xhr) {
              let message = 'Failed to delete attribute set';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
              showErrorAlert(message);
            }
          });
        }
      }

      function clearForm() {
        $('#attributeSetForm')[0].reset();
        $('#attributeSetId').val('');
        $('#formMethod').val('POST');
        isEditMode = false;
        selectedAttributeSet = null;
        $('#saveBtn').text('Save');
        $('.attribute-set-item').removeClass('selected');
        
        // Clear and reset attribute values
        $('#attributeValuesList').empty();
        rowCounter = 0;
        addAttributeValueRow();
      }

      function addAttributeValueRow(label = '', image = '') {
        rowCounter++;
        const rowHtml = `
          <div class="attribute-value-row" data-row="${rowCounter}">
            <div class="col-1">
              <i class="fas fa-grip-vertical drag-handle"></i>
            </div>
            <div class="col-5">
              <input type="text" class="form-control" name="values[${rowCounter}][label]" value="${label}" placeholder="Enter label" required>
            </div>
            <div class="col-5">
              <div class="image-upload-wrapper">
                <input type="file" class="image-upload-input" name="values[${rowCounter}][image]" accept="image/*" style="display: none;">
                <div class="image-upload-btn">
                  <div class="text-center">
                    <i class="fas fa-image"></i>
                    <div class="small text-muted mt-1">Click to upload</div>
                  </div>
                </div>
                <div class="image-preview-container mt-2" style="display: none;">
                  <img src="" alt="Preview" class="image-preview">
                </div>
              </div>
            </div>
            <div class="col-1">
              <i class="fas fa-trash delete-row"></i>
            </div>
          </div>
        `;
        
        $('#attributeValuesList').append(rowHtml);
        
        if (image) {
          const $row = $(`.attribute-value-row[data-row="${rowCounter}"]`);
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
          const $row = $(input).closest('.attribute-value-row');
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
        const valuesList = document.getElementById('attributeValuesList');
        if (valuesList) {
          Sortable.create(valuesList, {
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            animation: 150,
            onEnd: function(evt) {
              // Update row numbers after reordering
              updateRowNumbers();
            }
          });
        }
      }

      function updateRowNumbers() {
        $('#attributeValuesList .attribute-value-row').each(function(index) {
          const newRowNum = index + 1;
          $(this).attr('data-row', newRowNum);
          $(this).find('input[name*="[label]"]').attr('name', `values[${newRowNum}][label]`);
          $(this).find('input[name*="[image]"]').attr('name', `values[${newRowNum}][image]`);
        });
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
    });
  </script>
@endpush