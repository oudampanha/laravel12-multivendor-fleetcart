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
        <!-- Attribute Set Form -->
        <div class="col-md-5">
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
                  </div>
                </div>

                <div class="text-right mt-4">
                  <button type="button" class="btn btn-secondary" id="cancelBtn">
                    <i class="fas fa-times mr-2"></i>Cancel
                  </button>
                  <button type="submit" class="btn btn-primary ml-2" id="saveBtn">
                    <i class="fas fa-save mr-2"></i>Save
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <!-- Attribute Sets List -->
        <div class="col-md-7">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addAttributeSetBtn">
                    <i class="fas fa-plus me-2"></i>Add Attribute Set
                  </button>
                </div>
              </div>

              <div id="attributeSetsList" class="attribute-sets-list">
                <!-- Attribute sets table will be loaded here -->
                <div class="table-responsive">
                  <table class="table table-hover table-bordered">
                    <thead>
                      <tr>
                        <th width="50">#</th>
                        <th>Name</th>
                        <th width="120">Attributes</th>
                        <th width="150">Created Date</th>
                        <th width="120" class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="attributeSetsTableBody">
                      <!-- Rows will be loaded here -->
                    </tbody>
                  </table>
                </div>
              </div>
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

    .table-responsive {
      max-height: 550px;
      overflow-y: auto;
    }

    .attribute-sets-list table {
      margin-bottom: 0;
    }

    .attribute-sets-list thead th {
      position: sticky;
      top: 0;
      background-color: #f8f9fa;
      z-index: 10;
      border-bottom: 2px solid #dee2e6;
      font-weight: 600;
      font-size: 14px;
    }

    .attribute-sets-list tbody tr {
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .attribute-sets-list tbody tr:hover {
      background-color: #f8f9fa;
    }

    .attribute-sets-list tbody tr.selected {
      background-color: #e7f3ff;
      border-left: 3px solid #007bff;
    }

    .badge-attributes {
      background-color: #e3f2fd;
      color: #1976d2;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      font-weight: 500;
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

    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(360deg);
      }
    }
  </style>
@endpush

@push('scripts')
  <script>
    $(document).ready(function() {
      let selectedAttributeSet = null;
      let isEditMode = false;

      // Load attribute sets on page load
      loadAttributeSets();

      // Add Attribute Set
      $('#addAttributeSetBtn').click(function() {
        clearForm();
        isEditMode = false;
        $('#saveBtn').text('Save');
        selectedAttributeSet = null;
        $('.attribute-set-row').removeClass('selected');
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
        $('.attribute-set-row').removeClass('selected');
      });

      // Functions
      function loadAttributeSets() {
        $('#attributeSetsTableBody').html(
          '<tr><td colspan="5" class="text-center"><div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div></td></tr>'
        );

        $.ajax({
          url: '{{ route('admin.attribute-sets.index') }}',
          method: 'GET',
          data: {
            ajax: true
          },
          success: function(response) {
            displayAttributeSets(response.data);
          },
          error: function(xhr) {
            console.error('Error loading attribute sets:', xhr);
            $('#attributeSetsTableBody').html(
              '<tr><td colspan="5" class="text-center text-danger">Error loading attribute sets</td></tr>');
          }
        });
      }

      function displayAttributeSets(attributeSets) {
        const tbody = $('#attributeSetsTableBody');

        if (attributeSets.length === 0) {
          tbody.html(`
            <tr>
              <td colspan="5" class="text-center py-5">
                <div class="empty-state">
                  <i class="fas fa-tags"></i>
                  <div class="h6">No attribute sets found</div>
                  <div class="text-muted">Click "Add Attribute Set" to create your first one.</div>
                </div>
              </td>
            </tr>
          `);
          return;
        }

        let html = '';
        attributeSets.forEach(function(attributeSet, index) {
          const createdDate = new Date(attributeSet.created_at).toLocaleDateString();
          html += `
            <tr class="attribute-set-row" data-id="${attributeSet.id}">
              <td class="text-center">${index + 1}</td>
              <td>
                <strong>${attributeSet.name}</strong>
              </td>
              <td class="text-center">
                <span class="badge-attributes">
                  <i class="fas fa-tags"></i> ${attributeSet.attributes_count}
                </span>
              </td>
              <td class="text-muted small">${createdDate}</td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary edit-attribute-set" data-id="${attributeSet.id}" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger delete-attribute-set" data-id="${attributeSet.id}" title="Delete">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          `;
        });

        tbody.html(html);

        // Bind click events
        $('.attribute-set-row').click(function(e) {
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
        $('.attribute-set-row').removeClass('selected');
        $(`.attribute-set-row[data-id="${attributeSetId}"]`).addClass('selected');
        selectedAttributeSet = attributeSetId;

        // Load attribute set data for viewing
        loadAttributeSetData(attributeSetId, false);
      }

      function editAttributeSet(attributeSetId) {
        isEditMode = true;
        loadAttributeSetData(attributeSetId, true);
        $('#saveBtn').text('Update');

        // Select the row
        $('.attribute-set-row').removeClass('selected');
        $(`.attribute-set-row[data-id="${attributeSetId}"]`).addClass('selected');
        selectedAttributeSet = attributeSetId;
      }

      function loadAttributeSetData(attributeSetId, isEdit) {
        $.ajax({
          url: '{{ route('admin.attribute-sets.edit', ':id') }}'.replace(':id', attributeSetId),
          method: 'GET',
          data: {
            ajax: true
          },
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
        $('.attribute-set-row').removeClass('selected');
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
