@extends('admin.layouts.master_layout')

@section('title', 'Media Management')

@section('content')
  <!-- Main Media Manager Container -->
  <div id="mediaManagerContainer"></div>
@endsection

@push('scripts')
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Include MediaManager.js library -->
  <script src="{{ asset('assets/backend/js/MediaManager.js') }}"></script>

  <script>
    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {

      // Get the container element
      const container = document.getElementById('mediaManagerContainer');

      if (!container) {
        console.error('Media Manager container not found!');
        return;
      }

      // Initialize MediaManager with inline mode (full page)
      const mediaManager = new MediaManager({
        // Container element for inline mode
        container: container,

        // API endpoints
        endpoints: {
          list: '{{ route('admin.media.list') }}',
          upload: '{{ route('admin.media.upload') }}',
          bulkUpload: '{{ route('admin.media.bulk-upload') }}',
          createFolder: '{{ route('admin.media.create-folder') }}',
          renameFolder: '{{ route('admin.media.rename-folder') }}',
          deleteFolder: '{{ route('admin.media.delete-folder') }}',
          renameFile: '{{ route('admin.media.rename-file') }}',
          deleteFile: '/admin.media/delete/{id}',
          moveToFolder: '{{ route('admin.media.move-to-folder') }}',
          copyToFolder: '{{ route('admin.media.copy-to-folder') }}',
          bulkMoveToFolder: '{{ route('admin.media.bulk-move-to-folder') }}',
          bulkCopyToFolder: '{{ route('admin.media.bulk-copy-to-folder') }}',
          getFolders: '{{ route('admin.media.folders') }}'
        },

        // UI configuration
        modal: false, // Inline mode
        multiple: true,
        showUploadButton: true,
        showCreateFolderButton: true,
        showViewControls: true,
        showSearch: true,
        showBreadcrumb: true,
        showContextMenu: true,

        // File restrictions
        maxFileSize: 10 * 1024 * 1024, // 10MB
        acceptedTypes: 'image/*,.pdf,.doc,.docx,.xlsx',

        // CSRF token
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,

        // Callbacks (optional)
        onSelect: function(files) {
          console.log('Files selected:', files);
        },

        onUpload: function(response) {
          console.log('Upload completed:', response);
        },

        onError: function(error) {
          console.error('MediaManager Error:', error);
        }
      });

      // Make it available globally for debugging
      window.mediaManager = mediaManager;

      // Note: init() is called automatically when container is provided

      console.log('MediaManager initialized successfully');
    });
  </script>

  <!-- Custom styles -->
  <style>
    #mediaManagerContainer {
      min-height: calc(100vh - 200px);
    }

    /* Override container padding */
    .container {
      max-width: 100%;
      padding: 0 15px;
    }
  </style>
@endpush
