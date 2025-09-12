@extends('admin.layouts.master_layout')

@section('pageTitle', 'Options Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Options Management</h4>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createOptionModal">
              <i class="fas fa-plus"></i> Add New Option
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
                <label for="typeFilter">Filter by Type</label>
                <select class="form-control" id="typeFilter">
                  <option value="">All Types</option>
                  <option value="text">Text</option>
                  <option value="textarea">Textarea</option>
                  <option value="select">Select</option>
                  <option value="multiselect">Multiselect</option>
                  <option value="radio">Radio</option>
                  <option value="checkbox">Checkbox</option>
                  <option value="date">Date</option>
                  <option value="datetime">Datetime</option>
                  <option value="file">File</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="requiredFilter">Filter by Required</label>
                <select class="form-control" id="requiredFilter">
                  <option value="">All</option>
                  <option value="Required">Required</option>
                  <option value="Optional">Optional</option>
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
                  <button type="button" class="btn btn-success" id="exportOptionsBtn">
                    <i class="fas fa-download"></i> Export Options
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="optionsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Required</th>
                  <th>Global</th>
                  <th>Position</th>
                  <th>Values Count</th>
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
@endsection

@push('styles')
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

    #optionsTable th {
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

    /* Option Form Styling */
    .nav-pills .nav-link {
      border-radius: 0.375rem;
      margin-bottom: 0.5rem;
      color: #6c757d;
      border: 1px solid transparent;
    }

    .nav-pills .nav-link:hover {
      background-color: #e9ecef;
    }

    .nav-pills .nav-link.active {
      background-color: #007bff;
      border-color: #007bff;
    }

    .option-value-item {
      border: 1px solid #dee2e6;
      border-radius: 0.375rem;
      padding: 1rem;
      margin-bottom: 1rem;
      background-color: #f8f9fa;
    }

    .option-value-item .form-group:last-child {
      margin-bottom: 0;
    }

    .option-value-item {
      position: relative;
    }

    .drag-handle {
      cursor: move;
      color: #6c757d;
      margin-right: 0.5rem;
    }

    .drag-handle:hover {
      color: #495057;
    }

    /* Values table styling */
    #values-section table {
      margin-bottom: 0;
    }

    #values-section .table-bordered th,
    #values-section .table-bordered td {
      border: 1px solid #dee2e6;
      vertical-align: middle;
    }

    #values-section .table thead th {
      background-color: #f8f9fa;
      border-bottom: 2px solid #dee2e6;
      font-weight: 600;
    }

    #values-section .form-control {
      border-radius: 0.25rem;
    }

    #values-section .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
    }

    /* Enhanced table padding and spacing */
    .table-bordered td,
    .table-bordered th {
      padding: 12px 15px !important;
      vertical-align: middle;
      border: 1px solid #dee2e6;
    }

    /* Header styling */
    .table thead th {
      background-color: #f8f9fa;
      font-weight: 600;
      border-bottom: 2px solid #dee2e6;
      text-align: center;
    }

    /* Form controls within table */
    .table .form-control {
      border: 1px solid #ced4da;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 14px;
    }

    /* Center align specific columns */
    .table td:first-child,
    .table td:last-child {
      text-align: center;
      vertical-align: middle;
    }

    /* Button styling */
    .remove-value-btn {
      padding: 6px 10px;
      border-radius: 4px;
    }

    .remove-value-btn:hover {
      background-color: #dc3545;
      border-color: #dc3545;
    }
  </style>
@endpush

