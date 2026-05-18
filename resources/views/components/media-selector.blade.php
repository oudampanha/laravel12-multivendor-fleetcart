@props([
    'name' => 'image',
    'label' => 'Image',
    'required' => false,
    'multiple' => false,
    'value' => null,
    'preview_height' => '200px',
    'preview_width' => '100%',
    'accept' => 'image/*',
    'max_size' => '5MB',
    'show_remove' => true,
    'show_gallery' => true,
    'show_upload' => true,
    'placeholder_text' => 'Click to choose from gallery',
    'upload_text' => 'or upload new image',
    'container_class' => '',
    'preview_class' => '',
])

@php
  $componentId = 'media_selector_' . Str::random(8);
  $inputName = $name;
  $hiddenUrlName = $name . '_url';
  $hiddenOldName = 'old_' . $name;
  $fileInputId = $inputName . '_file_input';
  $previewId = $componentId . '_preview';
  $uploadContentId = $componentId . '_upload_content';
  $imagePreviewId = $componentId . '_image_preview';
  $previewImgId = $componentId . '_preview_img';
  $maxSizeBytes = (int) filter_var($max_size, FILTER_SANITIZE_NUMBER_INT) * 1024 * 1024;
@endphp

<div class="media-selector-component {{ $container_class }}" id="{{ $componentId }}">
  @if ($label)
    <label class="form-control-label mb-2">
      {{ $label }}
      @if ($required)
        <span class="text-danger">*</span>
      @endif
    </label>
  @endif

  <!-- Hidden form inputs -->
  <input type="hidden" name="{{ $hiddenOldName }}" id="{{ $componentId }}_old_input" value="{{ $value }}">
  <input type="hidden" name="{{ $hiddenUrlName }}" id="{{ $componentId }}_url_input" value="{{ $value }}">
  <input type="hidden" name="{{ $inputName }}" id="{{ $componentId }}_id_input" value="{{ $value }}">
  <input type="file" name="{{ $inputName }}_file" id="{{ $fileInputId }}" accept="{{ $accept }}"
    data-component-id="{{ $componentId }}" style="display: none;" @if ($multiple) multiple @endif>

  <!-- Upload area with preview -->
  <div class="media-upload-area" id="{{ $componentId }}_upload_area"
    style="min-height: {{ $preview_height }}; width: {{ $preview_width }};">

    <!-- Upload content (shown when no image) -->
    <div class="upload-content" id="{{ $uploadContentId }}">
      <i class="fas fa-cloud-upload-alt"></i>
      <p class="mb-1"><strong>{{ $placeholder_text }}</strong></p>
      <small class="text-muted">PNG, JPG, GIF up to {{ $max_size }}</small>
    </div>

    <!-- Image preview (hidden initially) -->
    <div class="image-preview {{ $preview_class }}" id="{{ $imagePreviewId }}" style="display: none;">
      <img id="{{ $previewImgId }}" src="" alt="Preview"
        style="width: 100%; height: {{ $preview_height }}; object-fit: cover; border-radius: 5px;">

      @if ($show_remove || $show_gallery)
        <div class="preview-overlay">
          @if ($show_gallery)
            <button type="button" class="btn btn-sm btn-primary"
              onclick="MediaSelector.openGallery('{{ $componentId }}'); event.stopPropagation();">
              <i class="fas fa-edit"></i> Change
            </button>
          @endif

          @if ($show_remove)
            <button type="button" class="btn btn-sm btn-danger"
              onclick="MediaSelector.clearImage('{{ $componentId }}'); event.stopPropagation();">
              <i class="fas fa-trash"></i> Remove
            </button>
          @endif
        </div>
      @endif
    </div>
  </div>

  <!-- Action buttons -->
  {{-- <div class="mt-2 text-center media-selector-actions">
    @if ($show_gallery)
      <div class="mb-2">
        <button type="button" class="btn btn-success btn-sm"
          onclick="MediaSelector.openGallery('{{ $componentId }}')">
          <i class="fas fa-images"></i> Choose from Gallery
        </button>
      </div>
    @endif

    @if ($show_upload && $show_gallery)
      <div class="mb-2">
        <small class="text-muted">or</small>
      </div>
    @endif

    @if ($show_upload)
      <div class="mb-2">
        <button type="button" class="btn btn-outline-success btn-sm"
          onclick="document.getElementById('{{ $fileInputId }}').click()">
          <i class="fas fa-upload"></i> {{ $upload_text ?: 'Upload New' }}
        </button>
      </div>
    @endif
  </div> --}}
</div>

@once
  @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/backend/css/media-selector.css') }}">
  @endpush

  @push('scripts')
    <!-- Include required dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/backend/js/MediaManager.js') }}"></script>

    <!-- Configure MediaSelector with Laravel routes -->
    <script>
      window.MediaSelectorConfig = {
        endpoints: {
          list: '{{ route('admin.media.list') }}',
          upload: '{{ route('admin.media.upload') }}',
          bulkUpload: '{{ route('admin.media.bulk-upload') }}',
          createFolder: '{{ route('admin.media.create-folder') }}',
          renameFolder: '{{ route('admin.media.rename-folder') }}',
          deleteFolder: '{{ route('admin.media.delete-folder') }}',
          renameFile: '{{ route('admin.media.rename-file') }}',
          deleteFile: '{{ url('/admin.media/delete') }}/{id}',
          moveToFolder: '{{ route('admin.media.move-to-folder') }}',
          copyToFolder: '{{ route('admin.media.copy-to-folder') }}',
          bulkMoveToFolder: '{{ route('admin.media.bulk-move-to-folder') }}',
          bulkCopyToFolder: '{{ route('admin.media.bulk-copy-to-folder') }}',
          getFolders: '{{ route('admin.media.folders') }}'
        },
        mediaManagerDefaults: {
          modal: false,
          multiple: false,
          showUploadButton: true,
          showCreateFolderButton: false,
          showViewControls: true,
          showSearch: true,
          showBreadcrumb: true,
          showContextMenu: false,
          maxFileSize: 5 * 1024 * 1024,
          acceptedTypes: 'image/*'
        }
      };
    </script>
    <script src="{{ asset('assets/backend/js/MediaSelector.js') }}"></script>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Initialize file input change handlers for all media selectors
        document.querySelectorAll('.media-selector-component input[type="file"]').forEach(function(input) {
          input.addEventListener('change', function(e) {
            const componentId = this.getAttribute('data-component-id');
            MediaSelector.handleFileUpload(componentId, this);
          });
        });

        // Initialize click handlers for upload areas
        document.querySelectorAll('.media-upload-area').forEach(function(area) {
          area.addEventListener('click', function(e) {
            if (e.target === this || e.target.closest('.upload-content')) {
              const componentId = this.id.replace('_upload_area', '');
              MediaSelector.openGallery(componentId);
            }
          });
        });

        // Initialize existing images
        document.querySelectorAll('.media-selector-component').forEach(function(component) {
          const componentId = component.id;
          const urlInput = component.querySelector('[id$="_url_input"]');
          if (urlInput && urlInput.value) {
            MediaSelector.setImagePreview(componentId, urlInput.value);
          }
        });
      });
    </script>
  @endpush
@endonce
