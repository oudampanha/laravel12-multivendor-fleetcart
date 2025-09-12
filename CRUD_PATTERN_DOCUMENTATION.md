# CRUD Pattern Documentation

This document outlines the standardized CRUD pattern used across all backend controllers and views in the Laravel 12 Multi-Vendor FleetCart application.

## Controller Pattern (`app/Http/Controllers/Backend/{Resource}Controller.php`)

### 1. Basic Controller Structure

```php
<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use App\Models\{Resource};
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class {Resource}Controller extends BaseController
{
    use ImageUploadTrait;

    protected string $resource = '{resource_lowercase}';

    protected array $additionalPermissions = ['{resource_lowercase}_management_access'];

    public function __construct()
    {
        parent::__construct();
        
        // Apply specific permissions for custom methods if needed
        // $this->applyMethodPermission('permission_name', ['methodName']);
    }
    
    // ... methods
}
```

### 2. Index Method with DataTables Support

```php
/**
 * Display a listing of the resource.
 */
public function index(Request $request)
{
    if ($request->ajax()) {
        return $this->getDataTableData($request);
    }
    return view('admin.{resources}.index');
}

/**
 * Get data for DataTables Ajax
 */
private function getDataTableData(Request $request)
{
    $query = {Resource}::query();

    // Handle global search
    if ($request->has('search') && $request->search['value']) {
        $search = $request->search['value'];
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
              // Add other searchable fields
        });
    }

    // Handle column-specific filters
    if ($request->has('columns')) {
        foreach ($request->columns as $index => $column) {
            if (!empty($column['search']['value'])) {
                $searchValue = $column['search']['value'];

                switch ($index) {
                    case N: // Status column (adjust index based on your table)
                        if ($searchValue === 'Active') {
                            $query->where('is_active', 1);
                        } elseif ($searchValue === 'Inactive') {
                            $query->where('is_active', 0);
                        }
                        break;
                        // Add other column filters as needed
                }
            }
        }
    }

    // Handle column ordering
    if ($request->has('order')) {
        $columns = ['id', 'image', 'name', 'slug', 'is_active', 'created_at']; // Adjust columns
        $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
        $orderDirection = $request->order[0]['dir'] ?? 'desc';

        // Handle special columns that can't be ordered directly
        if (!in_array($orderColumn, ['image'])) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->orderBy('created_at', 'desc'); // Default fallback
        }
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $totalRecords = {Resource}::count();
    $filteredRecords = $query->count();

    // Handle pagination
    $start = $request->start ?? 0;
    $length = $request->length ?? 10;
    ${resources} = $query->skip($start)->take($length)->get();

    $data = [];
    foreach (${resources} as ${resource}) {
        $status = ${resource}->is_active
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';

        $image = ${resource}->image
            ? '<img src="' . asset('storage/' . ${resource}->image) . '" alt="' . ${resource}->name . '" class="img-thumbnail" style="width:50px; height:50px; object-fit:cover;">'
            : '<div class="text-center" style="width:50px; height:50px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; border-radius:5px;"><i class="fas fa-image text-muted"></i></div>';

        $actions = '
            <div class="btn-group">
                <button class="btn btn-sm btn-info view-{resource}" data-id="' . ${resource}->id . '">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-warning edit-{resource}" data-id="' . ${resource}->id . '">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger delete-{resource}" data-id="' . ${resource}->id . '">
                    <i class="fas fa-trash"></i>
                </button>
            </div>';

        $data[] = [
            'id' => ${resource}->id,
            'image' => $image,
            'name' => '<strong>' . ${resource}->name . '</strong><br><small class="text-muted">' . ${resource}->slug . '</small>',
            'slug' => ${resource}->slug,
            'status' => $status,
            'created_at' => ${resource}->created_at ? ${resource}->created_at->format('Y-m-d H:i') : '-',
            'actions' => $actions,
        ];
    }

    return response()->json([
        'draw' => intval($request->draw),
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $data,
    ]);
}
```

### 3. Store Method