<!-- Option Modals -->
<div class="modal fade" id="createOptionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-xl" role="document">
    <form id="createOptionForm" method="POST">
      @csrf
      <input type="hidden" id="optionId" name="option_id">
      <input type="hidden" id="formMethod" name="_method" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Create Option</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body p-0">
          <div class="row no-gutters" style="min-height: 500px;">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 bg-light border-right">
              <div class="p-3">
                <h6 class="text-muted mb-3">Option Information</h6>
                <div class="nav flex-column nav-pills" id="option-tab" role="tablist">
                  <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab">
                    <i class="fas fa-info-circle mr-2"></i>General
                  </a>
                  <a class="nav-link" id="values-tab" data-toggle="pill" href="#values" role="tab">
                    <i class="fas fa-list mr-2"></i>Values
                  </a>
                </div>
              </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-md-9">
              <div class="p-4">
                <div class="tab-content" id="option-tabContent">
                  <!-- General Tab -->
                  <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <h5 class="mb-4">General</h5>

                    <div class="form-group">
                      <label for="createName">Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="createName" name="name[en]" required
                        placeholder="Enter option name">
                      <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                      <label for="createType">Type <span class="text-danger">*</span></label>
                      <select class="form-control" id="createType" name="type" required>
                        <option value="" selected="">
                          Please Select
                        </option>
                        <optgroup label="Text">
                          <option value="field">
                            Field
                          </option>
                          <option value="textarea">
                            Textarea
                          </option>
                        </optgroup>

                        <optgroup label="Select">
                          <option value="dropdown">
                            Dropdown
                          </option>

                          <option value="checkbox">
                            Checkbox
                          </option>

                          <option value="checkbox_custom">
                            Custom Checkbox
                          </option>

                          <option value="radio">
                            Radio Button
                          </option>

                          <option value="radio_custom">
                            Custom Radio Button
                          </option>

                          <option value="multiple_select">
                            Multiple Select
                          </option>
                        </optgroup>

                        <optgroup label="Date">
                          <option value="date">
                            Date
                          </option>

                          <option value="date_time">
                            Date &amp; Time
                          </option>

                          <option value="time">
                            Time
                          </option>
                        </optgroup>
                      </select>
                      <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="createRequired" name="is_required"
                          value="1">
                        <label class="custom-control-label" for="createRequired">This option is required</label>
                      </div>
                    </div>

                    <div class="form-group">
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="createGlobal" name="is_global"
                          value="1">
                        <label class="custom-control-label" for="createGlobal">This option is global</label>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="createPosition">Position</label>
                      <input type="number" class="form-control" id="createPosition" name="position" min="0"
                        placeholder="Enter position (optional)">
                      <div class="invalid-feedback"></div>
                    </div>
                  </div>

                  <!-- Values Tab -->
                  <div class="tab-pane fade" id="values" role="tabpanel">
                    <h5 class="mb-4">Values</h5>

                    <div id="values-content">
                      <div class="alert alert-info" id="values-placeholder">
                        <i class="fas fa-info-circle mr-2"></i>
                        Please select a option type
                      </div>

                      <!-- Simple form layout for Text/Date types -->
                      <div id="values-simple" style="display: none;">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="simplePrice">Price</label>
                              <input type="number" class="form-control" id="simplePrice" name="price"
                                placeholder="" min="0" step="0.01">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label for="simplePriceType">Price Type</label>
                              <select class="form-control" id="simplePriceType" name="price_type">
                                <option value="fixed">Fixed</option>
                                <option value="percent">Percent</option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Table layout for Select types -->
                      <div id="values-table" style="display: none;">
                        <div class="table-responsive">
                          <table class="table table-bordered">
                            <thead>
                              <tr>
                                <th width="5%"><i class="fas fa-grip-vertical"></i></th>
                                <th width="40%">Label</th>
                                <th width="25%">Price</th>
                                <th width="25%">Price Type</th>
                                <th width="5%"></th>
                              </tr>
                            </thead>
                            <tbody id="values-container">
                              <!-- Dynamic values will be added here -->
                            </tbody>
                          </table>
                        </div>

                        <div class="mt-3">
                          <button type="button" class="btn btn-sm btn-primary" id="addValueBtn">
                            <i class="fas fa-plus mr-1"></i> Add Row
                          </button>
                        </div>
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
          <button type="button" class="btn btn-primary" id="createOptionBtn">
            <i class="fas fa-save" id="buttonIcon"></i> <span id="buttonText">Save</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="viewOptionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Option Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="optionDetailsContent">
        <!-- Option details will be loaded here -->
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    $(document).ready(function() {
      // Initialize server-side DataTable
      const table = $('#optionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.options.index') }}',
          type: 'GET'
        },
        columns: [{
            data: 'id',
            name: 'id'
          },
          {
            data: 'name',
            name: 'name',
            orderable: false,
            searchable: true
          },
          {
            data: 'type',
            name: 'type'
          },
          {
            data: 'is_required',
            name: 'is_required'
          },
          {
            data: 'is_global',
            name: 'is_global'
          },
          {
            data: 'position',
            name: 'position'
          },
          {
            data: 'values_count',
            name: 'values_count',
            orderable: false
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

      // Function to reset modal to create mode
      function resetModalToCreateMode() {
        $('#modalTitle').text('Create Option');
        $('#buttonText').text('Save');
        $('#buttonIcon').removeClass('fa-edit').addClass('fa-save');
        $('#createOptionBtn').removeClass('btn-success').addClass('btn-primary');
        $('#formMethod').val('POST');
        $('#optionId').val('');

        // Reset to General tab
        $('#general-tab').tab('show');

        // Clear values and reset type change
        $('#values-container').empty();
        $('#values-simple').hide();
        $('#values-table').hide();
        $('#values-placeholder').show();

        // Clear simple form fields
        $('#simplePrice').val('');
        $('#simplePriceType').val('fixed');

        // Reset type selection
        $('#createType').val('').trigger('change');
      }

      // Function to handle type change
      function handleTypeChange() {
        const selectedType = $('#createType').val();
        // Define which types need table layout (with labels)
        const tableTypes = ['dropdown', 'checkbox', 'checkbox_custom', 'radio', 'radio_custom', 'multiple_select'];
        // Define which types need simple layout (just price)
        const simpleTypes = ['field', 'textarea', 'date', 'date_time', 'time'];

        console.log('Type changed to:', selectedType); // Debug log

        // Hide all sections first
        $('#values-placeholder').hide();
        $('#values-simple').hide();
        $('#values-table').hide();

        if (tableTypes.includes(selectedType)) {
          // Show table layout for select types
          $('#values-table').show();

          // If no values exist, add one default value
          if ($('#values-container tr.option-value-item').length === 0) {
            console.log('Adding default value field for table'); // Debug log
            addValueField();
          }
        } else if (simpleTypes.includes(selectedType)) {
          // Show simple layout for text/date types
          $('#values-simple').show();
        } else {
          // Show placeholder for no selection or unsupported types
          $('#values-placeholder').show();
          $('#values-container').empty();
        }
      }

      // Handle option type change
      $('#createType').on('change', handleTypeChange);

      // Add value field
      function addValueField(valueData = null) {
        const valueIndex = $('#values-container tr').length;
        const valueHtml = `
          <tr class="option-value-item" data-index="${valueIndex}">
            <td class="text-center">
              <i class="fas fa-grip-vertical drag-handle"></i>
            </td>
            <td>
              <input type="text" class="form-control" name="values[${valueIndex}][name]"
                     value="${valueData?.name || ''}" placeholder="" required>
            </td>
            <td>
              <input type="number" class="form-control" name="values[${valueIndex}][price]"
                     value="${valueData?.price || ''}" placeholder="" min="0" step="0.01">
            </td>
            <td>
              <select class="form-control" name="values[${valueIndex}][price_type]">
                <option value="fixed" ${valueData?.price_type === 'fixed' || !valueData?.price_type ? 'selected' : ''}>Fixed</option>
                <option value="percent" ${valueData?.price_type === 'percent' ? 'selected' : ''}>Percent</option>
              </select>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-sm btn-danger remove-value-btn">
                <i class="fas fa-trash"></i>
              </button>
              <input type="hidden" name="values[${valueIndex}][id]" value="${valueData?.id || ''}">
              <input type="hidden" name="values[${valueIndex}][position]" value="${valueData?.position || valueIndex}">
            </td>
          </tr>
        `;

        $('#values-container').append(valueHtml);
      }

      // Add value button click
      $(document).on('click', '#addValueBtn', function() {
        addValueField();
      });

      // Remove value button click
      $(document).on('click', '.remove-value-btn', function() {
        const valueItem = $(this).closest('tr.option-value-item');
        valueItem.remove();

        // Reindex remaining items
        $('#values-container tr.option-value-item').each(function(index) {
          $(this).attr('data-index', index);
          $(this).find('input[name*="[name]"]').attr('name', `values[${index}][name]`);
          $(this).find('input[name*="[price]"]').attr('name', `values[${index}][price]`);
          $(this).find('select[name*="[price_type]"]').attr('name', `values[${index}][price_type]`);
          $(this).find('input[name*="[position]"]').attr('name', `values[${index}][position]`);
          $(this).find('input[name*="[id]"]').attr('name', `values[${index}][id]`);
        });
      });

      // Handle modal close
      $('.modal').on('hidden.bs.modal', function() {
        const form = $(this).find('form')[0];
        if (form) {
          form.reset();
        }
        $(this).find('.form-control').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('');

        if ($(this).attr('id') === 'createOptionModal') {
          resetModalToCreateMode();
        }
      });

      // Reset modal when create button is clicked
      $('button[data-target="#createOptionModal"]').on('click', function() {
        resetModalToCreateMode();
      });

      // Handle modal shown event
      $('#createOptionModal').on('shown.bs.modal', function() {
        // Trigger type change to ensure proper display
        handleTypeChange();
      });

      // Type filter
      $('#typeFilter').on('change', function() {
        const type = $(this).val();
        table.column(2).search(type).draw();
      });

      // Required filter
      $('#requiredFilter').on('change', function() {
        const required = $(this).val();
        table.column(3).search(required).draw();
      });

      // Clear filters
      $('#clearFiltersBtn').on('click', function() {
        $('#typeFilter').val('');
        $('#requiredFilter').val('');
        table.search('').columns().search('').draw();
        showAlert('All filters cleared', 'info');
      });

      // Export options
      $('#exportOptionsBtn').on('click', function() {
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

      // Create/Update Option
      $('#createOptionBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isEdit = $('#optionId').val() !== '';
        const buttonLoadingText = isEdit ? '<i class="fas fa-spinner fa-spin"></i> Updating...' :
          '<i class="fas fa-spinner fa-spin"></i> Creating...';

        $btn.html(buttonLoadingText).prop('disabled', true);

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        // Prepare form data based on selected type
        const selectedType = $('#createType').val();
        const tableTypes = ['dropdown', 'checkbox', 'checkbox_custom', 'radio', 'radio_custom',
          'multiple_select'
        ];
        const simpleTypes = ['field', 'textarea', 'date', 'date_time', 'time'];

        const formData = new FormData();

        // Add CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Add basic form fields
        formData.append('name[en]', $('#createName').val());
        formData.append('type', selectedType);
        formData.append('position', $('#createPosition').val() || '');
        formData.append('is_required', $('#createRequired').is(':checked') ? '1' : '0');
        formData.append('is_global', $('#createGlobal').is(':checked') ? '1' : '0');

        // Add method and ID for edit
        if ($('#formMethod').val()) {
          formData.append('_method', $('#formMethod').val());
        }
        if ($('#optionId').val()) {
          formData.append('option_id', $('#optionId').val());
        }

        // Add type-specific data
        if (simpleTypes.includes(selectedType)) {
          // For simple types, add price fields from simple form
          formData.append('price', $('#simplePrice').val() || '');
          formData.append('price_type', $('#simplePriceType').val() || 'fixed');
        } else if (tableTypes.includes(selectedType)) {
          // For table types, add values from table
          $('#values-container tr.option-value-item').each(function(index) {
            const $row = $(this);
            const name = $row.find('input[name*="[name]"]').val();
            const price = $row.find('input[name*="[price]"]').val();
            const priceType = $row.find('select[name*="[price_type]"]').val();
            const position = $row.find('input[name*="[position]"]').val();
            const id = $row.find('input[name*="[id]"]').val();

            if (name) {
              formData.append(`values[${index}][name]`, name);
              formData.append(`values[${index}][price]`, price || '');
              formData.append(`values[${index}][price_type]`, priceType || 'fixed');
              formData.append(`values[${index}][position]`, position || index);
              if (id) {
                formData.append(`values[${index}][id]`, id);
              }
            }
          });
        }

        const url = isEdit ? '{{ route('admin.options.update', ':id') }}'.replace(':id', $('#optionId').val()) :
          '{{ route('admin.options.store') }}';
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
            if (response.success) {
              $('#createOptionModal').modal('hide');
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
                const field = $(`[name="${key}"], [name="${key}[]"]`);
                field.addClass('is-invalid');
                field.closest('.form-group').find('.invalid-feedback').text(errors[key][0]);
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `An error occurred while ${isEdit ? 'updating' : 'creating'} the option.`,
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

      // View Option
      $(document).on('click', '.view-option', function() {
        const optionId = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.options.show', ':id') }}'.replace(':id', optionId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const option = response.option;
              const requiredBadge = option.is_required ?
                '<span class="badge badge-danger">Required</span>' :
                '<span class="badge badge-secondary">Optional</span>';
              const globalBadge = option.is_global ?
                '<span class="badge badge-success">Yes</span>' :
                '<span class="badge badge-secondary">No</span>';

              $('#optionDetailsContent').html(`
                <div class="row">
                  <div class="col-md-6">
                    <strong>Name:</strong> ${option.name || 'N/A'}<br>
                    <strong>Type:</strong> ${option.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}<br>
                    <strong>Position:</strong> ${option.position || 'N/A'}<br>
                  </div>
                  <div class="col-md-6">
                    <strong>Required:</strong> ${requiredBadge}<br>
                    <strong>Global:</strong> ${globalBadge}<br>
                    <strong>Values Count:</strong> ${option.values ? option.values.length : 0}<br>
                    <strong>Created:</strong> ${new Date(option.created_at).toLocaleString()}
                  </div>
                </div>
              `);
              $('#viewOptionModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading option details');
          }
        });
      });

      // Edit Option
      $(document).on('click', '.edit-option', function() {
        const optionId = $(this).data('id');
        $.ajax({
          url: '{{ route('admin.options.edit', ':id') }}'.replace(':id', optionId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const option = response.option;

              $('#modalTitle').text('Edit Option');
              $('#buttonText').text('Update');
              $('#buttonIcon').removeClass('fa-save').addClass('fa-edit');
              $('#createOptionBtn').removeClass('btn-primary').addClass('btn-success');
              $('#formMethod').val('PUT');
              $('#optionId').val(option.id);

              // Fill general tab fields
              $('#createName').val(option.name);
              $('#createType').val(option.type);
              $('#createPosition').val(option.position);
              $('#createRequired').prop('checked', option.is_required);
              $('#createGlobal').prop('checked', option.is_global);

              // Clear values container first
              $('#values-container').empty();

              // Load simple form data for text/date types
              $('#simplePrice').val(option.price || '');
              $('#simplePriceType').val(option.price_type || 'fixed');

              // Load existing values if any (for table types)
              if (option.values && option.values.length > 0) {
                option.values.forEach(function(value) {
                  addValueField(value);
                });
              }

              // Trigger type change to show/hide values section
              handleTypeChange();

              $('#createOptionModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading option data');
          }
        });
      });

      // Delete Option
      $(document).on('click', '.delete-option', function() {
        const optionId = $(this).data('id');

        Swal.fire({
          title: 'Are you sure?',
          text: 'You are about to delete this option. This action cannot be undone!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('admin.options.destroy', ':id') }}'.replace(':id', optionId),
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
                }
              },
              error: function(xhr) {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'An error occurred while deleting the option.',
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
