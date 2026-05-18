@extends('admin.layouts.master_layout')

@section('pageTitle', 'Brands Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Brands Management</h4>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createBrandModal">
              <i class="fas fa-plus"></i> Add New Brand
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
                <label for="logoFilter">Filter by Logo</label>
                <select class="form-control" id="logoFilter">
                  <option value="">All Brands</option>
                  <option value="with_logo">With Logo</option>
                  <option value="without_logo">Without Logo</option>
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
                  <button type="button" class="btn btn-success" id="exportBrandsBtn">
                    <i class="fas fa-download"></i> Export Brands
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="brandsTable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Logo</th>
                  <th>Name</th>
                  <th>Slug</th>
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
  <link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css"
    rel="stylesheet">
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

    #brandsTable th {
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

<!-- Brand Modals -->
<div class="modal fade" id="createBrandModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form id="createBrandForm" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" id="brandId" name="brand_id">
      <input type="hidden" id="formMethod" name="_method" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Create New Brand</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Tabs with Icons -->
          <div class="tabs-showcase">
            <ul class="nav nav-tabs nav-tabs-icon" id="iconTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                  <i class="fas fa-tachometer-alt"></i> General
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="images-tab" data-toggle="tab" href="#images" role="tab">
                  <i class="fas fa-users"></i> Image
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab">
                  <i class="fas fa-users"></i> SEO
                </a>
              </li>
            </ul>
            <div class="tab-content tab-content-bordered" id="iconTabContent">
              <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="mb-3">
                  <label for="brandName" class="form-label">Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="brandName" name="name" required>
                </div>

                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1"
                    checked>
                  <label class="form-check-label" for="isActive">
                    Enable the Brand
                  </label>
                </div>
              </div>
              <div class="tab-pane fade" id="images" role="tabpanel">
                <div class="mb-3">
                  <div class="image-upload-section">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="card">
                          <div class="card-header">
                            <h5 class="card-title">
                              <i class="fas fa-images mr-2"></i>
                              Logo
                            </h5>
                          </div>
                          <div class="card-body">
                            <x-media-selector name="logo" label="" :required="false" preview_height="200px"
                              placeholder_text="Click to choose from gallery" upload_text="upload new image"
                              :show_gallery="true" :show_upload="true" :show_remove="true" />
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="single-image-wrapper">
                          <div class="card">
                            <div class="card-header">
                              <h5 class="card-title">
                                <i class="fas fa-images mr-2"></i>
                                Banner
                              </h5>
                            </div>
                            <div class="card-body">
                              <x-media-selector name="banner" label="" :required="false"
                                preview_height="200px" placeholder_text="Click to choose from gallery"
                                upload_text="upload new image" :show_gallery="true" :show_upload="true" :show_remove="true" />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="seo" role="tabpanel">
                <div class="mb-3">
                  <label for="metaTitle" class="form-label">Meta Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="metaTitle" name="meta_title" required>
                </div>
                <div class="mb-3">
                  <label for="metaDescription" class="form-label">Meta Description</label>
                  <textarea class="form-control" id="metaDescription" name="meta_description" rows="4"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="createBrandBtn">
            <i class="fas fa-save" id="buttonIcon"></i> <span id="buttonText">Create Brand</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="viewBrandModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Brand Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="brandDetailsContent">
        <!-- Brand details will be loaded here -->
      </div>
    </div>
  </div>
</div>

@push('scripts')
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize server-side DataTable
      const table = $('#brandsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.brands.index') }}',
          type: 'GET'
        },
        columns: [{
            data: 'id',
            name: 'id'
          },
          {
            data: 'logo',
            name: 'logo',
            orderable: false,
            searchable: false
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
            data: 'status',
            name: 'is_active'
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
        $('#modalTitle').text('Create New Brand');
        $('#buttonText').text('Create Brand');
        $('#buttonIcon').removeClass('fa-edit').addClass('fa-save');
        $('#createBrandBtn').removeClass('btn-success').addClass('btn-primary');

        // Reset form method and brand ID
        $('#formMethod').val('POST');
        $('#brandId').val('');

        // Reset media selector silently (without alerts)
        const mediaSelector = document.querySelector('#createBrandModal .media-selector-component');
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
        if ($(this).attr('id') === 'createBrandModal') {
          resetModalToCreateMode();
        }
      });

      // Reset modal when create button is clicked
      $('button[data-target="#createBrandModal"]').on('click', function() {
        resetModalToCreateMode();
      });

      // Status filter
      $('#statusFilter').on('change', function() {
        const status = $(this).val();
        table.column(4).search(status).draw(); // Status is column index 4
      });

      // Clear filters
      $('#clearFiltersBtn').on('click', function() {
        $('#statusFilter').val('');
        $('#logoFilter').val('');
        table.search('').columns().search('').draw();
        showAlert('All filters cleared', 'info');
      });

      // Export brands
      $('#exportBrandsBtn').on('click', function() {
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

      // Create/Update Brand
      $('#createBrandBtn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isEdit = $('#brandId').val() !== '';
        const buttonLoadingText = isEdit ? '<i class="fas fa-spinner fa-spin"></i> Updating...' :
          '<i class="fas fa-spinner fa-spin"></i> Creating...';

        $btn.html(buttonLoadingText).prop('disabled', true);

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData($('#createBrandForm')[0]);
        const url = isEdit ? '{{ route('admin.brands.update', ':id') }}'.replace(':id', $('#brandId').val()) :
          '{{ route('admin.brands.store') }}';

        $.ajax({
          url: url,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            console.log(response);
            if (response.success) {
              $('#createBrandModal').modal('hide');
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
                const field = $(`[name="${key}"]`);
                field.addClass('is-invalid');
                field.next('.invalid-feedback').text(errors[key][0]);
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: `An error occurred while ${isEdit ? 'updating' : 'creating'} the brand.`,
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

      // View Brand
      $(document).on('click', '.view-brand', function() {
        const brandId = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.brands.show', ':id') }}'.replace(':id', brandId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const brand = response.brand;
              let logo = brand.logo ?
                `<img src="${brand.logo}" alt="${brand.name || 'Brand'}" class="img-thumbnail" style="max-width:200px;">` :
                '<div class="text-center text-muted"><i class="fas fa-image fa-3x"></i><br>No Logo</div>';

              let banner = brand.banner ?
                `<img src="${brand.banner}" alt="${brand.name || 'Brand'} Banner" class="img-thumbnail" style="max-width:100%; max-height:200px;">` :
                '<div class="text-center text-muted"><i class="fas fa-image fa-3x"></i><br>No Banner</div>';

              $('#brandDetailsContent').html(`
                <div class="row">
                  <div class="col-md-6">
                    <strong>Name:</strong> ${brand.name || 'N/A'}<br>
                    <strong>Slug:</strong> ${brand.slug}<br>
                    <strong>Status:</strong> ${brand.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}<br>
                    <strong>Created:</strong> ${new Date(brand.created_at).toLocaleString()}<br>
                    <strong>Updated:</strong> ${new Date(brand.updated_at).toLocaleString()}
                  </div>
                  <div class="col-md-6">
                    <strong>Logo:</strong><br>
                    ${logo}
                  </div>
                </div>
                ${brand.banner ? `<div class="mt-3"><strong>Banner:</strong><br>${banner}</div>` : ''}
                ${brand.description ? `<div class="mt-3"><strong>Description:</strong><br>${brand.description}</div>` : ''}
                ${brand.meta_title || brand.meta_description ? `
                                <div class="mt-3">
                                  <h6><strong>SEO Information:</strong></h6>
                                  ${brand.meta_title ? `<strong>Meta Title:</strong> ${brand.meta_title}<br>` : ''}
                                  ${brand.meta_description ? `<strong>Meta Description:</strong> ${brand.meta_description}<br>` : ''}
                                </div>
                              ` : ''}
              `);
              $('#viewBrandModal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading brand details');
          }
        });
      });

      // Edit Brand
      $(document).on('click', '.edit-brand', function() {
        const brandId = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.brands.edit', ':id') }}'.replace(':id', brandId),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const brand = response.brand;

              // Set modal mode to edit
              $('#modalTitle').text('Edit Brand');
              $('#buttonText').text('Update Brand');
              $('#buttonIcon').removeClass('fa-save').addClass('fa-edit');
              $('#createBrandBtn').removeClass('btn-primary').addClass('btn-success');

              // Set form method for update
              $('#formMethod').val('PUT');
              $('#brandId').val(brand.id);

              // Fill form fields
              $('#brandName').val(brand.name || '');
              $('#isActive').prop('checked', brand.is_active == 1 || brand.is_active === true);

              // Fill metadata fields
              $('#metaTitle').val(brand.meta_title || '');
              $('#metaDescription').val(brand.meta_description || '');

              // Handle logo preview if brand has one
              const logoSelector = document.querySelector('#createBrandModal [name="logo"]')?.closest(
                '.media-selector-component');
              if (logoSelector && brand.logo) {
                const componentId = logoSelector.id;
                const urlInput = document.getElementById(componentId + '_url_input');
                const oldInput = document.getElementById(componentId + '_old_input');
                const idInput = document.getElementById(componentId + '_id_input');

                // Set the logo URL and ID in hidden inputs
                if (urlInput) urlInput.value = brand.logo;
                if (oldInput) oldInput.value = brand.logo;
                if (idInput && brand.logo_id) idInput.value = brand.logo_id;

                // Show the logo preview
                if (typeof MediaSelector !== 'undefined') {
                  MediaSelector.setImagePreview(componentId, brand.logo);
                }
              }

              // Handle banner preview if brand has one
              const bannerSelector = document.querySelector('#createBrandModal [name="banner"]')?.closest(
                '.media-selector-component');
              if (bannerSelector && brand.banner) {
                const componentId = bannerSelector.id;
                const urlInput = document.getElementById(componentId + '_url_input');
                const oldInput = document.getElementById(componentId + '_old_input');
                const idInput = document.getElementById(componentId + '_id_input');

                // Set the banner URL and ID in hidden inputs
                if (urlInput) urlInput.value = brand.banner;
                if (oldInput) oldInput.value = brand.banner;
                if (idInput && brand.banner_id) idInput.value = brand.banner_id;

                // Show the banner preview
                if (typeof MediaSelector !== 'undefined') {
                  MediaSelector.setImagePreview(componentId, brand.banner);
                }
              }

              $('#createBrandModal').modal('show');
            }
          },
          error: function(xhr) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error loading brand data',
              position: 'top-end',
              toast: true
            });
          }
        });
      });

      // Delete Brand
      $(document).on('click', '.delete-brand', function() {
        const brandId = $(this).data('id');
        const brandName = $(this).closest('tr').find('td:eq(2)').text().trim();

        Swal.fire({
          title: 'Are you sure?',
          text: `You are about to delete brand: ${brandName}. This action cannot be undone!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('admin.brands.destroy', ':id') }}'.replace(':id', brandId),
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
                  text: 'An error occurred while deleting the brand.',
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
