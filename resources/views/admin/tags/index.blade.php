@extends('admin.layouts.master_layout')

@section('pageTitle', 'Tags Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Tags Management</h4>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createTagModal">
              <i class="fas fa-plus"></i> Add New Tag
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
                <label for="productsFilter">Filter by Products</label>
                <select class="form-control" id="productsFilter">
                  <option value="">All Tags</option>
                  <option value="With Products">With Products</option>
                  <option value="No Products">No Products</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="slugFilter">Filter by Slug</label>
                <input type="text" class="form-control" id="slugFilter" placeholder="Search by slug...">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>&nbsp;</label>
                <div class="d-flex">
                  <button type="button" class="btn btn-secondary mr-2" id="clearFiltersBtn">
                    <i class="fas fa-times"></i> Clear Filters
                  </button>
                  <button type="button" class="btn btn-success" id="exportTagsBtn">
                    <i class="fas fa-download"></i> Export Tags
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="tagsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Slug</th>
                  <th>Products Count</th>
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
    .modal-lg {
      max-width: 800px;
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

    #tagsTable th {
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
  </style>
@endpush

<!-- Tag Modals -->
<div class="modal fade" id="createTagModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form id="createTagForm" method="POST">
      @csrf
      <input type="hidden" id="tagId" name="tag_id">
      <input type="hidden" id="formMethod" name="_method" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Create New Tag</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="createTagName">Tag Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="createTagName" name="name[en]" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="createTagSlug">Slug <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="createTagSlug" name="slug" required>
                <div class="invalid-feedback"></div>
                <small class="form-text text-muted">URL-friendly version (e.g., my-tag-name)</small>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="createTagBtn">
            <i class="fas fa-save" id="buttonIcon"></i> <span id="buttonText">Create Tag</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="viewTagModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tag Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="tagDetailsContent">
        <!-- Tag details will be loaded here -->
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script>
    $(document).ready(function() {
      // Initialize server-side DataTable
      const table = $('#tagsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.tags.index') }}',
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
            data: 'slug',
            name: 'slug'
          },
          {
            data: 'products_count',
            name: 'products_count'
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
        // Reset modal title and button
        $('#modalTitle').text('Create New Tag');
        $('#buttonText').text('Create Tag');
        $('#buttonIcon').removeClass('fa-edit').addClass('fa-save');
        $('#createTagBtn').removeClass('btn-success').addClass('btn-primary');

        // Reset form method and tag ID
        $('#formMethod').val('POST');
        $('#tagId').val('');
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

        // Reset modal to create mode
        if ($(this).attr('id') === 'createTagModal') {
          resetModalToCreateMode();
        }
      });

      // Reset modal when create button is clicked
      $('button[data-target="#createTagModal"]').on('click', function() {
        resetModalToCreateMode();
      });

      // Products filter
      $('#productsFilter').on('change', function() {
        const filter = $(this).val();
        table.column(3).search(filter).draw(); // Products count is column index 3
      });

      // Slug filter
      $('#slugFilter').on('keyup', function() {
        const slug = $(this).val();
        table.column(2).search(slug).draw(); // Slug is column index 2
      });

      // Clear filters
      $('#clearFiltersBtn').on('click', function() {
        $('#productsFilter').val('');
        $('#slugFilter').val('');
        table.search('').columns().search('').draw();
        showAlert('All filters cleared', 'info');
      });

      // Export tags
      $('#exportTagsBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Exporting...').prop('disabled', true);

        // Simulate export process
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

      // Auto-generate slug from name
      $('#createTagName').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
          .replace(/[^a-z0-9 -]/g, '') // Remove invalid chars
          .replace(/\s+/g, '-') // Replace spaces with -
          .replace(/-+/g, '-') // Collapse dashes
          .replace(/^-+|-+$/g, ''); // Trim dashes from ends
        $('#createTagSlug').val(slug);
      });

      // Create/Update Tag
      $('#createTagBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isEdit = $('#tagId').val() !== '';
        const buttonLoadingText = isEdit ? '<i class="fas fa-spinner fa-spin"></i> Updating...' :
          '<i class="fas fa-spinner fa-spin"></i> Creating...';

        $btn.html(buttonLoadingText).prop('disabled', true);

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData($('#createTagForm')[0]);
        const url = isEdit ? '{{ route('admin.tags.update', ':id') }}'.replace(':id', $('#tagId').val()) :
          '{{ route('admin.tags.store') }}';
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
              $('#createTagModal').modal('hide');
              table.ajax.reload();

              // Use SweetAlert2 for success notification
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
                const field = $(`[name="${key}"], [name="name[en]"]`);
                field.addClass('is-invalid');
                field.next('.invalid-feedback').text(errors[key][0]);
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `An error occurred while ${isEdit ? 'updating' : 'creating'} the tag.`,
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

      // View Tag
      $(document).on('click', '.view-tag', function() {
        const tagId = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.tags.show', ':id') }}'.replace(':id', tagId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const tag = response.tag;

              $('#tagDetailsContent').html(`
                <div class="row">
                  <div class="col-md-6">
                    <strong>Name:</strong> ${tag.name || 'Untitled Tag'}<br>
                    <strong>Slug:</strong> <code>${tag.slug}</code><br>
                    <strong>Products Count:</strong> ${tag.products_count || 0}<br>
                  </div>
                  <div class="col-md-6">
                    <strong>Created:</strong> ${new Date(tag.created_at).toLocaleString()}<br>
                    <strong>Updated:</strong> ${tag.updated_at ? new Date(tag.updated_at).toLocaleString() : 'Never'}
                  </div>
                </div>
              `);
              $('#viewTagModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading tag details');
          }
        });
      });

      // Edit Tag
      $(document).on('click', '.edit-tag', function() {
        const tagId = $(this).data('id');
        $.ajax({
          url: '{{ route('admin.tags.edit', ':id') }}'.replace(':id', tagId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const tag = response.tag;
              // Set modal mode to edit
              $('#modalTitle').text('Edit Tag');
              $('#buttonText').text('Update Tag');
              $('#buttonIcon').removeClass('fa-save').addClass('fa-edit');
              $('#createTagBtn').removeClass('btn-primary').addClass('btn-success');

              // Set form method for update
              $('#formMethod').val('PUT');
              $('#tagId').val(tag.id);

              // Fill form fields
              $('#createTagName').val(tag.name || '');
              $('#createTagSlug').val(tag.slug);

              $('#createTagModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading tag data');
          }
        });
      });

      // Delete Tag
      $(document).on('click', '.delete-tag', function() {
        const tagId = $(this).data('id');
        const tagName = $(this).closest('tr').find('td:eq(1)').text().trim();

        Swal.fire({
          title: 'Are you sure?',
          text: `You are about to delete tag: ${tagName}. This action cannot be undone!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('admin.tags.destroy', ':id') }}'.replace(':id', tagId),
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
                  text: 'An error occurred while deleting the tag.',
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
