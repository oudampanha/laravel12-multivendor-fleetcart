@extends('admin.layouts.master_layout')

@section('pageTitle', 'Categories')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Categories</h1>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">🏠</a></li>
            <li class="breadcrumb-item active" aria-current="page">Categories</li>
          </ol>
        </nav>
      </div>

      <!-- Success Alert -->
      <div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
        <i class="fas fa-check-circle me-2"></i>
        <span id="successMessage">Category created</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>

      <div class="row">
        <!-- Categories Tree -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <button type="button" class="btn btn-sm btn-outline-primary me-2" id="addRootBtn">
                    Add Root Category
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="addSubBtn" disabled>
                    Add Subcategory
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-link p-0" id="collapseAllBtn">Collapse All</button>
                  <span class="text-muted">|</span>
                  <button type="button" class="btn btn-sm btn-link p-0" id="expandAllBtn">Expand All</button>
                </div>
              </div>

              <div id="categoryTree"></div>
            </div>
          </div>
        </div>

        <!-- Category Form -->
        <div class="col-md-8">
          <div class="card">
            <div class="card-body">
              <form id="categoryForm">
                @csrf
                <input type="hidden" id="categoryId" name="category_id">
                <input type="hidden" id="parentId" name="parent_id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <!-- Tabs with Icons -->
                <div class="tabs-showcase">
                  <ul class="nav nav-tabs nav-tabs-icon" id="iconTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="dashboard-tab" data-toggle="tab" href="#dashboard" role="tab">
                        <i class="fas fa-tachometer-alt"></i> General
                      </a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="users-tab" data-toggle="tab" href="#images" role="tab">
                        <i class="fas fa-users"></i> Image
                      </a>
                    </li>
                  </ul>
                  <div class="tab-content tab-content-bordered" id="iconTabContent">
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                      <div class="mb-3">
                        <label for="categoryName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                      </div>

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="isSearchable" name="is_searchable"
                          value="1">
                        <label class="form-check-label" for="isSearchable">
                          Show this category in search box category list
                        </label>
                      </div>

                      <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1"
                          checked>
                        <label class="form-check-label" for="isActive">
                          Enable the category
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
                                  <x-media-selector name="logo" label="" :required="false"
                                    preview_height="200px" placeholder_text="Click to choose from gallery"
                                    upload_text="upload new image" :show_gallery="true" :show_upload="true"
                                    :show_remove="true" />
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
                                      upload_text="upload new image" :show_gallery="true" :show_upload="true"
                                      :show_remove="true" />
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

                <div class="d-flex justify-content-between mt-2">
                  <div>
                    <button type="button" class="btn btn-danger" id="deleteBtn" style="display: none;">
                      <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                  </div>
                  <div>
                    <button type="button" class="btn btn-secondary me-2" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary mx-2" id="saveBtn">Save</button>
                  </div>
                </div>
            </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/themes/default/style.min.css">
  <style>
    .jstree-default .jstree-clicked {
      background: #007bff !important;
      border-radius: 3px;
    }

    .jstree-default .jstree-hovered {
      background: #f8f9fa;
      border-radius: 3px;
    }

    .category-tree-actions {
      opacity: 0;
      transition: opacity 0.2s;
    }

    .jstree-node:hover .category-tree-actions {
      opacity: 1;
    }

    #categoryTree {
      min-height: 400px;
      max-height: 600px;
      overflow-y: auto;
    }

    .image-holder {
      position: relative;
      min-height: 150px;
      border: 2px dashed #ddd;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #f8f9fa;
      margin-bottom: 10px;
      overflow: hidden;
    }

    .image-holder>i {
      font-size: 60px;
      color: #d9d9d9;
    }

    .image-holder img {
      max-width: 100%;
      max-height: 100%;
      border-radius: 6px;
    }

    .single-image-wrapper {
      margin-bottom: 20px;
    }

    .single-image-wrapper h4 {
      margin-bottom: 10px;
      color: #333;
      font-size: 16px;
      font-weight: 600;
    }

    .remove-image {
      margin-top: 10px;
    }

    .card-header {
      padding: .25rem 1rem;
      background-color: rgba(0, 0, 0, .03);
      border-bottom: 1px solid rgba(0, 0, 0, .125);
    }

    .card-title {
      margin-bottom: .25rem;
    }
  </style>
@endpush

