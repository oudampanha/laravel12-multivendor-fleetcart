@extends('admin.layouts.master_layout')

@section('pageTitle', 'Users Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Users Management</h4>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
              <i class="fas fa-plus"></i> Add New User
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
                <label for="statusFilter">Filter by Status</label>
                <select class="form-control" id="statusFilter">
                  <option value="">All Status</option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="roleFilter">Filter by Role</label>
                <select class="form-control" id="roleFilter">
                  <option value="">All Roles</option>
                  <option value="Super Admin">Super Admin</option>
                  <option value="Admin">Admin</option>
                  <option value="Vendor">Vendor</option>
                  <option value="Customer">Customer</option>
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
                  <button type="button" class="btn btn-success" id="exportUsersBtn">
                    <i class="fas fa-download"></i> Export Users
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="usersTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Avatar</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Role</th>
                  <th>Status</th>
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
      max-width: 1080px;
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

    #usersTable th {
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

    /* Select2 Bootstrap 4 Theme Customizations */
    .select2-container--bootstrap4 .select2-selection--single {
      height: calc(1.5em + 0.75rem + 2px);
      border-radius: 6px;
    }

    .select2-container--bootstrap4 .select2-selection--multiple {
      border-radius: 6px;
      min-height: calc(1.5em + 0.75rem + 2px);
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
      line-height: calc(1.5em + 0.75rem);
    }

    .select2-container--bootstrap4 .select2-selection__clear {
      color: #dc3545;
      font-weight: bold;
    }

    .select2-container--bootstrap4 .select2-results__option--highlighted {
      background-color: #007bff;
    }

    .select2-dropdown {
      border-radius: 6px;
      box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
    }

    .select2-search--dropdown .select2-search__field {
      border-radius: 6px;
    }
  </style>
@endpush

<!-- User Modals -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form id="createUserForm" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" id="userId" name="user_id">
      <input type="hidden" id="formMethod" name="_method" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Create New User</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createFirstName">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="createFirstName" name="first_name" required>
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createLastName">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="createLastName" name="last_name" required>
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createUsername">Username</label>
                    <input type="text" class="form-control" id="createUsername" name="username">
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createEmail">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="createEmail" name="email" required>
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createPhone">Phone Number</label>
                    <input type="text" class="form-control" id="createPhone" name="phone_no">
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createRoles">Roles</label>
                    <select class="form-control form-select select2-roles" id="createRoles" name="roles[]" multiple>
                      @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->title }}</option>
                      @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createPassword" id="passwordLabel">Password <span
                        class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="createPassword" name="password">
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="createPasswordConfirmation" id="passwordConfirmLabel">Confirm Password <span
                        class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="createPasswordConfirmation"
                      name="password_confirmation">
                    <div class="invalid-feedback"></div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="createStatus">Status</label>
                    <select class="form-control" id="createStatus" name="status">
                      <option value="1">Active</option>
                      <option value="0">Inactive</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <!-- Media Upload using reusable component -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-images mr-2"></i>
                    Profile Image
                  </h3>
                </div>
                <div class="card-body">
                  <x-media-selector name="avatar" label="" :required="false" preview_height="200px"
                    placeholder_text="Click to choose from gallery" upload_text="upload new image" :show_gallery="true"
                    :show_upload="true" :show_remove="true" />
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="createUserBtn">
            <i class="fas fa-save" id="buttonIcon"></i> <span id="buttonText">Create User</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">User Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="userDetailsContent">
        <!-- User details will be loaded here -->
      </div>
    </div>
  </div>
</div>


