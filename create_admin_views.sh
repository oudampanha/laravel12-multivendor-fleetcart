#!/bin/bash

# Array of folders that need view files
folders=(
    "flash_sale_products"
    "language_lines"
    "menu_items"
    "meta_data"
    "option_values"
    "search_terms"
    "sliders"
    "slider_slides"
    "variation_values"
    "vendor_notifications"
    "vendor_reviews"
    "vendor_settings"
    "vendor_shipping_zones"
)

# Create index.blade.php template
create_index_template() {
    local folder=$1
    local title=$(echo $folder | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    local route_name=$(echo $folder | sed 's/_/-/g')
    local table_id="${folder}Table"
    
    cat > "resources/views/admin/$folder/index.blade.php" << EOF
@extends('admin.layouts.master_layout')

@section('pageTitle', '$title Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">$title Management</h4>
        <div class="card-tools">
          <a href="{{ route('admin.$route_name.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-bordered" id="$table_id">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse(\$${folder} ?? [] as \$item)
              <tr>
                <td>{{ \$item->id }}</td>
                <td>
                  <strong>{{ \$item->name ?? \$item->title ?? 'N/A' }}</strong>
                </td>
                <td>
                  @if(\$item->is_active ?? \$item->status ?? true)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-danger">Inactive</span>
                  @endif
                </td>
                <td>{{ \$item->created_at->format('Y-m-d H:i') }}</td>
                <td>
                  <div class="btn-group">
                    <a href="{{ route('admin.$route_name.show', \$item->id) }}" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.$route_name.edit', \$item->id) }}" class="btn btn-sm btn-warning">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.$route_name.destroy', \$item->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">No records found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="{{ assetUrl() }}assets/backend/lib/datatables/css/dataTables.bootstrap4.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/jquery.dataTables.min.js"></script>
<script src="{{ assetUrl() }}assets/backend/lib/datatables/js/dataTables.bootstrap4.min.js"></script>
<script>
\$(document).ready(function() {
  \$('#$table_id').DataTable({
    responsive: true,
    order: [[0, 'desc']],
    pageLength: 25
  });
});
</script>
@endpush
EOF
}

# Create create.blade.php template
create_create_template() {
    local folder=$1
    local title=$(echo $folder | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    local route_name=$(echo $folder | sed 's/_/-/g')
    local singular=$(echo $folder | sed 's/s$//' | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    
    cat > "resources/views/admin/$folder/create.blade.php" << EOF
@extends('admin.layouts.master_layout')

@section('pageTitle', 'Create New $singular')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Create New $singular</h4>
        <div class="card-tools">
          <a href="{{ route('admin.$route_name.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.$route_name.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ \$message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                  <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ \$message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ \$message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Create $singular
            </button>
            <a href="{{ route('admin.$route_name.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
EOF
}

# Create edit.blade.php template
create_edit_template() {
    local folder=$1
    local title=$(echo $folder | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    local route_name=$(echo $folder | sed 's/_/-/g')
    local singular=$(echo $folder | sed 's/s$//' | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    local variable=$(echo $folder | sed 's/s$//' | tr '[A-Z]' '[a-z]')
    
    cat > "resources/views/admin/$folder/edit.blade.php" << EOF
@extends('admin.layouts.master_layout')

@section('pageTitle', 'Edit $singular')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Edit $singular: {{ \$${variable}->name ?? \$${variable}->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.$route_name.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.$route_name.update', \$${variable}->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', \$${variable}->name ?? \$${variable}->title) }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ \$message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                  <option value="1" {{ old('status', \$${variable}->status ?? \$${variable}->is_active ?? '1') == '1' ? 'selected' : '' }}>Active</option>
                  <option value="0" {{ old('status', \$${variable}->status ?? \$${variable}->is_active ?? '1') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                  <div class="invalid-feedback">{{ \$message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" 
                      id="description" name="description" rows="3">{{ old('description', \$${variable}->description) }}</textarea>
            @error('description')
              <div class="invalid-feedback">{{ \$message }}</div>
            @enderror
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Update $singular
            </button>
            <a href="{{ route('admin.$route_name.index') }}" class="btn btn-secondary ml-2">
              <i class="fas fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
EOF
}

# Create show.blade.php template
create_show_template() {
    local folder=$1
    local title=$(echo $folder | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    local route_name=$(echo $folder | sed 's/_/-/g')
    local singular=$(echo $folder | sed 's/s$//' | sed 's/_/ /g' | sed 's/\b\w/\U&/g')
    local variable=$(echo $folder | sed 's/s$//' | tr '[A-Z]' '[a-z]')
    
    cat > "resources/views/admin/$folder/show.blade.php" << EOF
@extends('admin.layouts.master_layout')

@section('pageTitle', '$singular Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">$singular Details: {{ \$${variable}->name ?? \$${variable}->title ?? 'N/A' }}</h4>
        <div class="card-tools">
          <a href="{{ route('admin.$route_name.edit', \$${variable}->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit
          </a>
          <a href="{{ route('admin.$route_name.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h5>$singular Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <th width="30%">ID</th>
                      <td>{{ \$${variable}->id }}</td>
                    </tr>
                    <tr>
                      <th>Name</th>
                      <td><strong>{{ \$${variable}->name ?? \$${variable}->title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        @if(\$${variable}->status ?? \$${variable}->is_active ?? true)
                          <span class="badge badge-success">Active</span>
                        @else
                          <span class="badge badge-danger">Inactive</span>
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <th>Created At</th>
                      <td>{{ \$${variable}->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                    <tr>
                      <th>Updated At</th>
                      <td>{{ \$${variable}->updated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            @if(\$${variable}->description)
            <div class="card">
              <div class="card-header">
                <h5>Description</h5>
              </div>
              <div class="card-body">
                <p>{{ \$${variable}->description }}</p>
              </div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
EOF
}

# Main execution
for folder in "${folders[@]}"; do
    echo "Creating views for $folder..."
    
    # Create directory if it doesn't exist
    mkdir -p "resources/views/admin/$folder"
    
    # Create all template files
    create_index_template "$folder"
    create_create_template "$folder"
    create_edit_template "$folder"
    create_show_template "$folder"
    
    echo "✓ Created views for $folder"
done

echo "All admin view templates have been created successfully!"