@push('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.12/jstree.min.js"></script>
  <script>
    $(document).ready(function() {
      let selectedNode = null;
      let isEditMode = false;
      let logoMediaManager = null;
      let bannerMediaManager = null;

      // Initialize jsTree
      $('#categoryTree').jstree({
        'core': {
          'data': {
            'url': '{{ route('admin.categories.index') }}',
            'data': function(node) {
              return {
                'ajax': true
              };
            }
          },
          'check_callback': true
        },
        'plugins': ['wholerow', 'contextmenu'],
        'contextmenu': {
          'items': function(node) {
            return {
              'create': {
                'label': 'Add Subcategory',
                'action': function() {
                  addSubcategory(node.data);
                }
              },
              'edit': {
                'label': 'Edit',
                'action': function() {
                  editCategory(node.data);
                }
              },
              'delete': {
                'label': 'Delete',
                'action': function() {
                  deleteCategory(node.data);
                }
              }
            };
          }
        }
      });

      // Tree selection event
      $('#categoryTree').on('select_node.jstree', function(e, data) {
        selectedNode = data.node;
        $('#addSubBtn').prop('disabled', false);

        if (data.node.data) {
          // Load category in edit mode when clicking on tree
          editCategory(data.node.data);
        }
      });

      // Tree deselection event
      $('#categoryTree').on('deselect_node.jstree', function(e, data) {
        selectedNode = null;
        $('#addSubBtn').prop('disabled', true);
        clearForm();
      });

      // Add Root Category
      $('#addRootBtn').click(function() {
        clearForm();
        isEditMode = false;
        $('#parentId').val('');
        $('#saveBtn').text('Save');
        $('#deleteBtn').hide();
        $('#categoryTree').jstree('deselect_all');
      });

      // Add Subcategory
      $('#addSubBtn').click(function() {
        if (selectedNode && selectedNode.data) {
          addSubcategory(selectedNode.data);
        }
      });

      // Collapse/Expand All
      $('#collapseAllBtn').click(function() {
        $('#categoryTree').jstree('close_all');
      });

      $('#expandAllBtn').click(function() {
        $('#categoryTree').jstree('open_all');
      });

      // Form submission
      $('#categoryForm').submit(function(e) {
        e.preventDefault();
        saveCategory();
      });

      // Cancel button
      $('#cancelBtn').click(function() {
        clearForm();
        $('#categoryTree').jstree('deselect_all');
      });

      // Delete button
      $('#deleteBtn').click(function() {
        const categoryId = $('#categoryId').val();
        if (categoryId && selectedNode && selectedNode.data) {
          deleteCategory(selectedNode.data);
        }
      });

      // Media selector components are handled by x-media-selector blade component

      // Functions
      function addSubcategory(parentData) {
        clearForm();
        isEditMode = false;
        $('#parentId').val(parentData.id);
        $('#saveBtn').text('Save');
        $('#deleteBtn').hide();
      }

      function editCategory(categoryData) {
        isEditMode = true;
        $('#saveBtn').text('Update');
        $('#deleteBtn').show();
        // Load full category data from server
        loadCategoryData(categoryData.id, true);
      }

      function loadCategoryForm(categoryData) {
        $('#categoryId').val(categoryData.id);
        $('#categoryName').val(categoryData.name);
        $('#parentId').val(categoryData.parent_id || '');
        $('#isSearchable').prop('checked', categoryData.is_searchable == 1 || categoryData.is_searchable === true);
        $('#isActive').prop('checked', categoryData.is_active == 1 || categoryData.is_active === true);

        if (isEditMode) {
          $('#formMethod').val('PUT');
        }
      }

      function loadCategoryData(categoryId, isEdit) {
        $.ajax({
          url: '{{ route('admin.categories.index') }}/' + categoryId + '/edit',
          method: 'GET',
          data: {
            ajax: true
          },
          success: function(response) {
            if (response.success) {
              const category = response.category;
              loadCategoryForm(category);
            }
          },
          error: function(xhr) {
            console.error('Error loading category:', xhr);
            showErrorAlert('Error loading category data');
          }
        });
      }

      function clearForm() {
        $('#categoryForm')[0].reset();
        $('#categoryId').val('');
        $('#parentId').val('');
        $('#formMethod').val('POST');
        $('#isActive').prop('checked', true);
        isEditMode = false;
        $('#saveBtn').text('Save');
        $('#deleteBtn').hide();
      }

      function saveCategory() {
        const formData = new FormData($('#categoryForm')[0]);
        const categoryId = $('#categoryId').val();
        const method = isEditMode ? 'PUT' : 'POST';
        let url = '{{ route('admin.categories.store') }}';

        if (isEditMode && categoryId) {
          url = '{{ route('admin.categories.index') }}/' + categoryId;
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
              showSuccessAlert(response.message || 'Category saved successfully!');
              $('#categoryTree').jstree('refresh');
              clearForm();
              $('#categoryTree').jstree('deselect_all');
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

      function deleteCategory(categoryData) {
        if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
          $.ajax({
            url: '{{ route('admin.categories.index') }}/' + categoryData.id,
            method: 'DELETE',
            data: {
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success) {
                showSuccessAlert(response.message || 'Category deleted successfully!');
                $('#categoryTree').jstree('refresh');
                clearForm();
              } else {
                showErrorAlert(response.message || 'Failed to delete category');
              }
            },
            error: function(xhr) {
              let message = 'Failed to delete category';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
              }
              showErrorAlert(message);
            }
          });
        }
      }

      function showErrorAlert(message) {
        alert('Error: ' + message);
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
    });
  </script>
@endpush