@push('scripts')
  <script>
    $(document).ready(function() {
      // Check if Select2 is loaded
      if (typeof $.fn.select2 === 'undefined') {
        return;
      }
      // Initialize server-side DataTable
      const table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.users.index') }}',
          type: 'GET'
        },
        columns: [{
            data: 'id',
            name: 'id'
          },
          {
            data: 'avatar',
            name: 'avatar',
            orderable: true,
            searchable: true
          },
          {
            data: 'name',
            name: 'first_name',
            orderable: true,
            searchable: true
          },
          {
            data: 'email',
            name: 'email'
          },
          {
            data: 'phone',
            name: 'phone_no'
          },
          {
            data: 'roles',
            name: 'roles',
            orderable: false,
            searchable: false
          },
          {
            data: 'status',
            name: 'status'
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

      // Initialize Select2 when modals are shown
      function initializeSelect2InModal(modalId) {
        const $select = $(`${modalId} .select2-roles`);
        console.log('Initializing Select2 for:', modalId, 'Elements found:', $select.length);
        $select.select2({
          theme: 'bootstrap4',
          placeholder: 'Select roles...',
          allowClear: true,
          width: '100%',
          dropdownParent: $(modalId + ' .modal-body'),
          tags: false,
          escapeMarkup: function(markup) {
            return markup;
          },
          templateResult: function(role) {
            if (role.loading) {
              return role.text;
            }
            return `<div style="padding: 5px;"><i class="fas fa-user-tag text-primary"></i> ${role.text}</div>`;
          },
          templateSelection: function(role) {
            return `<span><i class="fas fa-user-tag"></i> ${role.text}</span>`;
          }
        });
      }

      // Initialize Select2 when create modal is shown
      $('#createUserModal').on('shown.bs.modal', function() {
        initializeSelect2InModal('#createUserModal');
      });

      // Fallback: Try to initialize Select2 immediately for any visible elements
      setTimeout(function() {
        $('.select2-roles').each(function() {
          if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({
              theme: 'bootstrap4',
              placeholder: 'Select roles...',
              allowClear: true,
              width: '100%'
            });
          }
        });
      }, 500);

      // Function to reset modal to create mode
      function resetModalToCreateMode() {
        // Reset modal title and button
        $('#modalTitle').text('Create New User');
        $('#buttonText').text('Create User');
        $('#buttonIcon').removeClass('fa-edit').addClass('fa-save');
        $('#createUserBtn').removeClass('btn-success').addClass('btn-primary');

        // Reset form method and user ID
        $('#formMethod').val('POST');
        $('#userId').val('');

        // Reset password labels and requirements
        $('#passwordLabel').html('Password <span class="text-danger">*</span>');
        $('#passwordConfirmLabel').html('Confirm Password <span class="text-danger">*</span>');
        $('#createPassword').attr('required', true).removeAttr('placeholder');
        $('#createPasswordConfirmation').attr('required', true).removeAttr('placeholder');

        // Reset media selector silently (without alerts)
        const mediaSelector = document.querySelector('#createUserModal .media-selector-component');
        if (mediaSelector) {
          const componentId = mediaSelector.id;
          // Clear inputs without triggering MediaSelector.clearImage to avoid alerts
          const urlInput = document.getElementById(componentId + '_url_input');
          const oldInput = document.getElementById(componentId + '_old_input');
          const imagePreview = document.getElementById(componentId + '_image_preview');
          const uploadContent = document.getElementById(componentId + '_upload_content');

          if (urlInput) urlInput.value = '';
          if (oldInput) oldInput.value = '';
          if (imagePreview) imagePreview.style.display = 'none';
          if (uploadContent) uploadContent.style.display = 'block';
        }
      }

      // Handle modal close - destroy Select2 and reset forms
      $('.modal').on('hidden.bs.modal', function() {
        // Destroy Select2 to prevent memory leaks
        $(this).find('.select2-roles').each(function() {
          if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
          }
        });

        // Reset form
        const form = $(this).find('form')[0];
        if (form) {
          form.reset();
        }

        // Clear validation states
        $(this).find('.form-control').removeClass('is-invalid');
        $(this).find('.invalid-feedback').text('');

        // Reset modal to create mode
        if ($(this).attr('id') === 'createUserModal') {
          resetModalToCreateMode();
        }
      });

      // Reset modal when create button is clicked
      $('button[data-target="#createUserModal"]').on('click', function() {
        resetModalToCreateMode();
      });

      // Status filter
      $('#statusFilter').on('change', function() {
        const status = $(this).val();
        table.column(6).search(status).draw(); // Status is column index 6
      });

      // Role filter
      $('#roleFilter').on('change', function() {
        const role = $(this).val();
        table.column(5).search(role).draw(); // Roles is column index 5
      });

      // Clear filters
      $('#clearFiltersBtn').on('click', function() {
        $('#statusFilter').val('');
        $('#roleFilter').val('');
        table.search('').columns().search('').draw();
        showAlert('All filters cleared', 'info');
      });

      // Export users
      $('#exportUsersBtn').on('click', function() {
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

      // Create/Update User
      $('#createUserBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isEdit = $('#userId').val() !== '';
        const buttonLoadingText = isEdit ? '<i class="fas fa-spinner fa-spin"></i> Updating...' :
          '<i class="fas fa-spinner fa-spin"></i> Creating...';

        $btn.html(buttonLoadingText).prop('disabled', true);

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData($('#createUserForm')[0]);
        const url = isEdit ? '{{ route('admin.users.update', ':id') }}'.replace(':id', $('#userId').val()) :
          '{{ route('admin.users.store') }}';
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
              // $('#createUserModal').modal('hide');
              // table.ajax.reload();

              // // Use SweetAlert2 for success notification
              // Swal.fire({
              //   icon: 'success',
              //   title: response.title || 'Success',
              //   text: response.message,
              //   timer: 3000,
              //   timerProgressBar: true,
              //   showConfirmButton: false,
              //   position: 'top-end',
              //   toast: true
              // });
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
                text: `An error occurred while ${isEdit ? 'updating' : 'creating'} the user.`,
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

      // View User
      $(document).on('click', '.view-user', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.users.show', ':id') }}'.replace(':id', userId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const user = response.user;
              let roles = user.roles.map(role => `<span class="badge badge-info">${role.title}</span>`)
                .join(' ') || '<span class="text-muted">No Role</span>';

              $('#userDetailsContent').html(`
                <div class="row">
                  <div class="col-md-6">
                    <strong>Name:</strong> ${user.first_name} ${user.last_name}<br>
                    <strong>Username:</strong> ${user.username || 'N/A'}<br>
                    <strong>Email:</strong> ${user.email}<br>
                    <strong>Phone:</strong> ${user.phone_no || 'N/A'}<br>
                  </div>
                  <div class="col-md-6">
                    <strong>Status:</strong> ${user.is_verified ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}<br>
                    <strong>Roles:</strong> ${roles}<br>
                    <strong>Created:</strong> ${new Date(user.created_at).toLocaleString()}<br>
                    <strong>Last Login:</strong> ${user.last_login ? new Date(user.last_login).toLocaleString() : 'Never'}
                  </div>
                </div>
              `);
              $('#viewUserModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading user details');
          }
        });
      });

      // Edit User
      $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        $.ajax({
          url: '{{ route('admin.users.edit', ':id') }}'.replace(':id', userId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const user = response.user;
              // Set modal mode to edit
              $('#modalTitle').text('Edit User');
              $('#buttonText').text('Update User');
              $('#buttonIcon').removeClass('fa-save').addClass('fa-edit');
              $('#createUserBtn').removeClass('btn-primary').addClass('btn-success');

              // Set form method for update
              $('#formMethod').val('PUT');
              $('#userId').val(user.id);

              // Fill form fields
              $('#createFirstName').val(user.first_name);
              $('#createLastName').val(user.last_name);
              $('#createUsername').val(user.username);
              $('#createEmail').val(user.email);
              $('#createPhone').val(user.phone_no);
              $('#createVerified').val(user.is_verified ? '1' : '0');

              // Make passwords optional for editing
              $('#passwordLabel').html(
                'Password <small class="text-muted">(Leave empty to keep current)</small>');
              $('#passwordConfirmLabel').html(
                'Confirm Password <small class="text-muted">(Leave empty to keep current)</small>');
              $('#createPassword').removeAttr('required').attr('placeholder',
                'Leave empty to keep current password');
              $('#createPasswordConfirmation').removeAttr('required').attr('placeholder',
                'Leave empty to keep current password');

              // Set selected roles in Select2
              const userRoleIds = user.roles.map(role => role.id.toString());
              $('#createRoles').val(userRoleIds).trigger('change');

              // Handle profile image preview if user has one
              const mediaSelector = document.querySelector('#createUserModal .media-selector-component');
              if (mediaSelector && (user.avatar_url || user.profile_image_url)) {
                const componentId = mediaSelector.id;
                const urlInput = document.getElementById(componentId + '_url_input');
                const oldInput = document.getElementById(componentId + '_old_input');
                const imageUrl = user.avatar_url || user.profile_image_url;

                // Set the image URL in hidden inputs
                if (urlInput) urlInput.value = imageUrl;
                if (oldInput) oldInput.value = imageUrl;

                // Show the image preview
                MediaSelector.setImagePreview(componentId, imageUrl);
              }

              $('#createUserModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading user data');
          }
        });
      });


      // Delete User
      $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        const userName = $(this).closest('tr').find('td:eq(1)').text().trim();

        Swal.fire({
          title: 'Are you sure?',
          text: `You are about to delete user: ${userName}. This action cannot be undone!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('admin.users.destroy', ':id') }}'.replace(':id', userId),
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
                  text: 'An error occurred while deleting the user.',
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