```php
/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|unique:{resources},slug',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'image_url' => 'nullable|url',
        'is_active' => 'boolean'
        // Add other validation rules
    ]);

    $data = $request->except(['image', 'image_url', 'old_image']);
    
    // Handle image upload - Check both file upload and URL from media selector
    if ($request->hasFile('image')) {
        // Direct file upload
        $data['image'] = $this->uploadImage($request, 'image', 'uploads/{resources}', '{resource}_');
        Log::info('{Resource} image uploaded from file: ' . $data['image']);
    } elseif ($request->filled('image_url')) {
        // Media selector URL - convert full URL to relative path
        $imageUrl = $request->image_url;
        if (str_contains($imageUrl, '/storage/')) {
            // Extract relative path from full URL
            $data['image'] = str_replace(url('/storage/'), '', $imageUrl);
        } else {
            // Keep external URLs as-is
            $data['image'] = $imageUrl;
        }
        Log::info('{Resource} image set from URL: ' . $data['image']);
    }

    // Create the resource
    ${resource} = {Resource}::create($data);

    // Save translations if the model supports them
    if (method_exists(${resource}, 'setTranslation')) {
        if ($request->has('name')) {
            ${resource}->setTranslation('name', $request->name, app()->getLocale());
        }
        if ($request->has('description')) {
            ${resource}->setTranslation('description', $request->description, app()->getLocale());
        }
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => '🎉 {Resource} created successfully!',
            'title' => 'Success',
            'type' => 'success',
            '{resource}' => ${resource}
        ]);
    }

    sweetalert()->success('{Resource} created successfully!');
    return redirect()->route('admin.{resources}.index');
}
```

### 4. Show Method

```php
/**
 * Display the specified resource.
 */
public function show({Resource} ${resource}, Request $request)
{
    ${resource}->load(['relatedModels']); // Load relationships as needed

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            '{resource}' => ${resource}
        ]);
    }

    return view('admin.{resources}.show', compact('{resource}'));
}
```

### 5. Edit Method

```php
/**
 * Show the form for editing the specified resource.
 */
public function edit({Resource} ${resource}, Request $request)
{
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            '{resource}' => ${resource}
        ]);
    }

    return view('admin.{resources}.edit', compact('{resource}'));
}
```

### 6. Update Method

```php
/**
 * Update the specified resource in storage.
 */
public function update(Request $request, {Resource} ${resource})
{
    $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|unique:{resources},slug,' . ${resource}->id,
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'image_url' => 'nullable|url',
        'is_active' => 'boolean'
        // Add other validation rules
    ]);

    $data = $request->except(['image', 'image_url', 'old_image']);
    
    // Handle image upload/update - Check both file upload and URL from media selector
    if ($request->hasFile('image')) {
        // Direct file upload - delete old and upload new
        $data['image'] = $this->updateImage($request, 'image', 'uploads/{resources}', '{resource}_', ${resource}->image);
        Log::info('{Resource} image updated from file: ' . $data['image']);
    } elseif ($request->filled('image_url')) {
        // Media selector URL - convert full URL to relative path
        $imageUrl = $request->image_url;
        $relativePath = $imageUrl;

        if (str_contains($imageUrl, '/storage/')) {
            // Extract relative path from full URL
            $relativePath = str_replace(url('/storage/'), '', $imageUrl);
        }

        // Only update if different from current
        if ($relativePath !== ${resource}->image) {
            if (${resource}->image && !str_starts_with(${resource}->image, 'http')) {
                // Delete old local file if switching to different file
                $this->deleteImage(${resource}->image);
            }
            $data['image'] = $relativePath;
        }
    } elseif ($request->filled('old_image') && empty($request->image_url)) {
        // Media selector cleared - delete image
        if (${resource}->image && !str_starts_with(${resource}->image, 'http')) {
            $this->deleteImage(${resource}->image);
        }
        $data['image'] = null;
    }

    ${resource}->update($data);

    // Update translations if the model supports them
    if (method_exists(${resource}, 'setTranslation')) {
        if ($request->has('name')) {
            ${resource}->setTranslation('name', $request->name, app()->getLocale());
        }
        if ($request->has('description')) {
            ${resource}->setTranslation('description', $request->description, app()->getLocale());
        }
    }

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => '✅ {Resource} updated successfully!',
            'title' => 'Updated',
            'type' => 'success',
            '{resource}' => ${resource}
        ]);
    }

    sweetalert()->success('{Resource} updated successfully!');
    return redirect()->route('admin.{resources}.index');
}
```

