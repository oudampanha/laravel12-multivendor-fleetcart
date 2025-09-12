@extends('admin.layouts.master_layout')

@section('pageTitle', 'Attributes Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Attributes Management</h4>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createAttributeModal">
              <i class="fas fa-plus"></i> Add New Attribute
            </button>
            <button type="button" class="btn btn-info" id="refreshTableBtn">
              <i class="fas fa-sync-alt"></i> Refresh
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-md-3">
              <div class="form-group">
                <label for="filterableFilter">Filter by Filterable</label>
                <select class="form-control" id="filterableFilter">
                  <option value="">All</option>
                  <option value="Yes">Yes</option>
                  <option value="No">No</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>&nbsp;</label>
                <div class="d-flex">
                  <button type="button" class="btn btn-secondary mr-2" id="clearFiltersBtn">
                    <i class="fas fa-times"></i> Clear Filters
                  </button>
                  <button type="button" class="btn btn-success" id="exportAttributesBtn">
                    <i class="fas fa-download"></i> Export Attributes
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="attributesTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Attribute Set</th>
                  <th>Values</th>
                  <th>Filterable</th>
                  <th>Created At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Data will be loaded via Ajax DataTables -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
      <i class="fas fa-spinner fa-spin"></i>
      <div>Processing...</div>
    </div>
  </div>

  <!-- Attribute Modals -->
  <div class="modal fade" id="createAttributeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
      <form id="createAttributeForm" method="POST">
        @csrf
        <input type="hidden" id="attributeId" name="attribute_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Create New Attribute</h5>
            <button type="button" class="close" data-dismiss="modal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
              <!-- Sidebar Navigation -->
              <div class="col-md-3">
                <div class="card">
                  <div class="card-header bg-light">
                    <h3 class="card-title">
                      <i class="fas fa-cogs mr-2"></i>
                      Attribute Information
                    </h3>
                  </div>
                  <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column" id="attribute-tabs">
                      <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab">
                          <i class="fas fa-info-circle mr-2"></i>
                          General
                        </a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" id="values-tab" data-toggle="pill" href="#values" role="tab">
                          <i class="fas fa-tags mr-2"></i>
                          Values
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!-- Content Area -->
              <div class="col-md-9">
                <div class="tab-content" id="attribute-tabContent">
                  <!-- General Tab -->
                  <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">General</h3>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="createAttributeSet">Attribute Set <span class="text-danger">*</span></label>
                              <select class="form-control" id="createAttributeSet" name="attribute_set_id" required>
                                <option value="">Please Select</option>
                                <!-- Options will be populated via AJAX -->
                              </select>
                              <div class="invalid-feedback"></div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="createName">Name <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" id="createName" name="name" required placeholder="Enter attribute name">
                              <div class="invalid-feedback"></div>
                            </div>
                          </div>
                        </div>
                        
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="createSlug">Slug</label>
                              <input type="text" class="form-control" id="createSlug" name="slug" placeholder="Auto-generated from name">
                              <div class="invalid-feedback"></div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="createCategories">Categories</label>
                              <select class="form-control" id="createCategories" name="categories[]" multiple>
                                <!-- Categories will be loaded via AJAX -->
                              </select>
                              <small class="form-text text-muted">Select categories where this attribute should be available (optional)</small>
                              <div class="invalid-feedback"></div>
                            </div>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-md-12">
                            <div class="form-group">
                              <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="createFilterable" name="is_filterable" value="1">
                                <label class="custom-control-label" for="createFilterable">
                                  Use this attribute for filtering products
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Values Tab -->
                  <div class="tab-pane fade" id="values" role="tabpanel">
                    <div class="card">
                      <div class="card-header">
                        <h3 class="card-title">Values</h3>
                      </div>
                      <div class="card-body">
                        <div class="form-group">
                          <label>Value</label>
                          <div id="attribute-values-container">
                            <!-- Dynamic values will be added here -->
                          </div>
                          <button type="button" class="btn btn-secondary btn-sm mt-2" id="addNewValueBtn">
                            <i class="fas fa-plus"></i> Add New Value
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="createAttributeBtn">
              <i class="fas fa-save" id="buttonIcon"></i> <span id="buttonText">Create Attribute</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="viewAttributeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Attribute Details</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body" id="attributeDetailsContent">
          <!-- Attribute details will be loaded here -->
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
  <link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
  
  <style>
    .modal-xl {
      max-width: 1200px;
    }

    .table-responsive {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .dataTables_wrapper .dataTables_filter input {
      border-radius: 20px;
      border: 1px solid #ddd;
      padding: 8px 15px;
    }

    .dataTables_wrapper .dataTables_length select {
      border-radius: 6px;
      border: 1px solid #ddd;
      padding: 5px 10px;
    }

    #attributesTable th {
      background: #f8f9fa;
      font-weight: 600;
      border-bottom: 2px solid #dee2e6;
    }

    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      z-index: 9999;
      justify-content: center;
      align-items: center;
    }

    .loading-spinner {
      color: white;
      font-size: 24px;
    }

    .nav-pills .nav-link {
      border-radius: 0;
      border-bottom: 1px solid #dee2e6;
    }

    .nav-pills .nav-link.active {
      background-color: #007bff;
      border-color: #007bff;
    }

    .attribute-value-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
      padding: 8px;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      background: #f8f9fa;
    }

    .attribute-value-item .drag-handle {
      cursor: move;
      margin-right: 10px;
      color: #6c757d;
    }

    .attribute-value-item input {
      flex: 1;
      margin-right: 10px;
    }

    .attribute-value-item .btn-danger {
      padding: 2px 8px;
      font-size: 12px;
    }
  </style>
