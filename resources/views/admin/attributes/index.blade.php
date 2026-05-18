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
                    <div class="card shadow-sm border-0">
                      <div class="card-header bg-gradient-primary text-white">
                        <h5 class="card-title mb-0">
                          <i class="fas fa-info-circle me-2"></i>General Information
                        </h5>
                      </div>
                      <div class="card-body p-4">
                        <!-- Basic Information -->
                        <div class="form-section mb-4">
                          <h6 class="text-uppercase text-muted mb-3"
                            style="font-size: 0.875rem; font-weight: 600; letter-spacing: 0.5px;">
                            <i class="fas fa-layer-group me-2"></i>Basic Information
                          </h6>
                          <div class="row g-3">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="createAttributeSet" class="form-label fw-semibold">
                                  <i class="fas fa-folder me-1 text-primary"></i>
                                  Attribute Set <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2-single" id="createAttributeSet"
                                  name="attribute_set_id" required>
                                  <option value="">Select an attribute set...</option>
                                  <!-- Options will be populated via AJAX -->
                                </select>
                                <small class="form-text text-muted">
                                  <i class="fas fa-info-circle me-1"></i>Choose which attribute set this belongs to
                                </small>
                                <div class="invalid-feedback"></div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="createName" class="form-label fw-semibold">
                                  <i class="fas fa-tag me-1 text-primary"></i>
                                  Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="createName" name="name" required
                                  placeholder="e.g., Color, Size, Material">
                                <div class="invalid-feedback"></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Additional Settings -->
                        <div class="form-section mb-4">
                          <h6 class="text-uppercase text-muted mb-3"
                            style="font-size: 0.875rem; font-weight: 600; letter-spacing: 0.5px;">
                            <i class="fas fa-cog me-2"></i>Additional Settings
                          </h6>
                          <div class="row g-3">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="createSlug" class="form-label fw-semibold">
                                  <i class="fas fa-link me-1 text-primary"></i>
                                  Slug
                                </label>
                                <input type="text" class="form-control" id="createSlug" name="slug"
                                  placeholder="auto-generated-slug">
                                <small class="form-text text-muted">
                                  <i class="fas fa-magic me-1"></i>Auto-generated from name if left empty
                                </small>
                                <div class="invalid-feedback"></div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="createCategories" class="form-label fw-semibold">
                                  <i class="fas fa-th-list me-1 text-primary"></i>
                                  Categories
                                </label>
                                <select class="form-control" id="createCategories" name="categories[]" multiple>
                                  <!-- Categories will be loaded via AJAX -->
                                </select>
                                <small class="form-text text-muted">
                                  <i class="fas fa-filter me-1"></i>Limit attribute to specific categories (optional)
                                </small>
                                <div class="invalid-feedback"></div>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Options -->
                        <div class="form-section">
                          <h6 class="text-uppercase text-muted mb-3"
                            style="font-size: 0.875rem; font-weight: 600; letter-spacing: 0.5px;">
                            <i class="fas fa-sliders-h me-2"></i>Options
                          </h6>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <div class="card bg-light border-0">
                                  <div class="card-body py-3">
                                    <div class="form-check form-switch">
                                      <input type="checkbox" class="form-check-input" id="createFilterable"
                                        name="is_filterable" value="1" style="cursor: pointer;">
                                      <label class="form-check-label" for="createFilterable" style="cursor: pointer;">
                                        <strong><i class="fas fa-filter me-2 text-primary"></i>Enable Product
                                          Filtering</strong>
                                        <div class="small text-muted mt-1">Allow customers to filter products using this
                                          attribute</div>
                                      </label>
                                    </div>
                                  </div>
                                </div>
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
                        <h3 class="card-title">Attribute Values</h3>
                      </div>
                      <div class="card-body">
                        <div class="form-group">
                          <label class="form-label">Values <span class="text-muted">(Label & Image)</span></label>

                          <!-- Values Container -->
                          <div id="attribute-values-container">
                            <!-- Dynamic value rows will be added here -->
                          </div>

                          <button type="button" class="btn btn-sm btn-secondary mt-3" id="addNewValueBtn">
                            <i class="fas fa-plus mr-1"></i> Add Value
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
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
    rel="stylesheet">

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

    /* Attribute Value Rows - Compact Layout */
    .attribute-value-row {
      display: flex;
      align-items: center;
      margin-bottom: 8px;
      padding: 8px 12px;
      border: 1px solid #dee2e6;
      border-radius: 4px;
      background: #fff;
      gap: 8px;
    }

    .attribute-value-row .drag-handle {
      cursor: move;
      color: #6c757d;
      font-size: 14px;
      padding: 0 4px;
      flex-shrink: 0;
    }

    .attribute-value-row .drag-handle:hover {
      color: #495057;
    }

    .attribute-value-row input.label-input {
      flex: 1;
      min-width: 200px;
    }

    .attribute-value-row .image-input-group {
      display: flex;
      align-items: center;
      gap: 8px;
      flex: 1;
      max-width: 600px;
    }

    .attribute-value-row .image-preview-box {
      width: 40px;
      height: 40px;
      border: 1px solid #ced4da;
      border-radius: 4px;
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      overflow: hidden;
      transition: all 0.2s;
      flex-shrink: 0;
    }

    .attribute-value-row .image-preview-box:hover {
      border-color: #007bff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }

    .attribute-value-row .image-preview-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 3px;
    }

    .attribute-value-row .image-preview-box i {
      color: #adb5bd;
      font-size: 16px;
    }

    .attribute-value-row .image-name-input {
      flex: 1;
      min-width: 150px;
    }

    .attribute-value-row .btn-select-image {
      flex-shrink: 0;
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }

    .attribute-value-row .delete-row {
      cursor: pointer;
      color: #dc3545;
      font-size: 16px;
      padding: 0 8px;
      transition: color 0.2s;
      flex-shrink: 0;
    }

    .attribute-value-row .delete-row:hover {
      color: #c82333;
    }

    /* Enhanced Select2 Styling */
    .select2-container--bootstrap4 .select2-selection--single {
      height: 38px !important;
      border-radius: 6px !important;
      border: 1px solid #ced4da !important;
      transition: all 0.2s !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
      line-height: 36px !important;
      padding-left: 12px !important;
      padding-right: 36px !important;
      color: #495057 !important;
      font-size: 1rem !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
      height: 36px !important;
      top: 1px !important;
      right: 1px !important;
      width: 32px !important;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
      color: #6c757d !important;
    }

    .select2-container--bootstrap4 .select2-selection--multiple {
      min-height: calc(1.5em + 0.75rem + 2px);
      border-radius: 6px;
      border: 1px solid #ced4da;
      transition: all 0.2s;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__rendered {
      padding: 2px 8px;
      min-height: calc(1.5em + 0.75rem);
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice {
      background-color: #667eea;
      border-color: #5a67d8;
      color: #fff;
      padding: 4px 10px;
      margin: 3px 4px 3px 0;
      border-radius: 4px;
      font-size: 0.875rem;
      line-height: 1.5;
      display: inline-flex;
      align-items: center;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove {
      color: #fff;
      margin-right: 6px;
      font-weight: bold;
      font-size: 1.1rem;
      line-height: 1;
      opacity: 0.8;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice__remove:hover {
      opacity: 1;
      color: #ffe0e0;
    }

    .select2-container--bootstrap4.select2-container--focus .select2-selection {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .select2-dropdown {
      border-color: #ced4da;
      border-radius: 6px;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
      margin-top: 4px;
    }

    .select2-container--bootstrap4 .select2-results__option {
      padding: 8px 12px;
      font-size: 0.95rem;
    }

    .select2-container--bootstrap4 .select2-results__option--highlighted {
      background-color: #667eea;
      color: #fff;
    }

    .select2-search--dropdown .select2-search__field {
      border: 1px solid #ced4da;
      border-radius: 6px;
      padding: 0.5rem 0.75rem;
      font-size: 0.95rem;
    }

    .select2-search--dropdown .select2-search__field:focus {
      border-color: #667eea;
      outline: 0;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Make select boxes more prominent */
    #createAttributeSet,
    #createCategories {
      font-size: 1rem;
    }

    /* Fix for category indentation display */
    .select2-results__option[role="option"] {
      white-space: normal;
      word-wrap: break-word;
    }

    .select2-container--bootstrap4 .select2-selection--multiple .select2-search--inline .select2-search__field {
      margin-top: 6px;
      margin-bottom: 3px;
      padding: 0 8px;
      font-size: 1rem;
    }

    /* Form group spacing */
    .form-group label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 0.5rem;
    }

    /* Enhanced form styling */
    .bg-gradient-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .form-section {
      padding: 1.5rem;
      border-radius: 8px;
      background: #f8f9fa;
      border-left: 4px solid #667eea;
    }

    .form-label {
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
      display: block;
    }

    .form-label .fas {
      font-size: 0.85rem;
    }

    .form-control {
      border-radius: 6px;
      border: 1px solid #ced4da;
      transition: all 0.2s;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .form-check-input:checked {
      background-color: #667eea;
      border-color: #667eea;
    }

    .form-check-input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .card.shadow-sm {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .row.g-3 {
      row-gap: 1rem;
    }

    .fw-semibold {
      font-weight: 600;
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

      // Initialize Select2 for both dropdowns
      function initializeSelect2() {
        // Initialize Attribute Set select (single selection)
        $('#createAttributeSet').select2({
          theme: 'bootstrap4',
          placeholder: 'Select an attribute set...',
          allowClear: true,
          dropdownParent: $('#createAttributeModal'),
          width: '100%',
          minimumResultsForSearch: 5, // Show search box if more than 5 items
        });

        // Initialize Categories select (multiple selection)
        $('#createCategories').select2({
          theme: 'bootstrap4',
          placeholder: 'Select categories (optional)',
          allowClear: true,
          dropdownParent: $('#createAttributeModal'),
          width: '100%',
          closeOnSelect: false, // Keep dropdown open for multiple selections
          tags: false, // Don't allow creating new tags
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
            select.empty().append('<option value="">Select an attribute set...</option>');

            if (response.data && response.data.length > 0) {
              response.data.forEach(function(attributeSet) {
                // Handle the name - it's already translated from the server
                const name = attributeSet.name || 'Untitled';
                select.append(`<option value="${attributeSet.id}">${name}</option>`);
              });
            }

            // Reinitialize Select2 after loading data
            if (select.hasClass('select2-hidden-accessible')) {
              select.select2('destroy');
            }
            select.select2({
              theme: 'bootstrap4',
              placeholder: 'Select an attribute set...',
              allowClear: true,
              dropdownParent: $('#createAttributeModal'),
              width: '100%',
              minimumResultsForSearch: 5,
            });
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
            select.select2({
              theme: 'bootstrap4',
              placeholder: 'Select categories (optional)',
              allowClear: true,
              dropdownParent: $('#createAttributeModal'),
              width: '100%',
              closeOnSelect: false,
              tags: false,
            });
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
          select.empty().append('<option value="">Select an attribute set...</option>');

          if (response.data && response.data.length > 0) {
            response.data.forEach(function(attributeSet) {
              // Handle the name - it's already translated from the server
              const name = attributeSet.name || 'Untitled';
              select.append(`<option value="${attributeSet.id}">${name}</option>`);
            });
          }

          // Reinitialize Select2
          if (select.hasClass('select2-hidden-accessible')) {
            select.select2('destroy');
          }
          select.select2({
            theme: 'bootstrap4',
            placeholder: 'Select an attribute set...',
            allowClear: true,
            dropdownParent: $('#createAttributeModal'),
            width: '100%',
            minimumResultsForSearch: 5,
          });
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
          select.select2({
            theme: 'bootstrap4',
            placeholder: 'Select categories (optional)',
            allowClear: true,
            dropdownParent: $('#createAttributeModal'),
            width: '100%',
            closeOnSelect: false,
            tags: false,
          });
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

      // Add value function with label and image support
      function addNewValue(label = '', imageUrl = '', imageId = '') {
        const index = Date.now();
        const imagePreview = imageUrl ?
          `<img src="${imageUrl}" alt="Preview">` :
          '<i class="fas fa-image"></i>';

        const html = `
          <div class="attribute-value-row" data-index="${index}">
            <i class="fas fa-grip-vertical drag-handle"></i>

            <div class="image-input-group">
              <div class="image-preview-box" data-index="${index}" title="Click to select image">
                ${imagePreview}
              </div>
              <input type="text" class="form-control label-input" name="values[${index}][label]"
                     value="${label}" placeholder="Enter label (e.g., Small, Red)">
              <input type="hidden" class="image-id-input" name="values[${index}][image_id]" value="${imageId}">
              <button type="button" class="btn btn-sm btn-outline-primary btn-select-image" data-index="${index}" title="Select image">
                <i class="fas fa-image"></i>
              </button>
            </div>

            <i class="fas fa-trash delete-row" title="Delete row"></i>
          </div>
        `;
        $('#attribute-values-container').append(html);
      }

      // Handle image selection - can click preview box or select button
      $(document).on('click', '.btn-select-image, .image-preview-box', function() {
        const index = $(this).data('index');
        const row = $(`.attribute-value-row[data-index="${index}"]`);

        // Check if MediaManager is available
        if (typeof window.MediaManager === 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Media Manager is not available. Please check if it is properly loaded.',
            position: 'top-end',
            toast: true,
            timer: 3000,
            showConfirmButton: false
          });
          return;
        }

        try {
          const mediaManager = new MediaManager({
            container: document.createElement('div'),
            endpoints: window.MediaSelectorConfig?.endpoints || {},
            modal: true,
            multiple: false,
            onSelect: (files) => {
              if (files && files.length > 0) {
                const file = files[0];

                // Update preview
                row.find('.image-preview-box').html(`<img src="${file.url}" alt="Preview">`);

                // Store image ID
                row.find('.image-id-input').val(file.id);

                showAlert('Image selected successfully!', 'success');
              }
            }
          });

          mediaManager.open();
        } catch (error) {
          console.error('Error opening media manager:', error);
          showAlert('Failed to open media manager', 'error');
        }
      }); // Clear image with right-click on preview
      $(document).on('contextmenu', '.image-preview-box', function(e) {
        e.preventDefault();
        const row = $(this).closest('.attribute-value-row');
        $(this).html('<i class="fas fa-image"></i>');
        row.find('.image-id-input').val('');
        showAlert('Image cleared', 'info');
      });

      // Remove value
      $(document).on('click', '.delete-row', function() {
        $(this).closest('.attribute-value-row').remove();
      });

      // Update value order
      function updateValueOrder() {
        $('#attribute-values-container .attribute-value-row').each(function(index) {
          $(this).find('input[name^="values"]').each(function() {
            const name = $(this).attr('name');
            const newName = name.replace(/values\[\d+\]/, `values[${index}]`);
            $(this).attr('name', newName);
          });
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
        if ($('#createAttributeSet').hasClass('select2-hidden-accessible')) {
          $('#createAttributeSet').select2('destroy');
        }
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

        // Add attribute values with labels and images
        $('#attribute-values-container .attribute-value-row').each(function(index) {
          const label = $(this).find('.label-input').val().trim();
          const imageId = $(this).find('.image-id-input').val();

          if (label) {
            formData.append(`values[${index}][label]`, label);
            if (imageId) {
              formData.append(`values[${index}][image_id]`, imageId);
            }
          }
        });
        const url = isEdit ? '{{ route('admin.attributes.update', ':id') }}'.replace(':id', $('#attributeId')
            .val()) :
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
                addNewValue(
                  value.value || value.label || '',
                  value.image_url || '',
                  value.image_id || ''
                );
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