### 7. Destroy Method

```php
/**
 * Remove the specified resource from storage.
 */
public function destroy({Resource} ${resource}, Request $request)
{
    // Delete image if exists
    if (${resource}->image) {
        Storage::disk('public')->delete(${resource}->image);
    }

    ${resource}->delete();

    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => '🗑️ {Resource} deleted successfully!',
            'title' => 'Deleted',
            'type' => 'success'
        ]);
    }

    sweetalert()->success('{Resource} deleted successfully!');
    return redirect()->route('admin.{resources}.index');
}
```

### 8. Additional Helper Methods

```php
/**
 * Toggle resource status
 */
public function toggleStatus({Resource} ${resource})
{
    ${resource}->update([
        'is_active' => !${resource}->is_active
    ]);

    $status = ${resource}->is_active ? 'activated' : 'deactivated';

    return redirect()->back()
        ->with('success', "{Resource} has been {$status} successfully.");
}

/**
 * Search resources
 */
public function search(Request $request)
{
    $query = $request->get('q');

    ${resources} = {Resource}::where(function ($q) use ($query) {
        $q->where('name', 'like', "%{$query}%")
          ->orWhere('slug', 'like', "%{$query}%");
    })->paginate(15);

    return view('admin.{resources}.index', compact('{resources}', 'query'));
}

/**
 * Get resources by status
 */
public function byStatus(Request $request)
{
    $status = $request->get('status', 'all');

    $query = {Resource}::query();

    switch ($status) {
        case 'active':
            $query->where('is_active', true);
            break;
        case 'inactive':
            $query->where('is_active', false);
            break;
        case 'with_image':
            $query->whereNotNull('image');
            break;
        case 'without_image':
            $query->whereNull('image');
            break;
    }

    ${resources} = $query->orderBy('created_at', 'desc')->paginate(15);

    return view('admin.{resources}.index', compact('{resources}', 'status'));
}
```

## View Pattern (`resources/views/admin/{resources}/index.blade.php`)

### 1. Basic Structure

```blade
@extends('admin.layouts.master_layout')

@section('pageTitle', '{Resources} Management')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">{Resources} Management</h4>
          <div class="card-tools">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#create{Resource}Modal">
              <i class="fas fa-plus"></i> Add New {Resource}
            </button>
            <button type="button" class="btn btn-info" id="refreshTableBtn">
              <i class="fas fa-sync-alt"></i> Refresh
            </button>
          </div>
        </div>
        <div class="card-body">
          <!-- Filters Section -->
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
            <!-- Add more filters as needed -->
            <div class="col-md-6">
              <div class="form-group">
                <label>&nbsp;</label>
                <div class="d-flex">
                  <button type="button" class="btn btn-secondary mr-2" id="clearFiltersBtn">
                    <i class="fas fa-times"></i> Clear Filters
                  </button>
                  <button type="button" class="btn btn-success" id="export{Resources}Btn">
                    <i class="fas fa-download"></i> Export {Resources}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- DataTable -->
          <div class="table-responsive">
            <table class="table table-striped table-bordered" id="{resources}Table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Image</th>
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
```

### 2. Styles Section

```blade
@push('styles')
  <link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
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

    #{resources}Table th {
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
```

### 3. Create/Edit Modal

```blade
<!-- {Resource} Modals -->
<div class="modal fade" id="create{Resource}Modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <form id="create{Resource}Form" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" id="{resource}Id" name="{resource}_id">
      <input type="hidden" id="formMethod" name="_method" value="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Create New {Resource}</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-8">
              
              <div class="form-group">
                <label for="createName">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="createName" name="name" required>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <label for="createSlug">Slug <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="createSlug" name="slug" required>
                <small class="form-text text-muted">URL-friendly version of the name. Auto-generated if left empty.</small>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <label for="createDescription">Description</label>
                <textarea class="form-control" id="createDescription" name="description" rows="4" 
                          placeholder="Enter {resource} description..."></textarea>
                <div class="invalid-feedback"></div>
              </div>

              <div class="form-group">
                <label for="createStatus">Status</label>
                <select class="form-control" id="createStatus" name="is_active">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select>
              </div>

            </div>

            <div class="col-md-4">
              <!-- Media Upload using reusable component -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-images mr-2"></i>
                    {Resource} Image
                  </h3>
                </div>
                <div class="card-body">
                  <x-media-selector 
                    name="image" 
                    label="" 
                    :required="false" 
                    preview_height="200px"
                    placeholder_text="Click to choose from gallery" 
                    upload_text="upload new image" 
                    :show_gallery="true"
                    :show_upload="true" 
                    :show_remove="true" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="create{Resource}Btn">
            <i class="fas fa-save" id="buttonIcon"></i> <span id="buttonText">Create {Resource}</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="view{Resource}Modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{Resource} Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="{resource}DetailsContent">
        <!-- {Resource} details will be loaded here -->
      </div>
    </div>
  </div>
</div>
```

