/**
 * MediaManager.js - Complete Media Management Library
 * Version: 2.0.0
 * With full container (inline) mode support
 */

(function (window, document) {
  'use strict';
  // Default configuration
  const defaults = {
    // Container for inline mode
    container: null,

    // API endpoints
    endpoints: {
      list: 'admin/media/list',
      upload: 'admin/media/upload',
      bulkUpload: 'admin/media/bulk-upload',
      createFolder: 'admin/media/create-folder',
      renameFolder: 'admin/media/rename-folder',
      deleteFolder: 'admin/media/delete-folder',
      renameFile: 'admin/media/rename-file',
      deleteFile: 'admin/media/delete/{id}',
      moveToFolder: 'admin/media/move-to-folder',
      copyToFolder: 'admin/media/copy-to-folder',
      bulkMoveToFolder: 'admin/media/bulk-move-to-folder',
      bulkCopyToFolder: 'admin/media/bulk-copy-to-folder',
      getFolders: 'admin/media/folders'
    },

    // UI options
    modal: true,
    multiple: false,
    showUploadButton: true,
    showCreateFolderButton: true,
    showViewControls: true,
    showSearch: true,
    showBreadcrumb: true,
    showContextMenu: true,

    // Features
    features: {
      upload: true,
      createFolder: true,
      rename: true,
      delete: true,
      download: true,
      search: true,
      dragDrop: true,
      contextMenu: true,
      multiSelect: true,
      folderNavigation: true
    },

    // File options
    maxFileSize: 10 * 1024 * 1024,
    acceptedTypes: 'image/*,.pdf,.doc,.docx,.xlsx',

    // Callbacks
    onSelect: null,
    onUpload: null,
    onFolderChange: null,
    onDelete: null,
    onRename: null,
    onError: null,
    onClose: null,

    // Texts
    texts: {
      title: 'Media Management',
      upload: 'Upload',
      createFolder: 'Add Folder',
      search: 'Search file name...',
      noFiles: 'This folder is empty',
      uploadHere: 'Drag and drop files here',
      select: 'Select',
      cancel: 'Cancel',
      delete: 'Delete',
      rename: 'Rename',
      open: 'Open',
      download: 'Download',
      copyUrl: 'Copy URL'
    },

    // CSRF token
    csrfToken: null,
    headers: {},

    // Theme
    theme: {
      primaryColor: '#007bff',
      successColor: '#28a745',
      dangerColor: '#dc3545',
      borderRadius: '8px'
    }
  };

  /**
   * MediaManager Constructor
   */
  function MediaManager(options) {
    this.options = Object.assign({}, defaults, options);
    this.currentPath = '';
    this.selectedFiles = [];
    this.selectedItems = []; // For bulk selection
    this.currentView = 'grid';
    this.container = null;
    this.uploadModal = null;
    this.contextMenu = null;
    this.data = { folders: [], files: [] };

    // Get CSRF token
    if (!this.options.csrfToken) {
      const tokenMeta = document.querySelector('meta[name="csrf-token"]');
      if (tokenMeta) {
        this.options.csrfToken = tokenMeta.content;
      }
    }

    // Auto-init if container is provided
    if (this.options.container) {
      this.options.modal = false;
      this.init();
    }
  }

  /**
   * Initialize
   */
  MediaManager.prototype.init = function () {
    this.setupStyles();

    if (this.options.container) {
      this.renderInline();
    } else if (this.options.modal) {
      this.createModal();
    }

    this.bindEvents();
    this.loadFiles();
  };

  /**
   * Setup styles
   */
  MediaManager.prototype.setupStyles = function () {
    if (document.getElementById('media-manager-styles')) return;

    const style = document.createElement('style');
    style.id = 'media-manager-styles';
    style.innerHTML = `
            .media-container {
                background: #f8f9fa;
                min-height: 100vh;
                padding: 20px;
            }
            .media-header {
                background: white;
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                flex-wrap: wrap;
                gap: 15px;
            }

            .search-section {
                display: flex;
                align-items: center;
                gap: 10px;
                flex: 1;
                max-width: 400px;
            }

            .search-input {
                flex: 1;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }

            .search-btn {
                padding: 8px 20px;
                background: #f8f9fa;
                border: 1px solid #ddd;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }

            .file-count {
                color: #666;
                font-size: 14px;
            }

            .view-controls {
                display: flex;
                gap: 5px;
            }

            .view-btn {
                padding: 8px 12px;
                background: #6c757d;
                color: white;
                border: none;
                cursor: pointer;
                font-size: 14px;
            }

            .view-btn:first-child {
                border-radius: 4px 0 0 4px;
            }

            .view-btn:last-child {
                border-radius: 0 4px 4px 0;
            }

            .view-btn.active {
                background: #495057;
            }

            .action-buttons {
                display: flex;
                gap: 10px;
            }

            .btn-add-folder, .btn-upload, .btn-toggle-selection, .btn-select-all, .btn-clear-selection, .btn-bulk-delete {
                padding: 8px 16px;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }

            .btn-add-folder {
                background: #007bff;
            }

            .btn-upload {
                background: #28a745;
            }

            .btn-toggle-selection {
                background: #6c757d;
            }

            .btn-select-all {
                background: #17a2b8;
            }

            .btn-clear-selection {
                background: #ffc107;
                color: #212529;
            }

            .btn-bulk-delete {
                background: #dc3545;
            }

            .btn-bulk-move {
                background: #fd7e14;
            }

            .btn-bulk-copy {
                background: #6f42c1;
            }

            .item-checkbox {
                background: rgba(255, 255, 255, 0.9);
                border-radius: 4px;
                padding: 2px;
            }

            .item-checkbox input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
                margin: 0;
            }

            .media-item {
                position: relative;
            }

            .media-item .item-checkbox {
                position: absolute;
                top: 5px;
                left: 5px;
                z-index: 10;
            }

            .media-list-item .item-checkbox {
                display: none;
                margin-right: 10px;
            }

            .breadcrumb-section {
                background: white;
                padding: 10px 20px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .breadcrumb {
                margin: 0;
                padding: 0;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .breadcrumb-item {
                color: #007bff;
                text-decoration: none;
                font-size: 14px;
                cursor: pointer;
            }

            .breadcrumb-separator {
                color: #666;
                margin: 0 5px;
            }

            .media-grid-container {
                background: white;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }

            .media-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 15px;
            }

            .media-list {
                display: none;
            }

            .list-view .media-grid {
                display: none;
            }

            .list-view .media-list {
                display: block;
            }

            .media-item {
                background: white;
                border: 1px solid #e0e0e0;
                border-radius: 4px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.2s;
                position: relative;
            }

            .media-item:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transform: translateY(-2px);
            }

            .media-item.selected {
                border-color: #007bff;
                box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
            }

            .media-thumbnail {
                width: 100%;
                height: 140px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f0f0f0;
                position: relative;
                overflow: hidden;
            }

            .media-thumbnail img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .folder-icon, .file-icon {
                font-size: 48px;
                color: #6c757d;
            }

            .media-name {
                padding: 8px;
                background: rgba(0,0,0,0.7);
                color: white;
                font-size: 12px;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
            }

            .media-list-item {
                display: flex;
                align-items: center;
                padding: 10px;
                border-bottom: 1px solid #e0e0e0;
                cursor: pointer;
                position: relative;
            }

            .media-list-item:hover {
                background: #f8f9fa;
            }

            .list-thumbnail {
                width: 40px;
                height: 40px;
                margin-right: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .list-details {
                flex: 1;
            }

            .list-actions {
                display: flex;
                gap: 5px;
            }

            .upload-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1000;
                align-items: center;
                justify-content: center;
            }

            .upload-modal.show {
                display: flex;
            }

            .upload-content {
                background: white;
                border-radius: 8px;
                padding: 0px 10px 0px 10px;
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
            }

            // .upload-area {
            //     border: 2px dashed #dee2e6;
            //     border-radius: 8px;
            //     padding: 40px;
            //     text-align: center;
            //     background: #f8f9fa;
            //     cursor: pointer;
            //     transition: all 0.3s;
            // }

            .upload-area.dragover {
                border-color: #007bff;
                background: #e7f3ff;
            }

            .context-menu {
                position: fixed;
                background: white;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 5px 0;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                z-index: 1001;
                display: none;
            }

            .context-menu-item {
                padding: 8px 20px;
                cursor: pointer;
                font-size: 14px;
                color: #333;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .context-menu-item:hover {
                background: #f8f9fa;
            }

            .context-menu-separator {
                border-top: 1px solid #e0e0e0;
                margin-top: 5px;
                padding-top: 8px;
            }

            .folder-selection-modal {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1002;
                display: none;
                align-items: center;
                justify-content: center;
            }

            .folder-selection-modal-content {
                background: white;
                border-radius: 8px;
                width: 90%;
                max-width: 500px;
                max-height: 80vh;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .folder-selection-header {
                padding: 20px;
                border-bottom: 1px solid #e0e0e0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f8f9fa;
            }

            .folder-selection-header h4 {
                margin: 0;
                font-size: 18px;
                color: #333;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .close-folder-modal {
                background: none;
                border: none;
                font-size: 20px;
                cursor: pointer;
                color: #6c757d;
                padding: 5px;
                border-radius: 4px;
            }

            .close-folder-modal:hover {
                background: #e9ecef;
                color: #dc3545;
            }

            .folder-selection-body {
                padding: 20px;
                flex: 1;
                overflow-y: auto;
            }

            .folder-list {
                border: 1px solid #e0e0e0;
                border-radius: 4px;
                max-height: 300px;
                overflow-y: auto;
                background: white;
            }

            .folder-option {
                padding: 10px 15px;
                cursor: pointer;
                border-bottom: 1px solid #f0f0f0;
                display: flex;
                align-items: center;
                gap: 10px;
                transition: all 0.2s;
            }

            .folder-option:last-child {
                border-bottom: none;
            }

            .folder-option:hover {
                background: #f8f9fa;
            }

            .folder-option.selected {
                background: #e7f3ff;
                border-color: #007bff;
                color: #007bff;
            }

            .folder-option i {
                width: 16px;
                text-align: center;
                color: #6c757d;
            }

            .folder-option.selected i {
                color: #007bff;
            }

            .folder-selection-footer {
                padding: 20px;
                border-top: 1px solid #e0e0e0;
                display: flex;
                justify-content: flex-end;
                gap: 10px;
                background: #f8f9fa;
            }

            .selected-files-preview {
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 4px;
                border: 1px solid #e0e0e0;
            }

            .files-preview-list {
                max-height: 150px;
                overflow-y: auto;
            }

            .file-preview-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 5px 0;
                font-size: 14px;
            }

            .file-preview-item i {
                color: #6c757d;
                width: 16px;
                text-align: center;
            }

            .file-preview-item .file-name {
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .btn {
                padding: 8px 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
            }

            .btn-primary {
                background: #007bff;
                color: white;
            }

            .btn-success {
                background: #28a745;
                color: white;
            }

            .btn-danger {
                background: #dc3545;
                color: white;
            }

            .btn-secondary {
                background: #6c757d;
                color: white;
            }
        `;
    document.head.appendChild(style);
  };

  /**
   * Render inline interface
   */
  MediaManager.prototype.renderInline = function () {
    const container = this.options.container;
    if (!container) return;

    container.innerHTML = `
            <div class="media-container">
                <div class="media-header">
                    <div class="search-section">
                        <input type="text" class="search-input" placeholder="${this.options.texts.search}">
                        <button class="search-btn">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>

                    <span class="file-count">
                        Total: <strong class="folder-count">0</strong> folders,
                        <strong class="file-count">0</strong> files
                    </span>

                    <div class="view-controls">
                        <button class="view-btn active" data-view="grid">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="view-btn" data-view="list">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>

                    <div class="action-buttons">
                        <button class="btn-select-all" style="display: none;">
                            <i class="fas fa-check-square"></i> Select All
                        </button>
                        <button class="btn-clear-selection" style="display: none;">
                            <i class="fas fa-times"></i> Clear
                        </button>
                        <button class="btn-bulk-delete" style="display: none;">
                            <i class="fas fa-trash"></i> Delete (<span class="selected-count">0</span>)
                        </button>
                        <button class="btn-bulk-move" style="display: none;">
                            <i class="fas fa-cut"></i> Move (<span class="selected-count">0</span>)
                        </button>
                        <button class="btn-bulk-copy" style="display: none;">
                            <i class="fas fa-copy"></i> Copy (<span class="selected-count">0</span>)
                        </button>
                        ${this.options.showCreateFolderButton ? `
                            <button class="btn-add-folder">
                                <i class="fas fa-folder-plus"></i> ${this.options.texts.createFolder}
                            </button>
                        ` : ''}
                        ${this.options.showUploadButton ? `
                            <button class="btn-upload">
                                <i class="fas fa-upload"></i> ${this.options.texts.upload}
                            </button>
                        ` : ''}
                    </div>
                </div>

                <div class="breadcrumb-section">
                    <nav class="breadcrumb"></nav>
                </div>

                <div class="media-grid-container">
                    <div class="media-grid"></div>
                    <div class="media-list"></div>
                </div>
            </div>
        `;

    // Keep the original container reference
    this.container = container;
    this.createUploadModal();
    this.createContextMenu();
  };

  /**
   * Create upload modal
   */
  MediaManager.prototype.createUploadModal = function () {
    const modal = document.createElement('div');
    modal.className = 'upload-modal';
    modal.innerHTML = `
        <div class="upload-content">
            <div class="upload-header">
                <h3><i class="fas fa-cloud-upload-alt"></i> Upload Files</h3>
                <button class="close-modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="upload-body">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h4>Drag & Drop Files Here</h4>
                    <p>or click to browse</p>
                    <button class="choose-files-btn">Choose Files</button>
                    <input type="file" class="file-input" multiple accept="${this.options.acceptedTypes}">
                </div>

                <div class="file-preview" id="filePreview"></div>

                <div class="upload-actions" style="display: none;">
                    <div class="upload-summary">
                        <p><strong id="file-count">0</strong> files selected</p>
                    </div>
                </div>
            </div>

            <div class="upload-footer">
                <button class="btn btn-secondary cancel-upload">Cancel</button>
                <button class="btn btn-primary start-upload">Upload Files</button>
            </div>
        </div>
         <style>
    .upload-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 20px 20px 0 20px;
      border-bottom: 1px solid #dee2e6;
      margin-bottom: 20px;
    }

    .upload-header h3 {
      margin: 0;
      font-size: 18px;
      color: #333;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .close-modal {
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      color: #6c757d;
      padding: 5px;
      border-radius: 4px;
      transition: background-color 0.2s;
    }

    .close-modal:hover {
      background: #f8f9fa;
      color: #dc3545;
    }

    .upload-body {
      padding: 0 20px;
    }

    .upload-footer {
      padding: 20px;
      border-top: 1px solid #dee2e6;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 20px;
    }

    .upload-area {
      border: 2px dashed #007bff;
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
      background: #f8f9fa;
      margin-bottom: 20px;
    }

    .upload-area:hover {
      background: #e9ecef;
      border-color: #0056b3;
    }

    .upload-area.dragover {
      background: #d1ecf1;
      border-color: #28a745;
    }

    .upload-icon {
      font-size: 48px;
      color: #007bff;
      margin-bottom: 5px;
    }

    .upload-area h4 {
      margin: 5px 0 10px 0;
      color: #333;
      font-weight: 500;
    }

    .upload-area p {
      color: #6c757d;
      margin-bottom: 10px;
    }

    .choose-files-btn {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.2s;
    }

    .choose-files-btn:hover {
      background: #0056b3;
    }

    .file-input {
      display: none;
    }

    .file-preview {
      margin-top: 20px;
      min-height: 0;
    }

    .file-item {
      display: flex;
      align-items: center;
      padding: 10px;
      background: #f8f9fa;
      margin-bottom: 10px;
      border-radius: 8px;
      border: 1px solid #dee2e6;
    }

    .file-preview-img {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 4px;
      margin-right: 10px;
    }

    .file-item .remove-file {
      background: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
      transition: background-color 0.2s;
    }

    .file-item .remove-file:hover {
      background: #c82333;
    }

    .upload-actions {
      margin-top: 15px;
      padding: 15px;
      background: #f8f9fa;
      border-radius: 5px;
      border: 1px solid #dee2e6;
    }

    .upload-summary p {
      margin: 0;
      color: #495057;
    }

    .btn {
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
      transition: all 0.2s;
    }

    .btn-primary {
      background: #007bff;
      color: white;
    }

    .btn-primary:hover {
      background: #0056b3;
    }

    .btn-primary:disabled {
      background: #6c757d;
      cursor: not-allowed;
    }

    .btn-secondary {
      background: #6c757d;
      color: white;
    }

    .btn-secondary:hover {
      background: #545b62;
    }
  </style>
    `;

    document.body.appendChild(modal);
    this.uploadModal = modal;

    // Bind event handlers
    this.bindUploadModalEvents();
  };

  // Add method to show the upload modal
  MediaManager.prototype.showUploadModal = function () {
    if (!this.uploadModal) {
      this.createUploadModal();
    }
    if (this.uploadModal) {
      this.uploadModal.classList.add('show');
    }
  };

  // Add method to hide the upload modal
  MediaManager.prototype.hideUploadModal = function () {
    if (this.uploadModal) {
      this.uploadModal.classList.remove('show');
      // Clear file input and preview
      this.clearFilePreview();
    }
  };

  // Bind event handlers for the upload modal
  MediaManager.prototype.bindUploadModalEvents = function () {
    if (!this.uploadModal) return;

    const modal = this.uploadModal;
    const closeBtn = modal.querySelector('.close-modal');
    const chooseFilesBtn = modal.querySelector('.choose-files-btn');
    const fileInput = modal.querySelector('.file-input');
    const uploadArea = modal.querySelector('.upload-area');
    const cancelBtn = modal.querySelector('.cancel-upload');
    const startUploadBtn = modal.querySelector('.start-upload');

    // Close modal - prevent bubbling and ensure proper binding
    if (closeBtn) {
      closeBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        this.hideUploadModal();
      });
    }

    // Click outside to close
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        this.hideUploadModal();
      }
    });

    // Choose files button - prevent bubbling
    if (chooseFilesBtn) {
      chooseFilesBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Prevent event from bubbling to upload area
        if (fileInput) {
          fileInput.click();
        }
      });
    }

    // Upload area click to select files
    if (uploadArea) {
      uploadArea.addEventListener('click', (e) => {
        // Only trigger file selection if clicking the area itself, not buttons
        if (e.target === uploadArea || uploadArea.contains(e.target) && !e.target.matches('button')) {
          if (fileInput) {
            fileInput.click();
          }
        }
      });
    }

    // File input change
    if (fileInput) {
      fileInput.addEventListener('change', (e) => {
        this.handleFileSelection(e.target.files);
      });
    }

    // Drag and drop
    if (uploadArea) {
      uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
      });

      uploadArea.addEventListener('dragleave', (e) => {
        // Only remove the class if we're leaving the upload area entirely
        if (!uploadArea.contains(e.relatedTarget)) {
          uploadArea.classList.remove('dragover');
        }
      });

      uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        this.handleFileSelection(e.dataTransfer.files);
      });
    }

    // Cancel upload
    if (cancelBtn) {
      cancelBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.hideUploadModal();
        this.clearFilePreview();
      });
    }

    // Start upload
    if (startUploadBtn) {
      startUploadBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.startUpload();
      });
    }
  };

  // Handle file selection with image preview
  MediaManager.prototype.handleFileSelection = function (files) {
    if (!this.uploadModal) return;

    // Show file preview and upload actions
    const filePreview = this.uploadModal.querySelector('.file-preview');
    const uploadActions = this.uploadModal.querySelector('.upload-actions');
    const fileCountEl = this.uploadModal.querySelector('#file-count');

    if (!filePreview) return;

    // Convert FileList to Array and store
    this.selectedFiles = Array.from(files);

    // Clear previous preview
    filePreview.innerHTML = '';

    if (this.selectedFiles.length === 0) {
      filePreview.style.display = 'none';
      if (uploadActions) uploadActions.style.display = 'none';
      return;
    }

    // Show file preview
    filePreview.style.display = 'block';

    // Display selected files with preview
    this.selectedFiles.forEach((file, index) => {
      const fileItem = document.createElement('div');
      fileItem.className = 'file-item';

      // Create preview based on file type
      let preview = '';
      if (file.type.startsWith('image/')) {
        // Create image preview
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = fileItem.querySelector('.file-preview-img');
          if (img) img.src = e.target.result;
        };
        reader.readAsDataURL(file);
        preview = `<img class="file-preview-img" src="" alt="Preview">`;
      } else {
        // Use icon for non-image files
        preview = `<div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; margin-right: 10px;"><i class="fas fa-file" style="font-size: 24px; color: #6c757d;"></i></div>`;
      }

      fileItem.innerHTML = `
              ${preview}
              <div style="flex: 1;">
                  <div style="font-weight: 500; margin-bottom: 5px;">${file.name}</div>
                  <div style="font-size: 12px; color: #6c757d;">${this.formatFileSize(file.size)}</div>
              </div>
              <button class="remove-file" data-index="${index}">
                  <i class="fas fa-times"></i>
              </button>
          `;
      filePreview.appendChild(fileItem);
    });

    // Add remove file handlers
    filePreview.querySelectorAll('.remove-file').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const index = parseInt(e.currentTarget.dataset.index);
        this.selectedFiles.splice(index, 1);
        this.handleFileSelection(this.selectedFiles);
      });
    });

    // Update file count and show upload actions
    if (fileCountEl) {
      fileCountEl.textContent = this.selectedFiles.length;
    }

    if (uploadActions) {
      uploadActions.style.display = this.selectedFiles.length > 0 ? 'block' : 'none';
    }
  };

  // Clear file preview
  MediaManager.prototype.clearFilePreview = function () {
    if (this.uploadModal) {
      const filePreview = this.uploadModal.querySelector('.file-preview');
      const uploadActions = this.uploadModal.querySelector('.upload-actions');
      const fileInput = this.uploadModal.querySelector('.file-input');
      const fileCountEl = this.uploadModal.querySelector('#file-count');

      if (filePreview) {
        filePreview.innerHTML = '';
        filePreview.style.display = 'none';
      }
      if (uploadActions) {
        uploadActions.style.display = 'none';
      }
      if (fileInput) {
        fileInput.value = '';
      }
      if (fileCountEl) {
        fileCountEl.textContent = '0';
      }
      this.selectedFiles = [];
    }
  };

  // Format file size helper
  MediaManager.prototype.formatFileSize = function (bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  };

  /**
   * Check if file is an image
   */
  MediaManager.prototype.isImageFile = function (item) {
    // Check the explicit is_image flag first
    if (item.is_image !== undefined) {
      return item.is_image;
    }

    // Check file type
    if (item.type === 'image') {
      return true;
    }

    // Check mime type
    if (item.mime_type && item.mime_type.startsWith('image/')) {
      return true;
    }

    // Check file extension
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff', 'tif', 'ico'];
    const extension = item.extension ? item.extension.toLowerCase() : '';
    if (imageExtensions.includes(extension)) {
      return true;
    }

    // Check extension from filename
    const filename = item.name || '';
    const filenameExtension = filename.split('.').pop();
    if (filenameExtension && imageExtensions.includes(filenameExtension.toLowerCase())) {
      return true;
    }

    return false;
  };

  // Start upload process
  MediaManager.prototype.startUpload = function () {
    if (!this.selectedFiles || this.selectedFiles.length === 0) {
      Swal.fire('Error', 'Please select files to upload', 'error');
      return;
    }

    const formData = new FormData();
    formData.append('path', this.currentPath);

    // Determine endpoint based on number of files
    let endpoint;
    if (this.selectedFiles.length === 1) {
      formData.append('file', this.selectedFiles[0]);
      endpoint = this.options.endpoints.upload;
    } else {
      this.selectedFiles.forEach(file => {
        formData.append('files[]', file);
      });
      endpoint = this.options.endpoints.bulkUpload;
    }

    // Show loading state
    const startUploadBtn = this.uploadModal?.querySelector('.start-upload');
    if (startUploadBtn) {
      startUploadBtn.disabled = true;
      startUploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
    }

    // Perform upload
    fetch(endpoint, {
      method: 'POST',
      headers: this.getHeaders(false), // Don't include Content-Type for FormData
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: data.message || 'Files uploaded successfully',
            timer: 2000,
            showConfirmButton: false
          });

          // Clear and close
          this.hideUploadModal();
          this.clearFilePreview();

          // Reload the files
          this.loadFiles(this.currentPath);

          // Call callback if provided
          if (this.options.onUpload) {
            this.options.onUpload(data);
          }
        } else {
          throw new Error(data.message || 'Upload failed');
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Upload Failed',
          text: error.message || 'An error occurred during upload'
        });
        console.error('Upload error:', error);
      })
      .finally(() => {
        // Reset button state
        if (startUploadBtn) {
          startUploadBtn.disabled = false;
          startUploadBtn.innerHTML = '<i class="fas fa-cloud-upload-alt"></i> Start Upload';
        }
      });
  };
  MediaManager.prototype.createContextMenu = function () {
    const menu = document.createElement('div');
    menu.className = 'context-menu';
    menu.innerHTML = `
            <div class="context-menu-item" data-action="open">
                <i class="fas fa-folder-open"></i> ${this.options.texts.open}
            </div>
            <div class="context-menu-item" data-action="rename">
                <i class="fas fa-edit"></i> ${this.options.texts.rename}
            </div>
            <div class="context-menu-item context-menu-separator" data-action="move-to-folder">
                <i class="fas fa-cut"></i> Move to Folder
            </div>
            <div class="context-menu-item" data-action="copy-to-folder">
                <i class="fas fa-copy"></i> Copy to Folder
            </div>
            <div class="context-menu-item context-menu-separator bulk-context-item" data-action="bulk-move-selected" style="display: none;">
                <i class="fas fa-cut"></i> Move Selected Files
            </div>
            <div class="context-menu-item bulk-context-item" data-action="bulk-copy-selected" style="display: none;">
                <i class="fas fa-copy"></i> Copy Selected Files
            </div>
            <div class="context-menu-item context-menu-separator" data-action="copy-url">
                <i class="fas fa-link"></i> ${this.options.texts.copyUrl}
            </div>
            <div class="context-menu-item" data-action="download">
                <i class="fas fa-download"></i> ${this.options.texts.download}
            </div>
            <div class="context-menu-item context-menu-separator" data-action="delete">
                <i class="fas fa-trash"></i> ${this.options.texts.delete}
            </div>
        `;
    document.body.appendChild(menu);
    this.contextMenu = menu;
  };

  /**
   * Bind events
   */
  MediaManager.prototype.bindEvents = function () {
    const self = this;

    // Search
    const searchInput = this.container?.querySelector('.search-input');
    const searchBtn = this.container?.querySelector('.search-btn');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        this.search(e.target.value);
      });
    }

    if (searchBtn) {
      searchBtn.addEventListener('click', () => {
        this.search(searchInput.value);
      });
    }

    // View toggle
    const viewBtns = this.container?.querySelectorAll('.view-btn');
    viewBtns?.forEach(btn => {
      btn.addEventListener('click', () => {
        const view = btn.dataset.view;
        this.switchView(view);
      });
    });

    // Create folder
    const addFolderBtn = this.container?.querySelector('.btn-add-folder');
    if (addFolderBtn) {
      addFolderBtn.addEventListener('click', () => {
        this.createFolder();
      });
    }

    // Upload
    const uploadBtn = this.container?.querySelector('.btn-upload');
    if (uploadBtn) {
      uploadBtn.addEventListener('click', () => {
        this.showUploadModal();
      });
    }

    // Upload modal events are handled in bindUploadModalEvents() when modal is created
    // No need to duplicate them here

    // Bulk selection controls
    const selectAllBtn = this.container?.querySelector('.btn-select-all');
    const clearSelectionBtn = this.container?.querySelector('.btn-clear-selection');
    const bulkDeleteBtn = this.container?.querySelector('.btn-bulk-delete');
    const bulkMoveBtn = this.container?.querySelector('.btn-bulk-move');
    const bulkCopyBtn = this.container?.querySelector('.btn-bulk-copy');

    if (selectAllBtn) {
      selectAllBtn.addEventListener('click', () => {
        this.selectAll();
      });
    }

    if (clearSelectionBtn) {
      clearSelectionBtn.addEventListener('click', () => {
        this.clearSelection();
      });
    }

    if (bulkDeleteBtn) {
      bulkDeleteBtn.addEventListener('click', () => {
        this.bulkDelete();
      });
    }

    if (bulkMoveBtn) {
      bulkMoveBtn.addEventListener('click', () => {
        this.bulkMoveToFolder();
      });
    }

    if (bulkCopyBtn) {
      bulkCopyBtn.addEventListener('click', () => {
        this.bulkCopyToFolder();
      });
    }

    // Show selection mode button - toggle checkboxes visibility
    const toggleSelectionBtn = document.createElement('button');
    toggleSelectionBtn.className = 'btn-toggle-selection';
    toggleSelectionBtn.innerHTML = '<i class="fas fa-check-square"></i> Select Files';
    toggleSelectionBtn.title = 'Click to enable selection mode for bulk operations';
    const actionButtons = this.container?.querySelector('.action-buttons');
    if (actionButtons) {
      actionButtons.insertBefore(toggleSelectionBtn, actionButtons.firstChild);

      toggleSelectionBtn.addEventListener('click', () => {
        const isSelectionMode = this.toggleSelectionMode();
        if (isSelectionMode) {
          toggleSelectionBtn.innerHTML = '<i class="fas fa-times-circle"></i> Exit Selection';
          toggleSelectionBtn.style.background = '#dc3545';
        } else {
          toggleSelectionBtn.innerHTML = '<i class="fas fa-check-square"></i> Select Files';
          toggleSelectionBtn.style.background = '#6c757d';
        }
      });
    }

    // Context menu
    document.addEventListener('click', () => {
      if (this.contextMenu) {
        this.contextMenu.style.display = 'none';
      }
    });

    // Context menu actions
    this.contextMenu?.querySelectorAll('.context-menu-item').forEach(item => {
      item.addEventListener('click', (e) => {
        e.stopPropagation();
        const action = item.dataset.action;
        this.handleContextAction(action);
      });
    });
  };

  /**
   * Load files from server
   */
  MediaManager.prototype.loadFiles = function (path = '') {
    this.currentPath = path || '';

    fetch(this.options.endpoints.list + '?path=' + this.currentPath, {
      headers: this.getHeaders()
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.data = data.data;
          this.render();
          this.updateBreadcrumb();
          this.updateCounts();
        }
      })
      .catch(error => {
        this.handleError(error);
      });
  };

  /**
   * Render content
   */
  MediaManager.prototype.render = function () {
    const gridContainer = this.container?.querySelector('.media-grid');
    const listContainer = this.container?.querySelector('.media-list');

    if (!gridContainer || !listContainer) return;

    gridContainer.innerHTML = '';
    listContainer.innerHTML = '';

    // Render folders
    if (this.data.folders && this.data.folders.length > 0) {
      this.data.folders.forEach(folder => {
        // Grid view
        const gridItem = this.createGridItem(folder, 'folder');
        gridContainer.appendChild(gridItem);

        // List view
        const listItem = this.createListItem(folder, 'folder');
        listContainer.appendChild(listItem);
      });
    }

    // Render files
    if (this.data.files && this.data.files.length > 0) {
      this.data.files.forEach(file => {
        // Grid view
        const gridItem = this.createGridItem(file, 'file');
        gridContainer.appendChild(gridItem);

        // List view
        const listItem = this.createListItem(file, 'file');
        listContainer.appendChild(listItem);
      });
    }

    // Empty message
    if ((!this.data.folders || this.data.folders.length === 0) &&
      (!this.data.files || this.data.files.length === 0)) {
      gridContainer.innerHTML = `<div style="grid-column: 1/-1; text-align: center; padding: 40px;">
                <p>${this.options.texts.noFiles}</p>
            </div>`;
      listContainer.innerHTML = `<div style="text-align: center; padding: 40px;">
                <p>${this.options.texts.noFiles}</p>
            </div>`;
    }

    // Bind checkbox events after rendering
    this.bindCheckboxEvents();
  };

  /**
   * Create grid item
   */
  MediaManager.prototype.createGridItem = function (item, type) {
    const div = document.createElement('div');
    div.className = 'media-item';
    div.dataset.type = type;
    div.dataset.id = item.id;
    div.dataset.name = item.name;
    div.dataset.path = item.path;
    div.dataset.url = item.url;

    if (type === 'folder') {
      div.innerHTML = `
                <div class="item-checkbox" style="display: none;">
                    <input type="checkbox" class="select-item" data-type="folder" data-path="${item.path}" data-name="${item.name}">
                </div>
                <div class="media-thumbnail">
                    <i class="fas fa-folder folder-icon"></i>
                    <div class="media-name">${item.name}</div>
                </div>
            `;

      div.addEventListener('dblclick', () => {
        this.loadFiles(item.path);
      });
    } else {
      div.innerHTML = `
                <div class="item-checkbox" style="display: none;">
                    <input type="checkbox" class="select-item" data-type="file" data-id="${item.id}" data-name="${item.name}">
                </div>
                <div class="media-thumbnail">
                    ${this.isImageFile(item)
          ? `<img src="${item.url}" alt="${item.name}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">`
          : `<i class="fas fa-file file-icon"></i>`}
                    <div class="media-name">${item.name}</div>
                </div>
            `;

      div.addEventListener('click', () => {
        if (!this.options.multiple) {
          // Clear previous selections for single selection mode
          this.container?.querySelectorAll('.media-item.selected').forEach(selectedItem => {
            selectedItem.classList.remove('selected');
          });
          this.selectedFiles = [];
        }

        this.selectFile(item);
        div.classList.toggle('selected');

        // Trigger onSelect callback for modal integration
        if (this.options.onSelect && typeof this.options.onSelect === 'function') {
          this.options.onSelect(this.selectedFiles);
        }
      });
    }

    // Right click context menu
    div.addEventListener('contextmenu', (e) => {
      e.preventDefault();
      this.showContextMenu(e, item, type);
    });

    return div;
  };

  /**
   * Create list item
   */
  /**
   * Create list item
   */
  MediaManager.prototype.createListItem = function (item, type) {
    const div = document.createElement('div');
    div.className = 'media-list-item';
    div.dataset.type = type;
    div.dataset.id = item.id;
    div.dataset.name = item.name;
    div.dataset.path = item.path;
    div.dataset.url = item.url;

    div.innerHTML = `
            <div class="item-checkbox" style="display: none;">
                <input type="checkbox" class="select-item"
                    data-type="${type}"
                    data-id="${item.id || ''}"
                    data-path="${item.path || ''}"
                    data-name="${item.name}">
            </div>
            <div class="list-thumbnail">
                ${type === 'folder'
        ? `<i class="fas fa-folder" style="font-size: 24px;"></i>`
        : this.isImageFile(item)
          ? `<img src="${item.url}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">`
          : `<i class="fas fa-file" style="font-size: 24px;"></i>`}
            </div>
            <div class="list-details">
                <div class="list-name">${item.name}</div>
                <div class="list-meta">${item.size || 'Folder'} • ${item.modified || ''}</div>
            </div>
            <div class="list-actions">
                ${type === 'file' ? `
                    <button class="btn btn-sm btn-outline-info list-copy-btn">
                        <i class="fas fa-copy"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger list-delete-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                ` : ''}
            </div>
        `;

    // Attach event listeners for action buttons
    if (type === 'file') {
      const copyBtn = div.querySelector('.list-copy-btn');
      const deleteBtn = div.querySelector('.list-delete-btn');

      if (copyBtn) {
        copyBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          this.copyToClipboard(item.url);
        });
      }

      if (deleteBtn) {
        deleteBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          this.deleteItem(item, type);
        });
      }
    }

    if (type === 'folder') {
      div.addEventListener('dblclick', () => {
        this.loadFiles(item.path);
      });
    } else {
      div.addEventListener('click', () => {
        if (!this.options.multiple) {
          // Clear previous selections for single selection mode
          this.container?.querySelectorAll('.media-list-item.selected').forEach(selectedItem => {
            selectedItem.classList.remove('selected');
          });
          this.selectedFiles = [];
        }

        this.selectFile(item);
        div.classList.toggle('selected');

        // Trigger onSelect callback for modal integration
        if (this.options.onSelect && typeof this.options.onSelect === 'function') {
          this.options.onSelect(this.selectedFiles);
        }
      });
    }

    // Right click context menu
    div.addEventListener('contextmenu', (e) => {
      e.preventDefault();
      this.showContextMenu(e, item, type);
    });

    return div;
  };
  /**
   * Update breadcrumb
   */
  MediaManager.prototype.updateBreadcrumb = function () {
    const breadcrumb = this.container?.querySelector('.breadcrumb');
    if (!breadcrumb) return;

    breadcrumb.innerHTML = '';

    // Home
    const home = document.createElement('a');
    home.className = 'breadcrumb-item';
    home.innerHTML = '<i class="fas fa-home"></i> Home';
    home.addEventListener('click', (e) => {
      e.preventDefault();
      this.loadFiles('');
    });
    breadcrumb.appendChild(home);

    // Path segments
    if (this.currentPath) {
      const segments = this.currentPath.split('/');
      let path = '';

      segments.forEach(segment => {
        if (segment) {
          path += (path ? '/' : '') + segment;

          const separator = document.createElement('span');
          separator.className = 'breadcrumb-separator';
          separator.textContent = '/';
          breadcrumb.appendChild(separator);

          const link = document.createElement('a');
          link.className = 'breadcrumb-item';
          link.textContent = segment;
          const currentPath = path;
          link.addEventListener('click', (e) => {
            e.preventDefault();
            this.loadFiles(currentPath);
          });
          breadcrumb.appendChild(link);
        }
      });
    }
  };

  /**
   * Update counts
   */
  MediaManager.prototype.updateCounts = function () {
    const folderCount = this.container?.querySelector('.folder-count');
    const fileCount = this.container?.querySelector('.file-count');

    if (folderCount) {
      folderCount.textContent = this.data.folders ? this.data.folders.length : 0;
    }

    if (fileCount) {
      fileCount.textContent = this.data.files ? this.data.files.length : 0;
    }
  };

  /**
   * Switch view
   */
  MediaManager.prototype.switchView = function (view) {
    const container = this.container?.querySelector('.media-grid-container');
    const viewBtns = this.container?.querySelectorAll('.view-btn');

    if (view === 'list') {
      container?.classList.add('list-view');
    } else {
      container?.classList.remove('list-view');
    }

    viewBtns?.forEach(btn => {
      if (btn.dataset.view === view) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    this.currentView = view;
  };

  /**
   * Search
   */
  MediaManager.prototype.search = function (query) {
    const items = this.container?.querySelectorAll('.media-item, .media-list-item');

    items?.forEach(item => {
      const name = item.dataset.name?.toLowerCase() || '';
      if (name.includes(query.toLowerCase())) {
        item.style.display = '';
      } else {
        item.style.display = 'none';
      }
    });
  };

  /**
   * Create folder
   */
  MediaManager.prototype.createFolder = function () {
    const name = prompt('Enter folder name:');

    if (name) {
      fetch(this.options.endpoints.createFolder, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          name: name,
          path: this.currentPath
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.showSuccess('Folder created successfully');
            this.loadFiles(this.currentPath);
          } else {
            this.handleError(data.message);
          }
        })
        .catch(error => {
          this.handleError(error);
        });
    }
  };

  /**
   * Show upload modal
   */
  MediaManager.prototype.showUploadModal = function () {
    if (this.uploadModal) {
      this.uploadModal.classList.add('show');
    }
  };

  /**
   * Hide upload modal
   */
  MediaManager.prototype.hideUploadModal = function () {
    if (this.uploadModal) {
      this.uploadModal.classList.remove('show');
    }
  };

  /**
   * Handle files
   */
  MediaManager.prototype.handleFiles = function (files) {
    const formData = new FormData();
    formData.append('path', this.currentPath);

    if (files.length === 1) {
      formData.append('file', files[0]);
      this.uploadFile(formData, this.options.endpoints.upload);
    } else {
      for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
      }
      this.uploadFile(formData, this.options.endpoints.bulkUpload);
    }
  };

  /**
   * Upload file
   */
  MediaManager.prototype.uploadFile = function (formData, endpoint) {
    fetch(endpoint, {
      method: 'POST',
      headers: this.getHeaders(false),
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.showSuccess('Files uploaded successfully');
          this.hideUploadModal();
          this.loadFiles(this.currentPath);

          if (this.options.onUpload) {
            this.options.onUpload(data);
          }
        } else {
          this.handleError(data.message);
        }
      })
      .catch(error => {
        this.handleError(error);
      });
  };

  /**
   * Select file
   */
  MediaManager.prototype.selectFile = function (file) {
    const index = this.selectedFiles.findIndex(f => f.id === file.id);

    if (index > -1) {
      this.selectedFiles.splice(index, 1);
    } else {
      if (!this.options.multiple) {
        this.selectedFiles = [];
        this.container?.querySelectorAll('.media-item.selected').forEach(item => {
          item.classList.remove('selected');
        });
      }
      this.selectedFiles.push(file);
    }
  };

  /**
   * Show context menu
   */
  MediaManager.prototype.showContextMenu = function (e, item, type) {
    if (!this.contextMenu) return;

    this.currentContextItem = { item, type };

    // Hide/show menu items based on type
    const copyUrl = this.contextMenu.querySelector('[data-action="copy-url"]');
    const download = this.contextMenu.querySelector('[data-action="download"]');
    const moveToFolder = this.contextMenu.querySelector('[data-action="move-to-folder"]');
    const copyToFolder = this.contextMenu.querySelector('[data-action="copy-to-folder"]');
    const bulkMoveSelected = this.contextMenu.querySelector('[data-action="bulk-move-selected"]');
    const bulkCopySelected = this.contextMenu.querySelector('[data-action="bulk-copy-selected"]');

    // Check if we have multiple files selected
    const selectedFiles = this.selectedItems.filter(item => item.type === 'file');
    const hasMultipleFiles = selectedFiles.length > 1;

    if (type === 'folder') {
      if (copyUrl) copyUrl.style.display = 'none';
      if (download) download.style.display = 'none';
      if (moveToFolder) moveToFolder.style.display = 'none';
      if (copyToFolder) copyToFolder.style.display = 'none';
      if (bulkMoveSelected) bulkMoveSelected.style.display = 'none';
      if (bulkCopySelected) bulkCopySelected.style.display = 'none';
    } else {
      if (copyUrl) copyUrl.style.display = '';
      if (download) download.style.display = '';
      if (moveToFolder) moveToFolder.style.display = '';
      if (copyToFolder) copyToFolder.style.display = '';
      
      // Show bulk options only if multiple files are selected
      if (bulkMoveSelected) {
        bulkMoveSelected.style.display = hasMultipleFiles ? '' : 'none';
        if (hasMultipleFiles) {
          const moveText = bulkMoveSelected.querySelector('i').nextSibling;
          moveText.textContent = ` Move ${selectedFiles.length} Selected Files`;
        }
      }
      
      if (bulkCopySelected) {
        bulkCopySelected.style.display = hasMultipleFiles ? '' : 'none';
        if (hasMultipleFiles) {
          const copyText = bulkCopySelected.querySelector('i').nextSibling;
          copyText.textContent = ` Copy ${selectedFiles.length} Selected Files`;
        }
      }
    }

    this.contextMenu.style.left = e.pageX + 'px';
    this.contextMenu.style.top = e.pageY + 'px';
    this.contextMenu.style.display = 'block';
  };

  /**
   * Handle context action
   */
  MediaManager.prototype.handleContextAction = function (action) {
    if (!this.currentContextItem) return;

    const { item, type } = this.currentContextItem;

    switch (action) {
      case 'open':
        if (type === 'folder') {
          this.loadFiles(item.path);
        } else {
          window.open(item.url, '_blank');
        }
        break;

      case 'rename':
        this.renameItem(item, type);
        break;

      case 'copy-url':
        this.copyToClipboard(item.url);
        break;

      case 'download':
        window.location.href = item.url;
        break;

      case 'delete':
        this.deleteItem(item, type);
        break;

      case 'move-to-folder':
        this.showFolderSelectionModal(item, 'move');
        break;

      case 'copy-to-folder':
        this.showFolderSelectionModal(item, 'copy');
        break;

      case 'bulk-move-selected':
        this.bulkMoveToFolder();
        break;

      case 'bulk-copy-selected':
        this.bulkCopyToFolder();
        break;
    }

    this.contextMenu.style.display = 'none';
  };

  /**
   * Rename item
   */
  MediaManager.prototype.renameItem = function (item, type) {
    const newName = prompt('Enter new name:', item.name);

    if (newName && newName !== item.name) {
      const endpoint = type === 'folder'
        ? this.options.endpoints.renameFolder
        : this.options.endpoints.renameFile;

      fetch(endpoint, {
        method: 'POST',
        headers: this.getHeaders(),
        body: JSON.stringify({
          id: item.id,
          path: item.path,
          new_name: newName
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            this.showSuccess('Renamed successfully');
            this.loadFiles(this.currentPath);
          } else {
            this.handleError(data.message);
          }
        })
        .catch(error => {
          this.handleError(error);
        });
    }
  };

  /**
   * Delete item with SweetAlert confirmation
   */
  MediaManager.prototype.deleteItem = function (item, type) {
    // Configure SweetAlert confirmation dialog
    Swal.fire({
      title: 'Are you sure?',
      text: `Do you want to delete ${type} "${item.name}"? This action cannot be undone!`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!',
      cancelButtonText: 'Cancel',
      reverseButtons: true,
      focusCancel: true,
      customClass: {
        popup: 'swal-delete-popup',
        title: 'swal-delete-title',
        confirmButton: 'swal-delete-confirm',
        cancelButton: 'swal-delete-cancel'
      },
      showLoaderOnConfirm: true,
      preConfirm: () => {
        // Show loading state
        Swal.showLoading();

        const endpoint = type === 'folder'
          ? this.options.endpoints.deleteFolder
          : this.options.endpoints.deleteFile.replace('{id}', item.id);

        // Return the fetch promise for SweetAlert to handle
        return fetch(endpoint, {
          method: 'DELETE',
          headers: this.getHeaders(),
          body: type === 'folder' ? JSON.stringify({ path: item.path }) : null
        })
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            if (!data.success) {
              throw new Error(data.message || 'Delete operation failed');
            }
            return data;
          })
          .catch(error => {
            Swal.showValidationMessage(`Request failed: ${error.message}`);
            throw error;
          });
      },
      allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
      if (result.isConfirmed) {
        // Success - item was deleted
        Swal.fire({
          title: 'Deleted!',
          text: `${type === 'folder' ? 'Folder' : 'File'} "${item.name}" has been deleted.`,
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });

        // Reload the file list
        this.loadFiles(this.currentPath);
      }
    }).catch((error) => {
      // Handle any errors that weren't caught in preConfirm
      if (error && error !== 'cancel') {
        Swal.fire({
          title: 'Error!',
          text: error.message || 'An error occurred while deleting the item',
          icon: 'error',
          confirmButtonColor: '#3085d6'
        });
      }
    });
  };

  /**
   * Copy to clipboard
   */
  MediaManager.prototype.copyToClipboard = function (text) {
    const input = document.createElement('input');
    document.body.appendChild(input);
    input.value = text;
    input.select();
    document.execCommand('copy');
    document.body.removeChild(input);

    this.showSuccess('URL copied to clipboard');
  };

  /**
   * Get headers
   */
  MediaManager.prototype.getHeaders = function (includeContentType = true) {
    const headers = Object.assign({}, this.options.headers);

    if (this.options.csrfToken) {
      headers['X-CSRF-TOKEN'] = this.options.csrfToken;
    }

    if (includeContentType) {
      headers['Content-Type'] = 'application/json';
    }

    return headers;
  };

  /**
   * Show success message
   */
  MediaManager.prototype.showSuccess = function (message) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        timer: 2000,
        showConfirmButton: false
      });
    } else {
      alert(message);
    }
  };

  /**
   * Handle error
   */
  MediaManager.prototype.handleError = function (error) {
    console.error('MediaManager Error:', error);

    if (this.options.onError) {
      this.options.onError(error);
    }

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: error.message || error
      });
    } else {
      alert('Error: ' + (error.message || error));
    }
  };

  /**
   * Open modal (for modal mode)
   */
  MediaManager.prototype.open = function (callback) {
    if (callback) {
      this.options.onSelect = callback;
    }

    if (!this.container) {
      this.init();
    }

    if (this.modal) {
      this.modal.classList.add('show');
    }
  };

  /**
   * Toggle selection mode - show/hide checkboxes
   */
  MediaManager.prototype.toggleSelectionMode = function () {
    const checkboxes = this.container?.querySelectorAll('.item-checkbox');
    const selectAllBtn = this.container?.querySelector('.btn-select-all');
    const clearBtn = this.container?.querySelector('.btn-clear-selection');
    const bulkDeleteBtn = this.container?.querySelector('.btn-bulk-delete');
    const bulkMoveBtn = this.container?.querySelector('.btn-bulk-move');
    const bulkCopyBtn = this.container?.querySelector('.btn-bulk-copy');

    let isSelectionMode = false;

    if (checkboxes && checkboxes.length > 0) {
      // Check if we're entering or exiting selection mode
      isSelectionMode = checkboxes[0].style.display === 'none' || !checkboxes[0].style.display;

      checkboxes.forEach(cb => {
        cb.style.display = isSelectionMode ? 'block' : 'none';
      });

      // Toggle bulk action buttons
      if (selectAllBtn) {
        selectAllBtn.style.display = isSelectionMode ? 'inline-block' : 'none';
      }
      if (clearBtn) {
        clearBtn.style.display = isSelectionMode ? 'inline-block' : 'none';
      }
      if (bulkDeleteBtn) {
        bulkDeleteBtn.style.display = isSelectionMode ? 'inline-block' : 'none';
      }
      if (bulkMoveBtn) {
        bulkMoveBtn.style.display = isSelectionMode ? 'inline-block' : 'none';
      }
      if (bulkCopyBtn) {
        bulkCopyBtn.style.display = isSelectionMode ? 'inline-block' : 'none';
      }

      // Clear previous selections when exiting selection mode
      if (!isSelectionMode) {
        this.clearSelection();
      }
    }

    // Add checkbox change listeners
    this.bindCheckboxEvents();

    return isSelectionMode;
  };

  /**
   * Bind checkbox events
   */
  MediaManager.prototype.bindCheckboxEvents = function () {
    const checkboxes = this.container?.querySelectorAll('.select-item');

    checkboxes?.forEach(checkbox => {
      checkbox.removeEventListener('change', this.handleCheckboxChange);
      checkbox.addEventListener('change', (e) => this.handleCheckboxChange(e));
    });
  };

  /**
   * Handle checkbox change
   */
  MediaManager.prototype.handleCheckboxChange = function (e) {
    const checkbox = e.target;
    const type = checkbox.dataset.type;
    const item = {
      type: type,
      id: checkbox.dataset.id,
      path: checkbox.dataset.path,
      name: checkbox.dataset.name
    };

    if (checkbox.checked) {
      // Check if item already exists to prevent duplicates
      const existingIndex = this.selectedItems.findIndex(i => 
        i.type === item.type && (
          (i.id && item.id && i.id === item.id) || 
          (i.path && item.path && i.path === item.path)
        )
      );
      
      if (existingIndex === -1) {
        this.selectedItems.push(item);
      }
    } else {
      this.selectedItems = this.selectedItems.filter(i =>
        !(i.type === item.type && (
          (i.id && item.id && i.id === item.id) || 
          (i.path && item.path && i.path === item.path)
        ))
      );
    }

    this.updateSelectionCount();
  };

  /**
   * Update selection count display
   */
  MediaManager.prototype.updateSelectionCount = function () {
    const countSpans = this.container?.querySelectorAll('.selected-count');
    if (countSpans) {
      countSpans.forEach(span => {
        span.textContent = this.selectedItems.length;
      });
    }

    // Filter out folders for move/copy operations (only show file counts)
    const fileCount = this.selectedItems.filter(item => item.type === 'file').length;
    const bulkMoveBtn = this.container?.querySelector('.btn-bulk-move');
    const bulkCopyBtn = this.container?.querySelector('.btn-bulk-copy');

    // Disable move/copy buttons if no files selected (folders can't be moved/copied yet)
    if (bulkMoveBtn) {
      bulkMoveBtn.disabled = fileCount === 0;
      bulkMoveBtn.style.opacity = fileCount === 0 ? '0.5' : '1';
    }

    if (bulkCopyBtn) {
      bulkCopyBtn.disabled = fileCount === 0;
      bulkCopyBtn.style.opacity = fileCount === 0 ? '0.5' : '1';
    }
  };

  /**
   * Select all items
   */
  MediaManager.prototype.selectAll = function () {
    const checkboxes = this.container?.querySelectorAll('.select-item');
    this.selectedItems = [];

    checkboxes?.forEach(checkbox => {
      checkbox.checked = true;
      const item = {
        type: checkbox.dataset.type,
        id: checkbox.dataset.id,
        path: checkbox.dataset.path,
        name: checkbox.dataset.name
      };
      this.selectedItems.push(item);
    });

    this.updateSelectionCount();
  };

  /**
   * Clear selection
   */
  MediaManager.prototype.clearSelection = function () {
    const checkboxes = this.container?.querySelectorAll('.select-item');

    checkboxes?.forEach(checkbox => {
      checkbox.checked = false;
    });

    this.selectedItems = [];
    this.updateSelectionCount();
  };

  /**
   * Bulk delete selected items
   */
  MediaManager.prototype.bulkDelete = function () {
    if (this.selectedItems.length === 0) {
      Swal.fire('Info', 'Please select items to delete', 'info');
      return;
    }

    const fileCount = this.selectedItems.filter(i => i.type === 'file').length;
    const folderCount = this.selectedItems.filter(i => i.type === 'folder').length;

    let message = `Are you sure you want to delete `;
    if (fileCount > 0 && folderCount > 0) {
      message += `${fileCount} file(s) and ${folderCount} folder(s)?`;
    } else if (fileCount > 0) {
      message += `${fileCount} file(s)?`;
    } else {
      message += `${folderCount} folder(s)?`;
    }

    Swal.fire({
      title: 'Confirm Bulk Delete',
      text: message + ' This action cannot be undone!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete them!',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        this.performBulkDelete();
      }
    });
  };

  /**
   * Perform bulk delete
   */
  MediaManager.prototype.performBulkDelete = function () {
    const promises = [];

    // Show loading
    Swal.fire({
      title: 'Deleting...',
      text: 'Please wait while we delete the selected items',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Delete each item
    this.selectedItems.forEach(item => {
      if (item.type === 'file') {
        const endpoint = this.options.endpoints.deleteFile.replace('{id}', item.id);
        promises.push(
          fetch(endpoint, {
            method: 'DELETE',
            headers: this.getHeaders()
          })
        );
      } else if (item.type === 'folder') {
        promises.push(
          fetch(this.options.endpoints.deleteFolder, {
            method: 'DELETE',
            headers: this.getHeaders(),
            body: JSON.stringify({ path: item.path })
          })
        );
      }
    });

    Promise.allSettled(promises)
      .then(results => {
        const successful = results.filter(r => r.status === 'fulfilled').length;
        const failed = results.filter(r => r.status === 'rejected').length;

        if (failed === 0) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: `Successfully deleted ${successful} item(s)`,
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Partial Success',
            text: `Deleted ${successful} item(s), ${failed} failed`
          });
        }

        // Clear selection and reload
        this.clearSelection();
        this.loadFiles(this.currentPath);
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred during deletion'
        });
        console.error('Bulk delete error:', error);
      });
  };

  /**
   * Bulk move selected files to folder
   */
  MediaManager.prototype.bulkMoveToFolder = function () {
    const selectedFiles = this.selectedItems.filter(item => item.type === 'file');

    if (selectedFiles.length === 0) {
      Swal.fire('Info', 'Please select files to move', 'info');
      return;
    }

    this.showBulkFolderSelectionModal(selectedFiles, 'move');
  };

  /**
   * Bulk copy selected files to folder
   */
  MediaManager.prototype.bulkCopyToFolder = function () {
    const selectedFiles = this.selectedItems.filter(item => item.type === 'file');

    if (selectedFiles.length === 0) {
      Swal.fire('Info', 'Please select files to copy', 'info');
      return;
    }

    this.showBulkFolderSelectionModal(selectedFiles, 'copy');
  };

  /**
   * Show folder selection modal for bulk operations
   */
  MediaManager.prototype.showBulkFolderSelectionModal = function (selectedFiles, action) {
    this.currentBulkFolderAction = { items: selectedFiles, action };

    // Load folders and show modal
    this.loadFoldersForBulkSelection(() => {
      this.createBulkFolderSelectionModal();
      const modal = document.getElementById('bulkFolderSelectionModal');
      if (modal) {
        modal.style.display = 'flex';
      }
    });
  };

  /**
   * Load folders for bulk selection
   */
  MediaManager.prototype.loadFoldersForBulkSelection = function (callback) {
    fetch(this.options.endpoints.getFolders, {
      headers: this.getHeaders()
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.availableFolders = data.folders || [];
          if (callback) callback();
        } else {
          this.handleError(data.message || 'Failed to load folders');
        }
      })
      .catch(error => {
        this.handleError(error);
      });
  };

  /**
   * Create bulk folder selection modal
   */
  MediaManager.prototype.createBulkFolderSelectionModal = function () {
    // Remove existing modal if any
    const existingModal = document.getElementById('bulkFolderSelectionModal');
    if (existingModal) {
      existingModal.remove();
    }

    const action = this.currentBulkFolderAction.action;
    const actionText = action === 'move' ? 'Move' : 'Copy';
    const actionIcon = action === 'move' ? 'cut' : 'copy';
    const fileCount = this.currentBulkFolderAction.items.length;

    const modal = document.createElement('div');
    modal.id = 'bulkFolderSelectionModal';
    modal.className = 'folder-selection-modal';
    modal.innerHTML = `
      <div class="folder-selection-modal-content">
        <div class="folder-selection-header">
          <h4><i class="fas fa-${actionIcon}"></i> ${actionText} Multiple Files</h4>
          <button class="close-bulk-folder-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="folder-selection-body">
          <p><strong>${actionText}</strong> ${fileCount} selected file(s) to:</p>
          <div class="selected-files-preview">
            ${this.renderSelectedFilesPreview()}
          </div>
          <div class="folder-list" id="bulkFolderSelectionList">
            ${this.renderFolderList()}
          </div>
        </div>
        <div class="folder-selection-footer">
          <button class="btn btn-secondary cancel-bulk-folder-selection">Cancel</button>
          <button class="btn btn-primary confirm-bulk-folder-selection" disabled>
            <i class="fas fa-${actionIcon}"></i> ${actionText} ${fileCount} File(s) Here
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    this.bindBulkFolderSelectionEvents();
  };

  /**
   * Render selected files preview
   */
  MediaManager.prototype.renderSelectedFilesPreview = function () {
    if (!this.currentBulkFolderAction || !this.currentBulkFolderAction.items) {
      return '<p class="text-muted">No files selected</p>';
    }

    // Remove duplicates by using a Map with file ID as key
    const uniqueFiles = [];
    const seenIds = new Set();
    
    this.currentBulkFolderAction.items.forEach(file => {
      if (file.id && !seenIds.has(file.id)) {
        seenIds.add(file.id);
        uniqueFiles.push(file);
      } else if (!file.id && file.name) {
        // For items without ID, use name as fallback
        const key = `${file.name}_${file.path || ''}`;
        if (!seenIds.has(key)) {
          seenIds.add(key);
          uniqueFiles.push(file);
        }
      }
    });

    const files = uniqueFiles.slice(0, 5); // Show first 5 unique files
    const remaining = uniqueFiles.length - files.length;

    let html = '<div class="files-preview-list">';
    files.forEach(file => {
      html += `
        <div class="file-preview-item">
          <i class="fas fa-file"></i>
          <span class="file-name">${file.name}</span>
        </div>
      `;
    });

    if (remaining > 0) {
      html += `
        <div class="file-preview-item">
          <i class="fas fa-ellipsis-h"></i>
          <span class="file-name">and ${remaining} more...</span>
        </div>
      `;
    }

    html += '</div>';
    return html;
  };

  /**
   * Bind bulk folder selection modal events
   */
  MediaManager.prototype.bindBulkFolderSelectionEvents = function () {
    const modal = document.getElementById('bulkFolderSelectionModal');
    if (!modal) return;

    const closeBtn = modal.querySelector('.close-bulk-folder-modal');
    const cancelBtn = modal.querySelector('.cancel-bulk-folder-selection');
    const confirmBtn = modal.querySelector('.confirm-bulk-folder-selection');
    const folderOptions = modal.querySelectorAll('.folder-option');

    // Close modal events
    [closeBtn, cancelBtn].forEach(btn => {
      if (btn) {
        btn.addEventListener('click', () => {
          this.closeBulkFolderSelectionModal();
        });
      }
    });

    // Click outside to close
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        this.closeBulkFolderSelectionModal();
      }
    });

    // Folder selection
    folderOptions.forEach(option => {
      option.addEventListener('click', () => {
        // Remove previous selection
        folderOptions.forEach(opt => opt.classList.remove('selected'));

        // Select current option
        option.classList.add('selected');

        // Enable confirm button
        confirmBtn.disabled = false;

        // Store selected path
        this.selectedDestinationFolder = option.dataset.path;
      });
    });

    // Confirm action
    if (confirmBtn) {
      confirmBtn.addEventListener('click', () => {
        this.performBulkFolderAction();
      });
    }
  };

  /**
   * Perform bulk folder action
   */
  MediaManager.prototype.performBulkFolderAction = function () {
    if (!this.currentBulkFolderAction || this.selectedDestinationFolder === undefined) {
      return;
    }

    const { items, action } = this.currentBulkFolderAction;
    const endpoint = action === 'move'
      ? this.options.endpoints.bulkMoveToFolder
      : this.options.endpoints.bulkCopyToFolder;

    // Remove duplicates and get unique file IDs
    const uniqueFileIds = [...new Set(items.map(item => item.id).filter(id => id))];

    if (uniqueFileIds.length === 0) {
      Swal.fire('Error', 'No valid files selected for this operation', 'error');
      return;
    }

    // Show loading
    Swal.fire({
      title: action === 'move' ? 'Moving Files...' : 'Copying Files...',
      text: `Please wait while we ${action} ${uniqueFileIds.length} file(s)`,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    fetch(endpoint, {
      method: 'POST',
      headers: this.getHeaders(),
      body: JSON.stringify({
        file_ids: uniqueFileIds,
        destination_folder: this.selectedDestinationFolder || null
      })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const folderName = this.selectedDestinationFolder || 'Root';

          let message = data.message;
          if (data.data && data.data.failed_count > 0) {
            message += `\n\nFailed files:\n`;
            data.data.failed_files.forEach(file => {
              message += `• ${file.name}: ${file.error}\n`;
            });
          }

          Swal.fire({
            icon: data.data && data.data.failed_count > 0 ? 'warning' : 'success',
            title: data.data && data.data.failed_count > 0 ? 'Partially Completed' : 'Success!',
            text: message,
            timer: data.data && data.data.failed_count > 0 ? 5000 : 2000,
            showConfirmButton: data.data && data.data.failed_count > 0
          });

          // Close modal and refresh
          this.closeBulkFolderSelectionModal();
          this.clearSelection();
          this.loadFiles(this.currentPath);
        } else {
          throw new Error(data.message || `Bulk ${action} operation failed`);
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || `Failed to ${action} files`
        });
      });
  };

  /**
   * Close bulk folder selection modal
   */
  MediaManager.prototype.closeBulkFolderSelectionModal = function () {
    const modal = document.getElementById('bulkFolderSelectionModal');
    if (modal) {
      modal.remove();
    }

    // Reset state
    this.currentBulkFolderAction = null;
    this.selectedDestinationFolder = undefined;
    this.availableFolders = [];
  };

  /**
   * Show folder selection modal for move/copy operations
   */
  MediaManager.prototype.showFolderSelectionModal = function (item, action) {
    this.currentFolderAction = { item, action };

    // Load folders and show modal
    this.loadFoldersForSelection(() => {
      this.createFolderSelectionModal();
      const modal = document.getElementById('folderSelectionModal');
      if (modal) {
        modal.style.display = 'flex';
      }
    });
  };

  /**
   * Load folders for selection
   */
  MediaManager.prototype.loadFoldersForSelection = function (callback) {
    const excludePath = this.currentFolderAction.item.folder_path || '';

    fetch(this.options.endpoints.getFolders + '?exclude_path=' + encodeURIComponent(excludePath), {
      headers: this.getHeaders()
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          this.availableFolders = data.folders || [];
          if (callback) callback();
        } else {
          this.handleError(data.message || 'Failed to load folders');
        }
      })
      .catch(error => {
        this.handleError(error);
      });
  };

  /**
   * Create folder selection modal
   */
  MediaManager.prototype.createFolderSelectionModal = function () {
    // Remove existing modal if any
    const existingModal = document.getElementById('folderSelectionModal');
    if (existingModal) {
      existingModal.remove();
    }

    const action = this.currentFolderAction.action;
    const actionText = action === 'move' ? 'Move' : 'Copy';
    const actionIcon = action === 'move' ? 'cut' : 'copy';
    const fileName = this.currentFolderAction.item.name;

    const modal = document.createElement('div');
    modal.id = 'folderSelectionModal';
    modal.className = 'folder-selection-modal';
    modal.innerHTML = `
      <div class="folder-selection-modal-content">
        <div class="folder-selection-header">
          <h4><i class="fas fa-${actionIcon}"></i> ${actionText} File</h4>
          <button class="close-folder-modal">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="folder-selection-body">
          <p><strong>${actionText}</strong> "${fileName}" to:</p>
          <div class="folder-list" id="folderSelectionList">
            ${this.renderFolderList()}
          </div>
        </div>
        <div class="folder-selection-footer">
          <button class="btn btn-secondary cancel-folder-selection">Cancel</button>
          <button class="btn btn-primary confirm-folder-selection" disabled>
            <i class="fas fa-${actionIcon}"></i> ${actionText} Here
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
    this.bindFolderSelectionEvents();
  };

  /**
   * Render folder list for selection
   */
  MediaManager.prototype.renderFolderList = function () {
    if (!this.availableFolders || this.availableFolders.length === 0) {
      return '<p class="text-muted">No folders available</p>';
    }

    return this.availableFolders.map(folder => {
      const indent = '&nbsp;'.repeat(folder.level * 4);
      const icon = folder.level === 0 ? 'fa-home' : 'fa-folder';

      return `
        <div class="folder-option" data-path="${folder.path}">
          <i class="fas ${icon}"></i>
          ${indent}${folder.name}
        </div>
      `;
    }).join('');
  };

  /**
   * Bind folder selection modal events
   */
  MediaManager.prototype.bindFolderSelectionEvents = function () {
    const modal = document.getElementById('folderSelectionModal');
    if (!modal) return;

    const closeBtn = modal.querySelector('.close-folder-modal');
    const cancelBtn = modal.querySelector('.cancel-folder-selection');
    const confirmBtn = modal.querySelector('.confirm-folder-selection');
    const folderOptions = modal.querySelectorAll('.folder-option');

    // Close modal events
    [closeBtn, cancelBtn].forEach(btn => {
      if (btn) {
        btn.addEventListener('click', () => {
          this.closeFolderSelectionModal();
        });
      }
    });

    // Click outside to close
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        this.closeFolderSelectionModal();
      }
    });

    // Folder selection
    folderOptions.forEach(option => {
      option.addEventListener('click', () => {
        // Remove previous selection
        folderOptions.forEach(opt => opt.classList.remove('selected'));

        // Select current option
        option.classList.add('selected');

        // Enable confirm button
        confirmBtn.disabled = false;

        // Store selected path
        this.selectedDestinationFolder = option.dataset.path;
      });
    });

    // Confirm action
    if (confirmBtn) {
      confirmBtn.addEventListener('click', () => {
        this.performFolderAction();
      });
    }
  };

  /**
   * Perform the selected folder action (move or copy)
   */
  MediaManager.prototype.performFolderAction = function () {
    if (!this.currentFolderAction || this.selectedDestinationFolder === undefined) {
      return;
    }

    const { item, action } = this.currentFolderAction;
    const endpoint = action === 'move'
      ? this.options.endpoints.moveToFolder
      : this.options.endpoints.copyToFolder;

    // Show loading
    Swal.fire({
      title: action === 'move' ? 'Moving...' : 'Copying...',
      text: 'Please wait while we process your request',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    fetch(endpoint, {
      method: 'POST',
      headers: this.getHeaders(),
      body: JSON.stringify({
        file_id: item.id,
        destination_folder: this.selectedDestinationFolder || null
      })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const actionText = action === 'move' ? 'moved' : 'copied';
          const folderName = this.selectedDestinationFolder || 'Root';

          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: `File ${actionText} to "${folderName}" successfully`,
            timer: 2000,
            showConfirmButton: false
          });

          // Close modal and refresh
          this.closeFolderSelectionModal();
          this.loadFiles(this.currentPath);
        } else {
          throw new Error(data.message || `${action} operation failed`);
        }
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.message || `Failed to ${action} file`
        });
      });
  };

  /**
   * Close folder selection modal
   */
  MediaManager.prototype.closeFolderSelectionModal = function () {
    const modal = document.getElementById('folderSelectionModal');
    if (modal) {
      modal.remove();
    }

    // Reset state
    this.currentFolderAction = null;
    this.selectedDestinationFolder = undefined;
    this.availableFolders = [];
  };

  /**
   * jQuery Plugin Integration
   */
  if (typeof jQuery !== 'undefined') {
    jQuery.fn.mediaManager = function (options) {
      return this.each(function () {
        const $element = jQuery(this);
        const manager = new MediaManager(options);

        $element.on('click', function (e) {
          e.preventDefault();

          manager.open(function (files) {
            // For input fields
            if ($element.is('input')) {
              if (files.length > 0) {
                const urls = files.map(f => f.url).join(',');
                $element.val(urls);
                $element.trigger('change');
              }
            }

            // Trigger custom event
            $element.trigger('mediaSelected', [files]);
          });
        });

        // Store instance on element
        $element.data('mediaManager', manager);
      });
    };
  }

  // Export
  window.MediaManager = MediaManager;

})(window, document);