@endpush

@push('scripts')
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
  
  <script>
    $(document).ready(function() {
      let currentAttributeValues = [];

      // Initialize server-side DataTable
      const table = $('#attributesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.attributes.index') }}',
          type: 'GET'
        },
        columns: [{
            data: 'id',
            name: 'id'
          },
          {
            data: 'name',
            name: 'name',
            orderable: true,
            searchable: true
          },
          {
            data: 'attribute_set',
            name: 'attribute_set',
            orderable: false,
            searchable: false
          },
          {
            data: 'values_count',
            name: 'values_count',
            orderable: false,
            searchable: false
          },
          {
            data: 'is_filterable',
            name: 'is_filterable'
          },
          {
            data: 'created_at',
            name: 'created_at'
          },
          {
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false
          }
        ],
        order: [
          [0, 'desc']
        ],
        pageLength: 25,
        responsive: true,
        language: {
          processing: '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>'
        }
      });

      // Initialize Select2 when modal is shown
      $('#createAttributeModal').on('shown.bs.modal', function() {
        loadAttributeSets();
        loadCategories();
        initializeValuesSortable();
        initializeSelect2();
      });

      // Initialize Select2 for categories
      function initializeSelect2() {
        $('#createCategories').select2({
          theme: 'bootstrap4',
          placeholder: 'Select categories (optional)',
          allowClear: true,
          dropdownParent: $('#createAttributeModal'),
          width: '100%'
        });
      }

      // Function to load attribute sets
      function loadAttributeSets() {
        $.ajax({
          url: '{{ route('admin.attribute-sets.index') }}',
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            const select = $('#createAttributeSet');
            select.empty().append('<option value="">Please Select</option>');
            
            if (response.data && response.data.length > 0) {
              response.data.forEach(function(attributeSet) {
                const name = $(attributeSet.name).text(); // Extract text from HTML
                select.append(`<option value="${attributeSet.id}">${name}</option>`);
              });
            }
          },
          error: function(xhr) {
            console.error('Error loading attribute sets:', xhr);
          }
        });
      }

      // Function to load categories
      function loadCategories() {
        $.ajax({
          url: '{{ route('admin.attributes.categories') }}',
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(categories) {
            const select = $('#createCategories');
            select.empty();
            
            if (categories && categories.length > 0) {
              // Group categories by parent
              const grouped = {};
              const rootCategories = [];
              
              categories.forEach(function(category) {
                if (category.parent_id === null) {
                  rootCategories.push(category);
                } else {
                  if (!grouped[category.parent_id]) {
                    grouped[category.parent_id] = [];
                  }
                  grouped[category.parent_id].push(category);
                }
              });
              
              // Add root categories and their children
              rootCategories.forEach(function(category) {
                select.append(`<option value="${category.id}">${category.text}</option>`);
                
                // Add child categories with indentation
                if (grouped[category.id]) {
                  grouped[category.id].forEach(function(child) {
                    select.append(`<option value="${child.id}">— ${child.text}</option>`);
                  });
                }
              });
            }
            
            // Reinitialize Select2 after loading data
            if (select.hasClass('select2-hidden-accessible')) {
              select.select2('destroy');
            }
            initializeSelect2();
          },
          error: function(xhr) {
            console.error('Error loading categories:', xhr);
          }
        });
      }

      // Promise-based versions for edit functionality
      function loadAttributeSetsPromise() {
        return $.ajax({
          url: '{{ route('admin.attribute-sets.index') }}',
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).then(function(response) {
          const select = $('#createAttributeSet');
          select.empty().append('<option value="">Please Select</option>');
          
          if (response.data && response.data.length > 0) {
            response.data.forEach(function(attributeSet) {
              const name = $(attributeSet.name).text(); // Extract text from HTML
              select.append(`<option value="${attributeSet.id}">${name}</option>`);
            });
          }
        });
      }

      function loadCategoriesPromise() {
        return $.ajax({
          url: '{{ route('admin.attributes.categories') }}',
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        }).then(function(categories) {
          const select = $('#createCategories');
          select.empty();
          
          if (categories && categories.length > 0) {
            // Group categories by parent
            const grouped = {};
            const rootCategories = [];
            
            categories.forEach(function(category) {
              if (category.parent_id === null) {
                rootCategories.push(category);
              } else {
                if (!grouped[category.parent_id]) {
                  grouped[category.parent_id] = [];
                }
                grouped[category.parent_id].push(category);
              }
            });
            
            // Add root categories and their children
            rootCategories.forEach(function(category) {
              select.append(`<option value="${category.id}">${category.text}</option>`);
              
              // Add child categories with indentation
              if (grouped[category.id]) {
                grouped[category.id].forEach(function(child) {
                  select.append(`<option value="${child.id}">— ${child.text}</option>`);
                });
              }
            });
          }
          
          // Reinitialize Select2 after loading data
          if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
          }
          initializeSelect2();
        });
      }

      // Auto-generate slug from name
      $('#createName').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
          .replace(/[^a-z0-9]+/g, '-')
          .replace(/(^-|-$)/g, '');
        $('#createSlug').val(slug);
      });

      // Initialize sortable for values
      function initializeValuesSortable() {
        const container = document.getElementById('attribute-values-container');
        if (container) {
          new Sortable(container, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function(evt) {
              // Update order when items are moved
              updateValueOrder();
            }
          });
        }
      }

      // Add new value
      $('#addNewValueBtn').on('click', function() {
        addNewValue('');
      });

      // Add value function
      function addNewValue(value = '') {
        const index = Date.now();
        const html = `
          <div class="attribute-value-item" data-index="${index}">
            <i class="fas fa-grip-vertical drag-handle"></i>
            <input type="text" class="form-control" name="values[]" value="${value}" placeholder="Enter value">
            <button type="button" class="btn btn-danger btn-sm remove-value">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        `;
        $('#attribute-values-container').append(html);
      }

      // Remove value
      $(document).on('click', '.remove-value', function() {
        $(this).closest('.attribute-value-item').remove();
      });

      // Update value order
      function updateValueOrder() {
        $('#attribute-values-container .attribute-value-item').each(function(index) {
          $(this).find('input[name="values[]"]').attr('data-order', index);
        });
      }

      // Function to reset modal to create mode
      function resetModalToCreateMode() {
        // Reset modal title and button
        $('#modalTitle').text('Create New Attribute');
        $('#buttonText').text('Create Attribute');
        $('#buttonIcon').removeClass('fa-edit').addClass('fa-save');
        $('#createAttributeBtn').removeClass('btn-success').addClass('btn-primary');

        // Reset form method and attribute ID
        $('#formMethod').val('POST');
        $('#attributeId').val('');

        // Reset to General tab
        $('#general-tab').tab('show');

        // Clear values
        $('#attribute-values-container').empty();
        currentAttributeValues = [];

        // Clear Select2 categories selection
        if ($('#createCategories').hasClass('select2-hidden-accessible')) {
          $('#createCategories').val(null).trigger('change');
        }
      }

      // Handle modal close - reset forms
      $('.modal').on('hidden.bs.modal', function() {
        // Reset form
        const form = $(this).find('form')[0];
        if (form) {
          form.reset();
        }

        // Clear validation states
        $(this).find('.form-control').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('');

        // Destroy Select2 instances
        if ($('#createCategories').hasClass('select2-hidden-accessible')) {
          $('#createCategories').select2('destroy');
        }

        // Reset modal to create mode
        if ($(this).attr('id') === 'createAttributeModal') {
          resetModalToCreateMode();
        }
      });

      // Reset modal when create button is clicked
      $('button[data-target="#createAttributeModal"]').on('click', function() {
        resetModalToCreateMode();
      });

      // Filterable filter
      $('#filterableFilter').on('change', function() {
        const filterable = $(this).val();
        table.column(4).search(filterable).draw(); // Filterable is column index 4
      });

      // Clear filters
      $('#clearFiltersBtn').on('click', function() {
        $('#filterableFilter').val('');
        table.search('').columns().search('').draw();
        showAlert('All filters cleared', 'info');
      });

      // Export attributes
      $('#exportAttributesBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);

        setTimeout(function() {
          $btn.html(originalText).prop('disabled', false);
          showAlert('Export functionality would be implemented here', 'warning');
        }, 2000);
      });

      // Refresh table
      $('#refreshTableBtn').on('click', function() {
        table.ajax.reload();
        showAlert('Table refreshed successfully!', 'info');
      });

      // Create/Update Attribute
      $('#createAttributeBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isEdit = $('#attributeId').val() !== '';
        const buttonLoadingText = isEdit ? '<i class="fas fa-spinner fa-spin"></i> Updating...' :
          '<i class="fas fa-spinner fa-spin"></i> Creating...';

        $btn.html(buttonLoadingText).prop('disabled', true);

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Collect form data manually to properly handle arrays
        const formData = new FormData();
        
        // Add basic fields
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        if (isEdit) {
          formData.append('_method', 'PUT');
        }
        
        formData.append('name', $('#createName').val());
        formData.append('attribute_set_id', $('#createAttributeSet').val());
        formData.append('slug', $('#createSlug').val());
        formData.append('is_filterable', $('#createFilterable').is(':checked') ? '1' : '0');
        
        // Add categories
        const categories = $('#createCategories').val() || [];
        if (categories.length > 0) {
          categories.forEach(function(categoryId, index) {
            formData.append(`categories[${index}]`, categoryId);
          });
        }
        
        // Add attribute values
        const values = [];
        $('#attribute-values-container .attribute-value-item').each(function() {
          const value = $(this).find('input[name="values[]"]').val().trim();
          if (value) {
            values.push(value);
          }
        });
        
        if (values.length > 0) {
          values.forEach(function(value, index) {
            formData.append(`values[${index}]`, value);
          });
        }
        const url = isEdit ? '{{ route('admin.attributes.update', ':id') }}'.replace(':id', $('#attributeId').val()) :
          '{{ route('admin.attributes.store') }}';
        const method = isEdit ? 'POST' : 'POST';

        $.ajax({
          url: url,
          type: method,
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            console.log(response);
            if (response.success) {
              $('#createAttributeModal').modal('hide');
              table.ajax.reload();

              Swal.fire({
                icon: 'success',
                title: response.title || 'Success',
                text: response.message,
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
              });
            }
          },
          error: function(xhr) {
            if (xhr.status === 422) {
              const errors = xhr.responseJSON?.errors || {};
              Object.keys(errors).forEach(key => {
                const field = $(`[name="${key}"]`);
                field.addClass('is-invalid');
                field.next('.invalid-feedback').text(errors[key][0]);
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `An error occurred while ${isEdit ? 'updating' : 'creating'} the attribute.`,
                position: 'top-end',
                toast: true,
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false
              });
            }
          },
          complete: function() {
            $btn.html(originalText).prop('disabled', false);
          }
        });
      });

      // View Attribute
      $(document).on('click', '.view-attribute', function() {
        const attributeId = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.attributes.show', ':id') }}'.replace(':id', attributeId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const attribute = response.attribute;
              
              $('#attributeDetailsContent').html(`
                <div class="row">
                  <div class="col-md-6">
                    <strong>Name:</strong> ${attribute.name}<br>
                    <strong>Slug:</strong> ${attribute.slug || 'N/A'}<br>
                    <strong>Attribute Set:</strong> ${attribute.attribute_set_name || 'N/A'}<br>
                  </div>
                  <div class="col-md-6">
                    <strong>Filterable:</strong> ${attribute.is_filterable ? 'Yes' : 'No'}<br>
                    <strong>Values Count:</strong> ${attribute.attribute_values ? attribute.attribute_values.length : 0}<br>
                    <strong>Created:</strong> ${new Date(attribute.created_at).toLocaleString()}<br>
                  </div>
                </div>
              `);
              $('#viewAttributeModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading attribute details');
          }
        });
      });

      // Edit Attribute
      $(document).on('click', '.edit-attribute', function() {
        const attributeId = $(this).data('id');
        
        // Set modal mode to edit first
        $('#modalTitle').text('Edit Attribute');
        $('#buttonText').text('Update Attribute');
        $('#buttonIcon').removeClass('fa-save').addClass('fa-edit');
        $('#createAttributeBtn').removeClass('btn-primary').addClass('btn-success');

        // Set form method for update
        $('#formMethod').val('PUT');
        $('#attributeId').val(attributeId);

        // Show modal and load data
        $('#createAttributeModal').modal('show');
        
        // Load attribute sets and categories first, then populate the form
        Promise.all([
          loadAttributeSetsPromise(),
          loadCategoriesPromise()
        ]).then(function() {
          // Now load the attribute data
          return $.ajax({
            url: '{{ route('admin.attributes.edit', ':id') }}'.replace(':id', attributeId),
            type: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
        }).then(function(response) {
          if (response.success) {
            const attribute = response.attribute;
            
            // Fill form fields
            $('#createName').val(attribute.name);
            $('#createSlug').val(attribute.slug);
            $('#createAttributeSet').val(attribute.attribute_set_id);
            $('#createFilterable').prop('checked', attribute.is_filterable);

            // Set selected categories - ensure Select2 is initialized
            setTimeout(function() {
              if (attribute.category_ids && attribute.category_ids.length > 0) {
                $('#createCategories').val(attribute.category_ids).trigger('change');
              } else {
                $('#createCategories').val(null).trigger('change');
              }
            }, 100);

            // Load attribute values if any
            $('#attribute-values-container').empty();
            if (attribute.attribute_values && attribute.attribute_values.length > 0) {
              attribute.attribute_values.forEach(function(value) {
                addNewValue(value.value || '');
              });
            }
          }
        }).catch(function(xhr) {
          console.error('Error loading attribute data:', xhr);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error loading attribute data',
            position: 'top-end',
            toast: true,
            timer: 5000,
            timerProgressBar: true,
            showConfirmButton: false
          });
        });
      });

      // Delete Attribute
      $(document).on('click', '.delete-attribute', function() {
        const attributeId = $(this).data('id');
        const attributeName = $(this).closest('tr').find('td:eq(1)').find('strong').text().trim();

        Swal.fire({
          title: 'Are you sure?',
          text: `You are about to delete attribute: ${attributeName}. This action cannot be undone!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('admin.attributes.destroy', ':id') }}'.replace(':id', attributeId),
              type: 'DELETE',
              headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success: function(response) {
                if (response.success) {
                  table.ajax.reload();
                  Swal.fire({
                    icon: 'success',
                    title: response.title || 'Success',
                    text: response.message,
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: response.title || 'Error',
                    text: response.message,
                    position: 'top-end',
                    toast: true,
                    timer: 5000,
                    timerProgressBar: true,
                    showConfirmButton: false
                  });
                }
              },
              error: function(xhr) {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'An error occurred while deleting the attribute.',
                  position: 'top-end',
                  toast: true,
                  timer: 5000,
                  timerProgressBar: true,
                  showConfirmButton: false
                });
              }
            });
          }
        });
      });

      // Helper function to show alerts using SweetAlert2
      function showAlert(message, type = 'info') {
        const iconMap = {
          'success': 'success',
          'danger': 'error',
          'warning': 'warning',
          'info': 'info'
        };

        const titleMap = {
          'success': 'Success!',
          'danger': 'Error!',
          'warning': 'Warning!',
          'info': 'Information'
        };

        Swal.fire({
          icon: iconMap[type] || 'info',
          title: titleMap[type] || 'Notification',
          text: message,
          timer: type === 'danger' ? 5000 : 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          position: 'top-end',
          toast: true,
          background: '#fff',
          customClass: {
            popup: 'colored-toast'
          }
        });
      }
    });
  </script>
@endpush