### 4. JavaScript Section

```blade
@push('scripts')
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
  <script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
  <script>
    $(document).ready(function() {
      // Initialize server-side DataTable
      const table = $('#{resources}Table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.{resources}.index') }}',
          type: 'GET'
        },
        columns: [
          {
            data: 'id',
            name: 'id'
          },
          {
            data: 'image',
            name: 'image',
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
        $('#modalTitle').text('Create New {Resource}');
        $('#buttonText').text('Create {Resource}');
        $('#buttonIcon').removeClass('fa-edit').addClass('fa-save');
        $('#create{Resource}Btn').removeClass('btn-success').addClass('btn-primary');

        // Reset form method and resource ID
        $('#formMethod').val('POST');
        $('#${resource}Id').val('');

        // Reset media selector silently (without alerts)
        const mediaSelector = document.querySelector('#create{Resource}Modal .media-selector-component');
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
        if ($(this).attr('id') === 'create{Resource}Modal') {
          resetModalToCreateMode();
        }
      });

      // Reset modal when create button is clicked
      $('button[data-target="#create{Resource}Modal"]').on('click', function() {
        resetModalToCreateMode();
      });

      // Auto-generate slug from name
      $('#createName').on('keyup', function() {
        const name = $(this).val();
        if (name && $('#${resource}Id').val() === '') { // Only auto-generate for new resources
          const slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .replace(/\s+/g, '-')     // Replace spaces with hyphens
            .replace(/-+/g, '-')      // Remove multiple consecutive hyphens
            .trim('-');               // Remove leading/trailing hyphens
          $('#createSlug').val(slug);
        }
      });

      // Status filter
      $('#statusFilter').on('change', function() {
        const status = $(this).val();
        table.column(4).search(status).draw(); // Status is column index 4
      });

      // Clear filters
      $('#clearFiltersBtn').on('click', function() {
        $('#statusFilter').val('');
        table.search('').columns().search('').draw();
        showAlert('All filters cleared', 'info');
      });

      // Export resources
      $('#export{Resources}Btn').on('click', function() {
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

      // Create/Update {Resource}
      $('#create{Resource}Btn').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        const isEdit = $('#${resource}Id').val() !== '';
        const buttonLoadingText = isEdit ? '<i class="fas fa-spinner fa-spin"></i> Updating...' :
          '<i class="fas fa-spinner fa-spin"></i> Creating...';

        $btn.html(buttonLoadingText).prop('disabled', true);

        // Clear previous validation errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        const formData = new FormData($('#create{Resource}Form')[0]);
        const url = isEdit ? '{{ route('admin.{resources}.update', ':id') }}'.replace(':id', $('#${resource}Id').val()) :
          '{{ route('admin.{resources}.store') }}';

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
              $('#create{Resource}Modal').modal('hide');
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
                text: `An error occurred while ${isEdit ? 'updating' : 'creating'} the {resource}.`,
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

      // View {Resource}
      $(document).on('click', '.view-{resource}', function() {
        const {resource}Id = $(this).data('id');

        $.ajax({
          url: '{{ route('admin.{resources}.show', ':id') }}'.replace(':id', {resource}Id),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const {resource} = response.{resource};
              let image = {resource}.image 
                ? `<img src="${{resource}.image}" alt="${{resource}.name || '{Resource}'}" class="img-thumbnail" style="max-width:200px;">` 
                : '<div class="text-center text-muted"><i class="fas fa-image fa-3x"></i><br>No Image</div>';

              $('#${resource}DetailsContent').html(`
                <div class="row">
                  <div class="col-md-6">
                    <strong>Name:</strong> ${{resource}.name || 'N/A'}<br>
                    <strong>Slug:</strong> ${{resource}.slug}<br>
                    <strong>Status:</strong> ${{resource}.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}<br>
                    <strong>Created:</strong> ${new Date({resource}.created_at).toLocaleString()}<br>
                    <strong>Updated:</strong> ${new Date({resource}.updated_at).toLocaleString()}
                  </div>
                  <div class="col-md-6">
                    <strong>Image:</strong><br>
                    ${image}
                  </div>
                </div>
                ${{resource}.description ? `<div class="mt-3"><strong>Description:</strong><br>${{resource}.description}</div>` : ''}
              `);
              $('#view{Resource}Modal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading {resource} details');
          }
        });
      });

      // Edit {Resource}
      $(document).on('click', '.edit-{resource}', function() {
        const {resource}Id = $(this).data('id');
        
        $.ajax({
          url: '{{ route('admin.{resources}.edit', ':id') }}'.replace(':id', {resource}Id),
          type: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
          success: function(response) {
            if (response.success) {
              const {resource} = response.{resource};
              
              // Set modal mode to edit
              $('#modalTitle').text('Edit {Resource}');
              $('#buttonText').text('Update {Resource}');
              $('#buttonIcon').removeClass('fa-save').addClass('fa-edit');
              $('#create{Resource}Btn').removeClass('btn-primary').addClass('btn-success');

              // Set form method for update
              $('#formMethod').val('PUT');
              $('#${resource}Id').val({resource}.id);

              // Fill form fields
              $('#createName').val({resource}.name || '');
              $('#createSlug').val({resource}.slug);
              $('#createDescription').val({resource}.description || '');
              $('#createStatus').val({resource}.is_active ? '1' : '0');

              // Handle image preview if resource has one
              const mediaSelector = document.querySelector('#create{Resource}Modal .media-selector-component');
              if (mediaSelector && {resource}.image) {
                const componentId = mediaSelector.id;
                const urlInput = document.getElementById(componentId + '_url_input');
                const oldInput = document.getElementById(componentId + '_old_input');
                
                // Set the image URL in hidden inputs
                if (urlInput) urlInput.value = {resource}.image;
                if (oldInput) oldInput.value = {resource}.image;

                // Show the image preview
                MediaSelector.setImagePreview(componentId, {resource}.image);
              }

              $('#create{Resource}Modal').modal('show');
            }
          },
          error: function(xhr) {
            alert('Error loading {resource} data');
          }
        });
      });

      // Delete {Resource}
      $(document).on('click', '.delete-{resource}', function() {
        const {resource}Id = $(this).data('id');
        const {resource}Name = $(this).closest('tr').find('td:eq(2)').text().trim();

        Swal.fire({
          title: 'Are you sure?',
          text: `You are about to delete {resource}: ${{resource}Name}. This action cannot be undone!`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: '{{ route('admin.{resources}.destroy', ':id') }}'.replace(':id', {resource}Id),
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
                  text: 'An error occurred while deleting the {resource}.',
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
```

## Key Features of This CRUD Pattern

1. **Server-side DataTables**: Efficient handling of large datasets
2. **Ajax-based operations**: Seamless user experience without page reloads
3. **Modal-based forms**: Space-efficient create/edit functionality
4. **Media selector integration**: Consistent image upload/management
5. **Validation handling**: Both server-side and client-side validation
6. **Responsive design**: Works on all device sizes
7. **Search and filtering**: Multiple ways to find data
8. **Status management**: Toggle active/inactive states
9. **SweetAlert2 notifications**: Beautiful and consistent alerts
10. **Translation support**: Ready for multi-language applications

## Usage Instructions

1. Replace all `{Resource}`, `{resource}`, and `{resources}` placeholders with your actual resource names
2. Adjust the model relationships and fields according to your specific needs
3. Update validation rules based on your model's requirements
4. Modify the DataTable columns to match your resource's attributes
5. Add additional filters and search functionality as needed
6. Customize the modal form fields to match your resource's properties

This pattern ensures consistency across all CRUD operations in the application and provides a solid foundation for future development.