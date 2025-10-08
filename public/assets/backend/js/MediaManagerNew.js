/**
 * MediaManager.js - A comprehensive media management library for Laravel
 * @version 1.0.0
 * @author Your Name
 * @license MIT
 *
 * Features:
 * - File upload with drag & drop
 * - Folder navigation and management
 * - Multiple view modes (grid/list)
 * - File preview and download
 * - Search functionality
 * - Bulk operations
 * - Context menus
 * - Media picker modal
 * - Customizable configuration
 */

(function(window, document) {
  'use strict';

  /**
   * MediaManager Constructor
   * @param {Object} config - Configuration object
   */
  class MediaManager {
    constructor(config = {}) {
      // Default configuration
      this.config = {
        baseUrl: config.baseUrl || '/admin/media',
        csrfToken: config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,
        container: config.container || '#mediaManager',
        viewMode: config.viewMode || 'grid',
        allowMultiSelect: config.allowMultiSelect !== false,
        allowUpload: config.allowUpload !== false,
        allowFolders: config.allowFolders !== false,
        allowDelete: config.allowDelete !== false,
        allowRename: config.allowRename !== false,
        maxFileSize: config.maxFileSize || 52428800, // 50MB default
        acceptedFiles: config.acceptedFiles || '*',
        onSelect: config.onSelect || null,
        onUpload: config.onUpload || null,
        onDelete: config.onDelete || null,
        onError: config.onError || null,
        language: config.language || 'en',
        debug: config.debug !== false // Enable debug by default
      };

      // State management
      this.state = {
        currentFolderId: null,
        currentView: this.config.viewMode,
        selectedItems: new Set(),
        searchQuery: '',
        isLoading: false,
        breadcrumb: [],
        folders: [],
        files: [],
        dragCounter: 0
      };

      // UI Elements cache
      this.elements = {};

      // Initialize
      this.init();
    }

    /**
     * Initialize the media manager
     */
    init() {
      this.log('Initializing MediaManager...');

      // Check if container exists
      const container = document.querySelector(this.config.container);
      if (!container) {
        this.error('Container element not found:', this.config.container);
        return;
      }

      this.log('Container found:', container);

      // Render UI
      this.renderUI(container);

      // Cache UI elements
      this.cacheElements();

      // Setup event listeners
      this.setupEventListeners();

      // Add modal styles
      this.addModalStyles();

      // Load initial content
      this.log('Loading initial content...');
      this.loadContents();
    }

    /**
     * Render the main UI structure
     */
    renderUI(container) {
      container.innerHTML = this.getTemplate();
    }

    /**
     * Get the main HTML template
     */
    getTemplate() {
      return `
        <div class="media-manager-wrapper">
          <!-- Control Bar -->
          <div class="mm-control-bar">
            <div class="mm-search-section">
              <input type="text" class="mm-search-input" placeholder="Search files and folders..." />
              <button class="mm-btn mm-btn-search">
                <i class="mm-icon mm-icon-search"></i>
              </button>
              <span class="mm-file-count">
                <span class="mm-count-text">0 items</span>
              </span>
            </div>
            <div class="mm-control-buttons">
              <div class="mm-bulk-actions" style="display: none;">
                <button class="mm-btn mm-btn-select-all" title="Select All">
                  <i class="mm-icon mm-icon-check-all"></i>
                </button>
                <button class="mm-btn mm-btn-clear-selection" title="Clear Selection">
                  <i class="mm-icon mm-icon-clear"></i>
                </button>
                <button class="mm-btn mm-btn-bulk-delete mm-btn-danger" title="Delete Selected">
                  <i class="mm-icon mm-icon-delete"></i> Delete (<span class="mm-selected-count">0</span>)
                </button>
              </div>
              <div class="mm-view-toggle">
                <button class="mm-btn mm-view-grid ${this.config.viewMode === 'grid' ? 'active' : ''}" data-view="grid" title="Grid View">
                  <i class="mm-icon mm-icon-grid"></i>
                </button>
                <button class="mm-btn mm-view-list ${this.config.viewMode === 'list' ? 'active' : ''}" data-view="list" title="List View">
                  <i class="mm-icon mm-icon-list"></i>
                </button>
              </div>
              ${this.config.allowFolders ? `
              <button class="mm-btn mm-btn-primary mm-btn-new-folder">
                <i class="mm-icon mm-icon-folder-plus"></i> New Folder
              </button>
              ` : ''}
              ${this.config.allowUpload ? `
              <button class="mm-btn mm-btn-success mm-btn-upload">
                <i class="mm-icon mm-icon-upload"></i> Upload
              </button>
              ` : ''}
            </div>
          </div>

          <!-- Breadcrumb -->
          <nav class="mm-breadcrumb">
            <a href="#" class="mm-breadcrumb-item" data-folder-id="">
              <i class="mm-icon mm-icon-home"></i> Home
            </a>
          </nav>

          <!-- Content Area -->
          <div class="mm-content-area">
            <!-- Upload Zone (Hidden by default) -->
            <div class="mm-upload-zone" style="display: none;">
              <div class="mm-upload-dropzone">
                <div class="mm-upload-icon">
                  <i class="mm-icon mm-icon-cloud-upload-alt"></i>
                </div>
                <div class="mm-upload-text">
                  <h3>Click to choose from gallery</h3>
                  <p>PNG, JPG, GIF up to 5MB</p>
                </div>
                <input type="file" class="mm-file-input" multiple style="display: none;" />
              </div>
              <div class="mm-upload-progress" style="display: none;">
                <div class="mm-progress-bar">
                  <div class="mm-progress-fill"></div>
                </div>
                <div class="mm-upload-status"></div>
              </div>
              <div class="mm-upload-list"></div>
            </div>

            <!-- Loading State -->
            <div class="mm-loading" style="display: none;">
              <div class="mm-spinner"></div>
              <p>Loading...</p>
            </div>

            <!-- Grid View -->
            <div class="mm-view mm-grid-view" style="${this.config.viewMode !== 'grid' ? 'display: none;' : ''}">
              <div class="mm-grid"></div>
            </div>

            <!-- List View -->
            <div class="mm-view mm-list-view" style="${this.config.viewMode !== 'list' ? 'display: none;' : ''}">
              <table class="mm-table">
                <thead>
                  <tr>
                    <th width="40">
                      <input type="checkbox" class="mm-select-all-checkbox" />
                    </th>
                    <th width="50">Type</th>
                    <th>Name</th>
                    <th width="120">Modified</th>
                    <th width="100">Size</th>
                    <th width="120">Actions</th>
                  </tr>
                </thead>
                <tbody class="mm-list-items"></tbody>
              </table>
            </div>

            <!-- Empty State -->
            <div class="mm-empty-state" style="display: none;">
              <i class="mm-icon mm-icon-folder-empty"></i>
              <h3>No files found</h3>
              <p>This folder is empty. Upload some files to get started.</p>
              ${this.config.allowUpload ? `
              <button class="mm-btn mm-btn-primary mm-btn-upload-empty">
                <i class="mm-icon mm-icon-upload"></i> Upload Files
              </button>
              ` : ''}
            </div>
          </div>
        </div>

        <!-- Context Menu -->
        <div class="mm-context-menu" style="display: none;">
          <div class="mm-context-item" data-action="open">
            <i class="mm-icon mm-icon-open"></i> Open
          </div>
          <div class="mm-context-item" data-action="preview">
            <i class="mm-icon mm-icon-eye"></i> Preview
          </div>
          <div class="mm-context-item" data-action="download">
            <i class="mm-icon mm-icon-download"></i> Download
          </div>
          ${this.config.allowRename ? `
          <div class="mm-context-divider"></div>
          <div class="mm-context-item" data-action="rename">
            <i class="mm-icon mm-icon-edit"></i> Rename
          </div>
          ` : ''}
          ${this.config.allowDelete ? `
          <div class="mm-context-divider"></div>
          <div class="mm-context-item mm-context-danger" data-action="delete">
            <i class="mm-icon mm-icon-trash"></i> Delete
          </div>
          ` : ''}
        </div>

        <!-- Image Preview Modal -->
        <div class="mm-preview-modal" style="display: none;">
          <div class="mm-preview-backdrop"></div>
          <div class="mm-preview-container">
            <div class="mm-preview-header">
              <span class="mm-preview-title"></span>
              <button class="mm-preview-close" type="button">
                <i class="mm-icon mm-icon-close"></i>
              </button>
            </div>
            <div class="mm-preview-content">
              <div class="mm-preview-loading">
                <div class="mm-spinner"></div>
                <p>Loading...</p>
              </div>
              <img class="mm-preview-image" style="display: none;" />
            </div>
            <div class="mm-preview-footer">
              <div class="mm-preview-info">
                <span class="mm-preview-name"></span>
                <span class="mm-preview-size"></span>
              </div>
              <div class="mm-preview-actions">
                <a class="mm-btn mm-btn-sm mm-btn-download" target="_blank">
                  <i class="mm-icon mm-icon-download"></i> Download
                </a>
              </div>
            </div>
          </div>
        </div>
      `;
    }

    /**
     * Cache DOM elements for better performance
     */
    cacheElements() {
      const container = document.querySelector(this.config.container);

      this.elements = {
        container: container,
        searchInput: container.querySelector('.mm-search-input'),
        searchBtn: container.querySelector('.mm-btn-search'),
        fileCount: container.querySelector('.mm-count-text'),
        bulkActions: container.querySelector('.mm-bulk-actions'),
        selectedCount: container.querySelector('.mm-selected-count'),
        viewToggle: container.querySelectorAll('.mm-view-toggle button'),
        newFolderBtn: container.querySelector('.mm-btn-new-folder'),
        uploadBtn: container.querySelector('.mm-btn-upload'),
        breadcrumb: container.querySelector('.mm-breadcrumb'),
        uploadZone: container.querySelector('.mm-upload-zone'),
        dropzone: container.querySelector('.mm-upload-dropzone'),
        fileInput: container.querySelector('.mm-file-input'),
        browseBtn: container.querySelector('.mm-btn-browse'),
        uploadList: container.querySelector('.mm-upload-list'),
        uploadProgress: container.querySelector('.mm-upload-progress'),
        progressBar: container.querySelector('.mm-progress-fill'),
        uploadStatus: container.querySelector('.mm-upload-status'),
        loading: container.querySelector('.mm-loading'),
        gridView: container.querySelector('.mm-grid-view'),
        grid: container.querySelector('.mm-grid'),
        listView: container.querySelector('.mm-list-view'),
        listItems: container.querySelector('.mm-list-items'),
        selectAllCheckbox: container.querySelector('.mm-select-all-checkbox'),
        emptyState: container.querySelector('.mm-empty-state'),
        contextMenu: document.querySelector('.mm-context-menu'),
        // Preview modal elements
        previewModal: container.querySelector('.mm-preview-modal'),
        previewBackdrop: container.querySelector('.mm-preview-backdrop'),
        previewContainer: container.querySelector('.mm-preview-container'),
        previewClose: container.querySelector('.mm-preview-close'),
        previewTitle: container.querySelector('.mm-preview-title'),
        previewContent: container.querySelector('.mm-preview-content'),
        previewLoading: container.querySelector('.mm-preview-loading'),
        previewImage: container.querySelector('.mm-preview-image'),
        previewName: container.querySelector('.mm-preview-name'),
        previewSize: container.querySelector('.mm-preview-size'),
        previewDownload: container.querySelector('.mm-preview-actions .mm-btn-download')
      };
    }

    /**
     * Setup all event listeners
     */
    setupEventListeners() {
      // Search
      this.elements.searchInput?.addEventListener('input', this.debounce(() => {
        this.performSearch();
      }, 300));

      this.elements.searchBtn?.addEventListener('click', () => {
        this.performSearch();
      });

      // View toggle
      this.elements.viewToggle?.forEach(btn => {
        btn.addEventListener('click', (e) => {
          this.toggleView(e.target.closest('button').dataset.view);
        });
      });

      // Upload
      if (this.config.allowUpload) {
        this.elements.uploadBtn?.addEventListener('click', () => {
          this.showUploadZone();
        });

        // Browse functionality handled by dropzone click

        this.elements.fileInput?.addEventListener('change', (e) => {
          this.handleFileSelect(e.target.files);
          // Reset file input to allow selecting the same files again
          e.target.value = '';
        });

        // Drag and drop
        this.setupDragAndDrop();
      }

      // New folder
      if (this.config.allowFolders) {
        this.elements.newFolderBtn?.addEventListener('click', () => {
          this.createNewFolder();
        });
      }

      // Bulk actions
      this.elements.container.querySelector('.mm-btn-select-all')?.addEventListener('click', () => {
        this.selectAll();
      });

      this.elements.container.querySelector('.mm-btn-clear-selection')?.addEventListener('click', () => {
        this.clearSelection();
      });

      this.elements.container.querySelector('.mm-btn-bulk-delete')?.addEventListener('click', () => {
        this.bulkDelete();
      });

      // Select all checkbox
      this.elements.selectAllCheckbox?.addEventListener('change', (e) => {
        this.toggleSelectAll(e.target.checked);
      });

      // Context menu
      this.setupContextMenu();

      // Image preview modal
      this.setupPreviewModal();

      // Global click to close context menu
      document.addEventListener('click', () => {
        this.hideContextMenu();
      });

      // Breadcrumb navigation
      this.elements.breadcrumb?.addEventListener('click', (e) => {
        if (e.target.classList.contains('mm-breadcrumb-item')) {
          e.preventDefault();
          const folderId = e.target.dataset.folderId;
          this.navigateToFolder(folderId || null);
        }
      });
    }

    /**
     * Setup drag and drop functionality
     */
    setupDragAndDrop() {
      const dropzone = this.elements.dropzone;
      if (!dropzone) return;

      // Prevent default drag behaviors
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
        });
      });

      // Drag enter
      dropzone.addEventListener('dragenter', () => {
        this.state.dragCounter++;
        dropzone.classList.add('mm-dragover');
      });

      // Drag leave
      dropzone.addEventListener('dragleave', () => {
        this.state.dragCounter--;
        if (this.state.dragCounter === 0) {
          dropzone.classList.remove('mm-dragover');
        }
      });

      // Drop files
      dropzone.addEventListener('drop', (e) => {
        this.state.dragCounter = 0;
        dropzone.classList.remove('mm-dragover');
        this.handleFileSelect(e.dataTransfer.files);
      });

      // Click to upload functionality - click anywhere in dropzone
      dropzone.addEventListener('click', (e) => {
        // Don't trigger if clicking on thumbnails or remove buttons
        if (e.target.closest('.mm-dropzone-previews') ||
            e.target.closest('.mm-preview-remove')) {
          return;
        }

        // Trigger file input
        if (this.elements.fileInput) {
          this.elements.fileInput.click();
        }
      });

      // Make dropzone visually clickable
      dropzone.style.cursor = 'pointer';
    }

    /**
     * Setup context menu
     */
    setupContextMenu() {
      this.elements.contextMenu?.addEventListener('click', (e) => {
        const item = e.target.closest('.mm-context-item');
        if (item) {
          const action = item.dataset.action;
          this.handleContextAction(action);
        }
      });
    }

    /**
     * Setup preview modal event listeners
     */
    setupPreviewModal() {
      // Close modal on backdrop click
      this.elements.previewBackdrop?.addEventListener('click', () => {
        this.hidePreviewModal();
      });

      // Close modal on close button click
      this.elements.previewClose?.addEventListener('click', () => {
        this.hidePreviewModal();
      });

      // Close modal on Escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.elements.previewModal?.style.display === 'flex') {
          this.hidePreviewModal();
        }
      });

      // Prevent modal content click from closing modal
      this.elements.previewContainer?.addEventListener('click', (e) => {
        e.stopPropagation();
      });
    }

    /**
     * Load folder contents
     */
    async loadContents(folderId = null) {
      try {
        this.showLoading();

        const url = `${this.config.baseUrl}/contents?folder_id=${folderId || ''}`;
        this.log('Loading contents from:', url);

        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) throw new Error('Failed to load contents');

        const data = await response.json();
        this.log('Loaded contents:', data);

        this.state.currentFolderId = folderId;
        this.state.breadcrumb = data.breadcrumb || [];
        this.state.folders = data.folders || [];
        this.state.files = data.files || [];

        this.log('State updated:', {
          currentFolderId: this.state.currentFolderId,
          folders: this.state.folders.length,
          files: this.state.files.length
        });

        this.updateBreadcrumb();
        this.renderContent();
        this.updateFileCount();

      } catch (error) {
        this.handleError('Failed to load contents', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Render content based on current view
     */
    renderContent() {
      this.log('Rendering content:', {
        view: this.state.currentView,
        folders: this.state.folders.length,
        files: this.state.files.length
      });

      // Clear existing content
      if (this.elements.grid) this.elements.grid.innerHTML = '';
      if (this.elements.listItems) this.elements.listItems.innerHTML = '';

      if (this.state.currentView === 'grid') {
        this.renderGridView();
      } else {
        this.renderListView();
      }

      // Show empty state if needed
      const isEmpty = this.state.folders.length === 0 && this.state.files.length === 0;
      if (this.elements.emptyState) {
        this.elements.emptyState.style.display = isEmpty ? 'flex' : 'none';
      }

      // Hide views properly
      if (this.elements.gridView && this.elements.listView) {
        this.elements.gridView.style.display = this.state.currentView === 'grid' ? 'block' : 'none';
        this.elements.listView.style.display = this.state.currentView === 'list' ? 'block' : 'none';
      }
    }

    /**
     * Render grid view
     */
    renderGridView() {
      const grid = this.elements.grid;
      if (!grid) {
        this.log('Grid element not found!');
        return;
      }

      this.log('Rendering grid view with:', {
        folders: this.state.folders.length,
        files: this.state.files.length
      });

      grid.innerHTML = '';

      // Render folders
      this.state.folders.forEach((folder, index) => {
        this.log(`Rendering folder ${index + 1}:`, folder.name);
        const item = this.createGridItem('folder', folder);
        grid.appendChild(item);
      });

      // Render files
      this.state.files.forEach((file, index) => {
        this.log(`Rendering file ${index + 1}:`, {
          name: file.original_name,
          type: file.type,
          url: file.url
        });
        const item = this.createGridItem('file', file);
        grid.appendChild(item);
      });

      this.log('Grid rendering complete. Grid has', grid.children.length, 'items');

      // Debug grid styles
      this.log('Grid element styles:', {
        display: getComputedStyle(grid).display,
        gridTemplateColumns: getComputedStyle(grid).gridTemplateColumns,
        gap: getComputedStyle(grid).gap
      });
    }

    /**
     * Create grid item element
     */
    createGridItem(type, data) {
      const div = document.createElement('div');
      div.className = `mm-grid-item mm-item-${type}`;
      div.dataset.type = type;
      div.dataset.id = data.id;

      if (type === 'folder') {
        div.innerHTML = `
          <div class="mm-item-icon">
            <i class="mm-icon mm-icon-folder"></i>
          </div>
          <div class="mm-item-name">${this.escapeHtml(data.name)}</div>
          <div class="mm-item-meta">${data.item_count?.total || 0} items</div>
        `;

        div.addEventListener('dblclick', () => {
          this.navigateToFolder(data.id);
        });
      } else {
        // File item
        this.log(`Creating file item for: ${data.original_name}, type: ${data.type}, url: ${data.url}`);

        if (data.type === 'image' && data.url) {
          div.innerHTML = `
            <div class="mm-item-preview">
              <img src="${data.url}" alt="${this.escapeHtml(data.original_name)}" loading="lazy" />
            </div>
            <div class="mm-item-name">${this.escapeHtml(data.original_name)}</div>
            <div class="mm-item-meta">${data.human_size}</div>
          `;

          // Add error handling after creating the element
          const img = div.querySelector('img');
          if (img) {
            img.onload = () => {
              this.log('Image loaded successfully:', data.original_name);
            };
            img.onerror = () => {
              this.log('Image failed to load:', data.original_name, 'URL:', data.url);
              const preview = div.querySelector('.mm-item-preview');
              if (preview) {
                preview.innerHTML = this.getFileIcon(data.type);
              }
            };
          }
        } else {
          div.innerHTML = `
            <div class="mm-item-icon">
              ${this.getFileIcon(data.type)}
            </div>
            <div class="mm-item-name">${this.escapeHtml(data.original_name)}</div>
            <div class="mm-item-meta">${data.human_size}</div>
          `;
        }

        div.addEventListener('dblclick', () => {
          this.previewFile(data);
        });
      }

      // Selection
      div.addEventListener('click', (e) => {
        this.handleItemClick(div, type, data, e);
      });

      // Context menu
      div.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        this.showContextMenu(e, type, data);
      });

      return div;
    }

    /**
     * Render list view
     */
    renderListView() {
      const tbody = this.elements.listItems;
      tbody.innerHTML = '';

      // Render folders
      this.state.folders.forEach(folder => {
        const row = this.createListRow('folder', folder);
        tbody.appendChild(row);
      });

      // Render files
      this.state.files.forEach(file => {
        const row = this.createListRow('file', file);
        tbody.appendChild(row);
      });
    }

    /**
     * Create list row element
     */
    createListRow(type, data) {
      const tr = document.createElement('tr');
      tr.className = 'mm-list-row';
      tr.dataset.type = type;
      tr.dataset.id = data.id;

      const checkboxCell = `<td><input type="checkbox" class="mm-item-checkbox" /></td>`;

      if (type === 'folder') {
        tr.innerHTML = `
          ${checkboxCell}
          <td class="mm-type-cell">
            <i class="mm-icon mm-icon-folder"></i>
          </td>
          <td class="mm-name-cell">${this.escapeHtml(data.name)}</td>
          <td class="mm-date-cell">${this.formatDate(data.updated_at)}</td>
          <td class="mm-size-cell">-</td>
          <td class="mm-actions-cell">
            <button class="mm-btn mm-btn-sm mm-btn-open" data-id="${data.id}">
              <i class="mm-icon mm-icon-open"></i>
            </button>
            ${this.config.allowDelete ? `
            <button class="mm-btn mm-btn-sm mm-btn-danger mm-btn-delete" data-id="${data.id}" data-type="folder">
              <i class="mm-icon mm-icon-trash"></i>
            </button>
            ` : ''}
          </td>
        `;

        tr.querySelector('.mm-btn-open')?.addEventListener('click', () => {
          this.navigateToFolder(data.id);
        });

        // Double-click to open folder (like in grid view)
        tr.addEventListener('dblclick', () => {
          this.navigateToFolder(data.id);
        });
      } else {
        // File row
        const typeIcon = data.type === 'image' && data.url
          ? `<img src="${data.url}" class="mm-list-thumbnail" alt="${this.escapeHtml(data.original_name)}" />`
          : this.getFileIcon(data.type);

        tr.innerHTML = `
          ${checkboxCell}
          <td class="mm-type-cell">${typeIcon}</td>
          <td class="mm-name-cell">${this.escapeHtml(data.original_name)}</td>
          <td class="mm-date-cell">${this.formatDate(data.updated_at)}</td>
          <td class="mm-size-cell">${data.human_size}</td>
          <td class="mm-actions-cell">
            <button class="mm-btn mm-btn-sm mm-btn-preview" data-id="${data.id}">
              <i class="mm-icon mm-icon-eye"></i>
            </button>
            <a href="${this.config.baseUrl}/download/${data.id}" class="mm-btn mm-btn-sm mm-btn-download">
              <i class="mm-icon mm-icon-download"></i>
            </a>
            ${this.config.allowDelete ? `
            <button class="mm-btn mm-btn-sm mm-btn-danger mm-btn-delete" data-id="${data.id}" data-type="file">
              <i class="mm-icon mm-icon-trash"></i>
            </button>
            ` : ''}
          </td>
        `;

        tr.querySelector('.mm-btn-preview')?.addEventListener('click', () => {
          this.previewFile(data);
        });

        // Double-click to preview file (like in grid view)
        tr.addEventListener('dblclick', () => {
          this.previewFile(data);
        });
      }

      // Handle delete buttons
      tr.querySelector('.mm-btn-delete')?.addEventListener('click', (e) => {
        const id = e.currentTarget.dataset.id;
        const type = e.currentTarget.dataset.type;
        this.deleteItem(type, id);
      });

      // Handle checkbox - use querySelector to get the element after innerHTML is set
      const checkboxElement = tr.querySelector('.mm-item-checkbox');
      checkboxElement?.addEventListener('change', (e) => {
        e.stopPropagation(); // Prevent row click event
        this.handleCheckboxChange(tr, type, data, e.target.checked);
      });

      // Prevent row click when clicking directly on checkbox
      checkboxElement?.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent row click event
      });

      // Handle row click for selection
      tr.addEventListener('click', (e) => {
        // Don't handle if clicking on actions or checkbox
        if (!e.target.closest('.mm-actions-cell') && !e.target.closest('.mm-item-checkbox')) {
          this.handleItemClick(tr, type, data, e);
        }
      });

      // Context menu
      tr.addEventListener('contextmenu', (e) => {
        e.preventDefault();
        this.showContextMenu(e, type, data);
      });

      return tr;
    }

    /**
     * Handle item click for selection
     */
    handleItemClick(element, type, data, event) {
      const itemKey = `${type}-${data.id}`;

      if (event.ctrlKey || event.metaKey) {
        // Multi-select with Ctrl/Cmd
        if (this.state.selectedItems.has(itemKey)) {
          this.deselectItem(element, itemKey);
        } else {
          this.selectItem(element, itemKey, data);
        }
      } else if (event.shiftKey && this.state.selectedItems.size > 0) {
        // Range select with Shift
        this.selectRange(element, itemKey, data);
      } else {
        // Single select
        this.clearSelection();
        this.selectItem(element, itemKey, data);
      }
    }

    /**
     * Select range of items (Shift+Click)
     */
    selectRange(element, itemKey, data) {
      // Find all items in current view
      const items = this.state.currentView === 'grid'
        ? Array.from(this.elements.grid.querySelectorAll('.mm-grid-item'))
        : Array.from(this.elements.listItems.querySelectorAll('.mm-list-row'));

      // Find indices
      const currentIndex = items.indexOf(element);
      if (currentIndex === -1) return;

      // Find the last selected item index
      let lastSelectedIndex = -1;
      for (let i = items.length - 1; i >= 0; i--) {
        const item = items[i];
        const type = item.dataset.type;
        const id = item.dataset.id;
        const key = `${type}-${id}`;
        if (this.state.selectedItems.has(key)) {
          lastSelectedIndex = i;
          break;
        }
      }

      if (lastSelectedIndex === -1) {
        // No previous selection, just select current item
        this.selectItem(element, itemKey, data);
        return;
      }

      // Select range between last selected and current
      const startIndex = Math.min(lastSelectedIndex, currentIndex);
      const endIndex = Math.max(lastSelectedIndex, currentIndex);

      for (let i = startIndex; i <= endIndex; i++) {
        const item = items[i];
        const type = item.dataset.type;
        const id = item.dataset.id;
        const key = `${type}-${id}`;

        // Get data for this item
        let itemData;
        if (type === 'folder') {
          itemData = this.state.folders.find(f => f.id == id);
        } else {
          itemData = this.state.files.find(f => f.id == id);
        }

        if (itemData && !this.state.selectedItems.has(key)) {
          this.selectItem(item, key, itemData);
        }
      }
    }

    /**
     * Select item
     */
    selectItem(element, key, data) {
      element.classList.add('mm-selected');

      // Update checkbox if in list view
      const checkbox = element.querySelector('.mm-item-checkbox');
      if (checkbox) checkbox.checked = true;

      this.state.selectedItems.add(key);
      this.updateSelectionUI();

      // Trigger callback
      if (this.config.onSelect) {
        this.config.onSelect(data, Array.from(this.state.selectedItems));
      }
    }

    /**
     * Deselect item
     */
    deselectItem(element, key) {
      element.classList.remove('mm-selected');

      // Update checkbox if in list view
      const checkbox = element.querySelector('.mm-item-checkbox');
      if (checkbox) checkbox.checked = false;

      this.state.selectedItems.delete(key);
      this.updateSelectionUI();
    }

    /**
     * Clear all selections
     */
    clearSelection() {
      // Clear visual selection
      this.elements.container.querySelectorAll('.mm-selected').forEach(el => {
        el.classList.remove('mm-selected');
      });

      // Clear checkboxes
      this.elements.container.querySelectorAll('.mm-item-checkbox').forEach(cb => {
        cb.checked = false;
      });

      // Clear state
      this.state.selectedItems.clear();
      this.updateSelectionUI();
    }

    /**
     * Update selection UI
     */
    updateSelectionUI() {
      const count = this.state.selectedItems.size;

      // Update bulk actions visibility
      this.elements.bulkActions.style.display = count > 0 ? 'flex' : 'none';
      this.elements.selectedCount.textContent = count;

      // Update select all checkbox
      const totalItems = this.state.folders.length + this.state.files.length;
      if (this.elements.selectAllCheckbox) {
        this.elements.selectAllCheckbox.checked = count === totalItems && totalItems > 0;
        this.elements.selectAllCheckbox.indeterminate = count > 0 && count < totalItems;
      }
    }

    /**
     * Toggle view mode
     */
    toggleView(view) {
      this.state.currentView = view;

      // Update buttons
      this.elements.viewToggle.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.view === view);
      });

      // Show/hide views
      this.elements.gridView.style.display = view === 'grid' ? 'block' : 'none';
      this.elements.listView.style.display = view === 'list' ? 'block' : 'none';

      // Re-render content
      this.renderContent();
    }

    /**
     * Navigate to folder
     */
    navigateToFolder(folderId) {
      this.clearSelection();
      this.loadContents(folderId);
    }

    /**
     * Update breadcrumb
     */
    updateBreadcrumb() {
      const breadcrumb = this.elements.breadcrumb;
      breadcrumb.innerHTML = '';

      // Home link
      const home = document.createElement('a');
      home.href = '#';
      home.className = 'mm-breadcrumb-item';
      home.dataset.folderId = '';
      home.innerHTML = '<i class="mm-icon mm-icon-home"></i> Home';
      breadcrumb.appendChild(home);

      // Add folder path
      this.state.breadcrumb.forEach(item => {
        const separator = document.createElement('span');
        separator.className = 'mm-breadcrumb-separator';
        separator.textContent = ' / ';
        breadcrumb.appendChild(separator);

        const link = document.createElement('a');
        link.href = '#';
        link.className = 'mm-breadcrumb-item';
        link.dataset.folderId = item.id;
        link.textContent = item.name;
        breadcrumb.appendChild(link);
      });
    }

    /**
     * Update file count display
     */
    updateFileCount() {
      const total = this.state.folders.length + this.state.files.length;
      this.elements.fileCount.textContent = `${total} items`;
    }

    /**
     * Perform search
     */
    async performSearch() {
      const query = this.elements.searchInput.value.trim();

      if (query.length < 2) {
        this.loadContents(this.state.currentFolderId);
        return;
      }

      try {
        this.showLoading();

        const response = await fetch(`${this.config.baseUrl}/search?query=${encodeURIComponent(query)}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) throw new Error('Search failed');

        const data = await response.json();
        this.state.folders = data.folders || [];
        this.state.files = data.files || [];

        this.renderContent();
        this.updateFileCount();

      } catch (error) {
        this.handleError('Search failed', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Show upload zone
     */
    showUploadZone() {
      this.elements.uploadZone.style.display = 'block';
      this.elements.gridView.style.display = 'none';
      this.elements.listView.style.display = 'none';
      this.elements.emptyState.style.display = 'none';
    }

    /**
     * Hide upload zone
     */
    hideUploadZone() {
      if (this.elements.uploadZone) {
        this.elements.uploadZone.style.display = 'none';
      }

      // Clear pending files and cleanup object URLs
      if (this._pendingFiles) {
        this._pendingFiles.forEach(file => {
          // Clean up any blob URLs that might exist
          if (file.url && file.url.startsWith('blob:')) {
            URL.revokeObjectURL(file.url);
          }
        });
        this._pendingFiles = null;
      }

      // Restore original dropzone content
      if (this.elements.dropzone) {
        this.elements.dropzone.innerHTML = `
          <div class="mm-upload-icon">
            <i class="mm-icon mm-icon-cloud-upload-alt"></i>
          </div>
          <div class="mm-upload-text">
            <h3>Click to choose from gallery</h3>
            <p>PNG, JPG, GIF up to 5MB</p>
          </div>
          <input type="file" class="mm-file-input" multiple style="display: none;" />
        `;

        // Re-setup file input listeners
        const fileInput = this.elements.dropzone.querySelector('.mm-file-input');

        if (fileInput) {
          this.elements.fileInput = fileInput;
          fileInput.addEventListener('change', (e) => {
            this.handleFileSelect(e.target.files);
            // Reset file input to allow selecting the same files again
            e.target.value = '';
          });
        }
      }

      // Clear upload list
      if (this.elements.uploadList) {
        this.elements.uploadList.innerHTML = '';
      }

      // Return to normal grid view
      this.loadContents(this.state.currentFolderId);
    }

    /**
     * Handle file selection
     */
    handleFileSelect(files) {
      const validFiles = this.validateFiles(Array.from(files));

      if (validFiles.length === 0) {
        this.showMessage('No valid files selected', 'warning');
        return;
      }

      // If there are already pending files, add to them
      if (this._pendingFiles && this._pendingFiles.length > 0) {
        this._pendingFiles = [...this._pendingFiles, ...validFiles];
        this.displaySelectedFiles(this._pendingFiles);
      } else {
        this.displaySelectedFiles(validFiles);
      }
    }

    /**
     * Validate files
     */
    validateFiles(files) {
      return files.filter(file => {
        // Check file size
        if (file.size > this.config.maxFileSize) {
          this.showMessage(`File "${file.name}" exceeds maximum size of ${this.formatFileSize(this.config.maxFileSize)}`, 'error');
          return false;
        }

        // Check file type if specified
        if (this.config.acceptedFiles !== '*') {
          const accepted = this.config.acceptedFiles.split(',').map(t => t.trim());
          const ext = '.' + file.name.split('.').pop().toLowerCase();
          const mime = file.type.toLowerCase();

          const isAccepted = accepted.some(accept => {
            if (accept.startsWith('.')) {
              return ext === accept.toLowerCase();
            } else if (accept.includes('*')) {
              return mime.startsWith(accept.replace('*', ''));
            } else {
              return mime === accept;
            }
          });

          if (!isAccepted) {
            this.showMessage(`File type "${ext}" is not allowed`, 'error');
            return false;
          }
        }

        return true;
      });
    }

    /**
     * Display selected files
     */
    displaySelectedFiles(files) {
      if (files.length === 0) return;

      // Store files for upload
      this._pendingFiles = files;

      // Show upload zone and display previews in upload list
      this.showUploadZone();
      this.showFilePreviews(files);

      this.log('Displayed preview for', files.length, 'files');
    }

    /**
     * Show file previews in dropzone area (like DropzoneJS)
     */
    showFilePreviews(files) {
      const dropzone = this.elements.dropzone;
      if (!dropzone) {
        this.log('Dropzone element not found!');
        return;
      }

      // Clear existing content and hide the default message
      dropzone.innerHTML = '';

      // Create thumbnails container
      const thumbnailsContainer = document.createElement('div');
      thumbnailsContainer.className = 'mm-dropzone-previews';

      // Create preview items as thumbnails
      files.forEach((file, index) => {
        const preview = document.createElement('div');
        preview.className = 'mm-dropzone-preview';
        preview.dataset.index = index;

        const isImage = file.type.startsWith('image/');
        const fileType = this.getFileTypeFromName(file.name);

        if (isImage) {
          const url = URL.createObjectURL(file);
          preview.innerHTML = `
            <div class="mm-preview-thumbnail">
              <img src="${url}" alt="${this.escapeHtml(file.name)}" />
              <div class="mm-preview-remove" data-index="${index}" title="Remove file"></div>
            </div>
            <div class="mm-preview-name">${this.escapeHtml(file.name)}</div>
            <div class="mm-preview-size">${this.formatFileSize(file.size)}</div>
          `;
        } else {
          preview.innerHTML = `
            <div class="mm-preview-thumbnail mm-preview-file">
              ${this.getFileIcon(fileType)}
              <div class="mm-preview-remove" data-index="${index}" title="Remove file"></div>
            </div>
            <div class="mm-preview-name">${this.escapeHtml(file.name)}</div>
            <div class="mm-preview-size">${this.formatFileSize(file.size)}</div>
          `;
        }

        thumbnailsContainer.appendChild(preview);
      });

      dropzone.appendChild(thumbnailsContainer);

      // Add upload actions below the dropzone
      const uploadList = this.elements.uploadList;
      uploadList.innerHTML = '';

      const actions = document.createElement('div');
      actions.className = 'mm-upload-actions';
      actions.innerHTML = `
        <button class="mm-btn mm-btn-primary mm-btn-start-upload">
          <i class="mm-icon mm-icon-upload"></i> Upload ${files.length} file(s)
        </button>
        <button class="mm-btn mm-btn-cancel-upload">Cancel</button>
      `;
      uploadList.appendChild(actions);

      // Add event listeners
      this.setupDropzoneListeners();

      this.log('Added', files.length, 'thumbnail previews to dropzone');
    }

    /**
     * Setup event listeners for dropzone previews
     */
    setupDropzoneListeners() {
      const dropzone = this.elements.dropzone;
      const uploadList = this.elements.uploadList;

      // Remove file buttons in dropzone
      dropzone.querySelectorAll('.mm-preview-remove').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const index = parseInt(e.currentTarget.dataset.index);
          this.removeFileFromUpload(index);
        });
      });

      // Upload button in upload list
      uploadList.querySelector('.mm-btn-start-upload')?.addEventListener('click', () => {
        this.uploadFiles(this._pendingFiles);
      });

      // Cancel button in upload list
      uploadList.querySelector('.mm-btn-cancel-upload')?.addEventListener('click', () => {
        this.hideUploadZone();
      });
    }

    /**
     * Remove file from upload list
     */
    removeFileFromUpload(index) {
      if (!this._pendingFiles || index < 0 || index >= this._pendingFiles.length) return;

      // Clean up object URL for images
      const item = this.elements.dropzone.querySelector(`[data-index="${index}"]`);
      if (item) {
        const img = item.querySelector('img');
        if (img && img.src.startsWith('blob:')) {
          URL.revokeObjectURL(img.src);
        }
      }

      // Remove from array
      this._pendingFiles.splice(index, 1);

      // Update display
      if (this._pendingFiles.length > 0) {
        this.showFilePreviews(this._pendingFiles);
      } else {
        this.hideUploadZone();
      }
    }

    /**
     * Show upload preview in main grid
     */
    showUploadPreview(files) {
      const grid = this.elements.grid;
      if (!grid) {
        this.log('Grid element not found for upload preview!');
        return;
      }

      this.log('Showing upload preview for', files.length, 'files');

      // Clear existing content and show the grid
      grid.innerHTML = '';
      this.elements.gridView.style.display = 'block';
      this.elements.listView.style.display = 'none';
      this.elements.emptyState.style.display = 'none';

      // Add upload header
      const uploadHeader = document.createElement('div');
      uploadHeader.className = 'mm-upload-header';
      uploadHeader.innerHTML = `
        <h3>Ready to Upload ${files.length} file(s)</h3>
        <p>Review your files below and click upload when ready</p>
      `;
      grid.appendChild(uploadHeader);
      this.log('Added upload header');

      // Create individual preview items directly in the grid
      files.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'mm-grid-item mm-upload-preview-item';
        item.dataset.index = index;

        const isImage = file.type.startsWith('image/');
        const fileType = this.getFileTypeFromName(file.name);

        // Add file type data attribute for styling
        item.dataset.type = fileType;

        let preview = '';

        if (isImage) {
          const url = URL.createObjectURL(file);
          preview = `
            <div class="mm-item-preview">
              <img src="${url}" class="mm-upload-preview-image" alt="${this.escapeHtml(file.name)}"
                   style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-bottom: 10px;" />
            </div>
          `;
        } else {
          preview = `
            <div class="mm-item-icon">
              ${this.getFileIcon(fileType)}
            </div>
          `;
        }

        item.innerHTML = `
          ${preview}
          <div class="mm-item-name">${this.escapeHtml(file.name)}</div>
          <div class="mm-item-meta">${this.formatFileSize(file.size)}</div>
          <button class="mm-upload-preview-remove" data-index="${index}" title="Remove file"
                  style="position: absolute; top: -8px; right: -8px; width: 26px; height: 26px;
                         border-radius: 50%; background: #dc3545; color: white; border: 2px solid white;
                         cursor: pointer; display: flex; align-items: center; justify-content: center;
                         font-size: 14px; z-index: 10;">
            <i class="mm-icon mm-icon-close"></i>
          </button>
        `;

        // Add the item directly to the grid
        grid.appendChild(item);
      });

      this.log('Added', files.length, 'preview items to grid');

      // Add upload actions at the bottom
      const uploadActions = document.createElement('div');
      uploadActions.className = 'mm-upload-actions mm-grid-actions';
      uploadActions.style.cssText = `
        grid-column: 1 / -1;
        text-align: center;
        margin-top: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border-top: 3px solid #007bff;
        display: block !important;
        width: 100%;
      `;
      uploadActions.innerHTML = `
        <button class="mm-btn mm-btn-primary mm-btn-start-upload"
                style="margin: 0 10px; padding: 12px 24px; font-size: 16px; font-weight: 500;
                       min-width: 150px; background-color: #007bff; border-color: #007bff; color: white;">
          <i class="mm-icon mm-icon-upload"></i> Upload ${files.length} file(s)
        </button>
        <button class="mm-btn mm-btn-cancel-upload"
                style="margin: 0 10px; padding: 12px 24px; font-size: 16px; font-weight: 500;
                       min-width: 150px;">Cancel</button>
      `;
      grid.appendChild(uploadActions);
      this.log('Added upload actions buttons');

      // Event listeners for remove buttons
      grid.querySelectorAll('.mm-upload-preview-remove').forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          const index = parseInt(e.currentTarget.dataset.index);
          const removedFile = this._pendingFiles[index];
          this._pendingFiles.splice(index, 1);

          // Clean up object URL to prevent memory leaks
          const img = btn.parentElement.querySelector('img');
          if (img && img.src.startsWith('blob:')) {
            URL.revokeObjectURL(img.src);
          }

          if (this._pendingFiles.length > 0) {
            this.showUploadPreview(this._pendingFiles);
          } else {
            this.hideUploadZone();
          }
        });
      });

      // Event listeners for upload actions
      uploadActions.querySelector('.mm-btn-start-upload')?.addEventListener('click', () => {
        this.uploadFiles(this._pendingFiles);
      });

      uploadActions.querySelector('.mm-btn-cancel-upload')?.addEventListener('click', () => {
        this.hideUploadZone();
      });

      // Debug: Check if elements are visible
      this.log('Final grid contents:', {
        totalChildren: grid.children.length,
        headerPresent: !!grid.querySelector('.mm-upload-header'),
        actionsPresent: !!grid.querySelector('.mm-grid-actions'),
        previewItems: grid.querySelectorAll('.mm-upload-preview-item').length,
        gridDisplay: this.elements.gridView.style.display
      });
    }

    /**
     * Upload files
     */
    async uploadFiles(files) {
      if (!files || files.length === 0) return;

      // Debug logging
      this.log('Starting upload for', files.length, 'files');
      this.log('Current folder ID:', this.state.currentFolderId);
      this.log('Base URL:', this.config.baseUrl);
      this.log('CSRF Token:', this.config.csrfToken ? 'Present' : 'Missing');

      const formData = new FormData();
      files.forEach((file, index) => {
        formData.append('files[]', file);
        this.log(`File ${index + 1}:`, {
          name: file.name,
          size: file.size,
          type: file.type
        });
      });

      if (this.state.currentFolderId) {
        formData.append('folder_id', this.state.currentFolderId);
      }

      try {
        // Show progress
        this.elements.uploadProgress.style.display = 'block';
        this.elements.uploadStatus.textContent = `Uploading ${files.length} file(s)...`;
        this.elements.progressBar.style.width = '0%';

        // Disable upload button to prevent multiple uploads
        const uploadButton = this.elements.uploadList.querySelector('.mm-btn-start-upload');
        if (uploadButton) {
          uploadButton.disabled = true;
          uploadButton.innerHTML = '<i class="mm-icon mm-icon-upload"></i> Uploading...';
        }

        // Animate progress bar
        let progress = 0;
        const progressInterval = setInterval(() => {
          progress += Math.random() * 20;
          if (progress > 90) progress = 90;
          this.elements.progressBar.style.width = progress + '%';
        }, 200);

        const uploadUrl = `${this.config.baseUrl}/upload`;
        this.log('Upload URL:', uploadUrl);

        const response = await fetch(uploadUrl, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });

        // Complete progress animation
        clearInterval(progressInterval);
        this.elements.progressBar.style.width = '100%';

        this.log('Response status:', response.status);
        this.log('Response headers:', Object.fromEntries(response.headers.entries()));

        if (!response.ok) {
          const errorText = await response.text();
          this.log('Error response:', errorText);

          let errorMessage = 'Upload failed';
          try {
            const errorJson = JSON.parse(errorText);
            errorMessage = errorJson.message || errorJson.error || errorMessage;
          } catch (e) {
            errorMessage = errorText || errorMessage;
          }

          throw new Error(errorMessage);
        }

        const resultText = await response.text();
        this.log('Success response text:', resultText);

        let result;
        try {
          result = JSON.parse(resultText);
        } catch (e) {
          this.log('Failed to parse JSON response:', e);
          throw new Error('Invalid response format');
        }

        this.log('Parsed result:', result);

        // Update status to success
        this.elements.uploadStatus.textContent = `Successfully uploaded ${result.files?.length || 0} file(s)!`;
        this.elements.uploadStatus.style.color = '#28a745';

        // Success callback
        if (this.config.onUpload) {
          this.config.onUpload(result.files);
        }

        this.showMessage(`Successfully uploaded ${result.files?.length || 0} file(s)`, 'success');

        // Wait a moment to show success, then hide upload zone and reload
        setTimeout(() => {
          this.hideUploadZone();
          // Force reload the current folder to show uploaded files
          this.log('Reloading folder after upload:', this.state.currentFolderId);
          this.loadContents(this.state.currentFolderId);
        }, 1500);

      } catch (error) {
        this.log('Upload error:', error);

        this.elements.uploadStatus.textContent = 'Upload failed!';
        this.elements.uploadStatus.style.color = '#dc3545';
        this.elements.progressBar.style.width = '0%';

        // Re-enable upload button
        const uploadButton = this.elements.uploadList.querySelector('.mm-btn-start-upload');
        if (uploadButton) {
          uploadButton.disabled = false;
          uploadButton.innerHTML = `<i class="mm-icon mm-icon-upload"></i> Upload ${files.length} file(s)`;
        }

        this.handleError('Upload failed', error);
      } finally {
        setTimeout(() => {
          this.elements.uploadProgress.style.display = 'none';
          this.elements.uploadStatus.style.color = '';
          this._pendingFiles = null;
        }, 2000);
      }
    }

    /**
     * Create new folder
     */
    async createNewFolder() {
      const name = prompt('Enter folder name:');
      if (!name) return;

      try {
        this.showLoading();

        const response = await fetch(`${this.config.baseUrl}/folder`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            name: name,
            parent_id: this.state.currentFolderId
          })
        });

        if (!response.ok) {
          const error = await response.json();
          throw new Error(error.message || 'Failed to create folder');
        }

        this.showMessage('Folder created successfully', 'success');
        this.loadContents(this.state.currentFolderId);

      } catch (error) {
        this.handleError('Failed to create folder', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Delete item
     */
    async deleteItem(type, id) {
      if (!confirm(`Are you sure you want to delete this ${type}?`)) {
        return;
      }

      try {
        this.showLoading();

        const response = await fetch(`${this.config.baseUrl}/${type}/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) {
          throw new Error('Delete failed');
        }

        // Success callback
        if (this.config.onDelete) {
          this.config.onDelete({type, id});
        }

        this.showMessage(`${type === 'folder' ? 'Folder' : 'File'} deleted successfully`, 'success');
        this.loadContents(this.state.currentFolderId);

      } catch (error) {
        this.handleError('Delete failed', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Bulk delete
     */
    async bulkDelete() {
      const count = this.state.selectedItems.size;
      if (count === 0) return;

      if (!confirm(`Are you sure you want to delete ${count} selected item(s)?`)) {
        return;
      }

      const items = Array.from(this.state.selectedItems).map(key => {
        const [type, id] = key.split('-');
        return { type, id: parseInt(id) };
      });

      try {
        this.showLoading();

        const response = await fetch(`${this.config.baseUrl}/bulk-delete`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ items })
        });

        if (!response.ok) {
          throw new Error('Bulk delete failed');
        }

        const result = await response.json();
        this.showMessage(`Deleted ${result.deleted_count} item(s)`, 'success');
        this.clearSelection();
        this.loadContents(this.state.currentFolderId);

      } catch (error) {
        this.handleError('Bulk delete failed', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Rename item
     */
    async renameItem(type, id, currentName) {
      const newName = prompt('Enter new name:', currentName);
      if (!newName || newName === currentName) return;

      try {
        this.showLoading();

        const response = await fetch(`${this.config.baseUrl}/${type}/${id}/rename`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ name: newName })
        });

        if (!response.ok) {
          throw new Error('Rename failed');
        }

        this.showMessage('Renamed successfully', 'success');
        this.loadContents(this.state.currentFolderId);

      } catch (error) {
        this.handleError('Rename failed', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Preview file
     */
    previewFile(file) {
      // You can implement a modal or use a callback
      if (this.config.onPreview) {
        this.config.onPreview(file);
      } else {
        // Default preview behavior
        if (file.type === 'image') {
          this.showImagePreview(file);
        } else {
          window.open(`${this.config.baseUrl}/download/${file.id}`, '_blank');
        }
      }
    }

    /**
     * Show image preview in modal
     */
    showImagePreview(file) {
      this.log('Showing image preview for:', file.original_name, 'URL:', file.url);

      if (!this.elements.previewModal) {
        this.log('Preview modal not found!');
        return;
      }

      // Re-cache elements if they don't exist (in case they were replaced)
      this.refreshModalElements();

      // Set up modal content
      if (this.elements.previewTitle) this.elements.previewTitle.textContent = 'Image Preview';
      if (this.elements.previewName) this.elements.previewName.textContent = file.original_name;
      if (this.elements.previewSize) this.elements.previewSize.textContent = file.human_size;
      if (this.elements.previewDownload) this.elements.previewDownload.href = `${this.config.baseUrl}/download/${file.id}`;

      // Reset modal state
      if (this.elements.previewLoading) this.elements.previewLoading.style.display = 'flex';
      if (this.elements.previewImage) {
        this.elements.previewImage.style.display = 'none';
        this.elements.previewImage.src = '';
      }

      // Hide any existing error
      const errorElement = this.elements.previewContent?.querySelector('.mm-preview-error');
      if (errorElement) {
        errorElement.style.display = 'none';
      }

      // Show modal
      this.elements.previewModal.style.display = 'flex';

      // Load image - always get fresh reference to avoid caching issues
      const img = this.elements.previewContent?.querySelector('.mm-preview-image');
      if (img) {
        this.log('Loading image:', file.url);

        // Clear any previous handlers to prevent conflicts
        img.onload = null;
        img.onerror = null;

        img.onload = () => {
          this.log('Image loaded successfully:', file.original_name);
          const loading = this.elements.previewContent?.querySelector('.mm-preview-loading');
          if (loading) loading.style.display = 'none';
          img.style.display = 'block';
        };
        img.onerror = (error) => {
          this.log('Image failed to load:', file.original_name, 'URL:', file.url, 'Error:', error);
          const loading = this.elements.previewContent?.querySelector('.mm-preview-loading');
          if (loading) loading.style.display = 'none';
          img.style.display = 'none';
          this.showImageError();
        };
        img.src = file.url;
      } else {
        this.log('Preview image element not found!');
      }
    }

    /**
     * Refresh modal element references
     */
    refreshModalElements() {
      const container = document.querySelector(this.config.container);
      if (!container) return;

      // Re-cache modal elements if they're missing
      if (!this.elements.previewLoading) {
        this.elements.previewLoading = container.querySelector('.mm-preview-loading');
      }
      if (!this.elements.previewImage) {
        this.elements.previewImage = container.querySelector('.mm-preview-image');
      }
      if (!this.elements.previewTitle) {
        this.elements.previewTitle = container.querySelector('.mm-preview-title');
      }
      if (!this.elements.previewName) {
        this.elements.previewName = container.querySelector('.mm-preview-name');
      }
      if (!this.elements.previewSize) {
        this.elements.previewSize = container.querySelector('.mm-preview-size');
      }
      if (!this.elements.previewDownload) {
        this.elements.previewDownload = container.querySelector('.mm-preview-actions .mm-btn-download');
      }
      if (!this.elements.previewContent) {
        this.elements.previewContent = container.querySelector('.mm-preview-content');
      }
    }

    /**
     * Show image error without destroying cached elements
     */
    showImageError() {
      // Create or show error element without destroying the cached elements
      let errorElement = this.elements.previewContent?.querySelector('.mm-preview-error');

      if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'mm-preview-error';
        errorElement.style.cssText = `
          text-align: center;
          padding: 40px;
          color: #666;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
        `;
        errorElement.innerHTML = `
          <i class="mm-icon mm-icon-image" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
          <p style="margin: 0;">Unable to load image</p>
        `;

        if (this.elements.previewContent) {
          this.elements.previewContent.appendChild(errorElement);
        }
      }

      errorElement.style.display = 'block';
    }

    /**
     * Hide image preview modal
     */
    hidePreviewModal() {
      if (!this.elements.previewModal) return;

      this.elements.previewModal.style.display = 'none';

      // Clean up image
      if (this.elements.previewImage && this.elements.previewImage.src) {
        if (this.elements.previewImage.src.startsWith('blob:')) {
          URL.revokeObjectURL(this.elements.previewImage.src);
        }
        this.elements.previewImage.src = '';
        this.elements.previewImage.style.display = 'none';
      }

      // Reset loading state (don't replace innerHTML to preserve cached elements)
      if (this.elements.previewLoading) {
        this.elements.previewLoading.style.display = 'flex';
      }

      // Hide any error element
      const errorElement = this.elements.previewContent?.querySelector('.mm-preview-error');
      if (errorElement) {
        errorElement.style.display = 'none';
      }

      // Clear modal info
      if (this.elements.previewTitle) this.elements.previewTitle.textContent = '';
      if (this.elements.previewName) this.elements.previewName.textContent = '';
      if (this.elements.previewSize) this.elements.previewSize.textContent = '';
      if (this.elements.previewDownload) this.elements.previewDownload.href = '';
    }

    /**
     * Show context menu
     */
    showContextMenu(event, type, data) {
      const menu = this.elements.contextMenu;

      // Store context data
      this._contextData = { type, data };

      // Configure menu items based on type
      const previewItem = menu.querySelector('[data-action="preview"]');
      const downloadItem = menu.querySelector('[data-action="download"]');
      const openItem = menu.querySelector('[data-action="open"]');

      if (type === 'folder') {
        previewItem.style.display = 'none';
        downloadItem.style.display = 'none';
        openItem.style.display = 'block';
      } else {
        previewItem.style.display = 'block';
        downloadItem.style.display = 'block';
        openItem.style.display = 'none';
      }

      // Position menu
      const x = event.pageX;
      const y = event.pageY;
      menu.style.left = x + 'px';
      menu.style.top = y + 'px';
      menu.style.display = 'block';

      event.stopPropagation();
    }

    /**
     * Hide context menu
     */
    hideContextMenu() {
      this.elements.contextMenu.style.display = 'none';
    }

    /**
     * Handle context menu action
     */
    handleContextAction(action) {
      if (!this._contextData) return;

      const { type, data } = this._contextData;

      switch (action) {
        case 'open':
          this.navigateToFolder(data.id);
          break;
        case 'preview':
          this.previewFile(data);
          break;
        case 'download':
          window.open(`${this.config.baseUrl}/download/${data.id}`, '_blank');
          break;
        case 'rename':
          this.renameItem(type, data.id, data.name || data.original_name);
          break;
        case 'delete':
          this.deleteItem(type, data.id);
          break;
      }

      this.hideContextMenu();
    }

    /**
     * Select all items
     */
    selectAll() {
      this.clearSelection();

      if (this.state.currentView === 'grid') {
        this.elements.grid.querySelectorAll('.mm-grid-item').forEach(item => {
          const type = item.dataset.type;
          const id = item.dataset.id;
          const key = `${type}-${id}`;
          item.classList.add('mm-selected');
          this.state.selectedItems.add(key);
        });
      } else {
        this.elements.listItems.querySelectorAll('.mm-list-row').forEach(row => {
          const type = row.dataset.type;
          const id = row.dataset.id;
          const key = `${type}-${id}`;
          row.classList.add('mm-selected');
          const checkbox = row.querySelector('.mm-item-checkbox');
          if (checkbox) checkbox.checked = true;
          this.state.selectedItems.add(key);
        });
      }

      this.updateSelectionUI();
    }

    /**
     * Toggle select all
     */
    toggleSelectAll(checked) {
      if (checked) {
        this.selectAll();
      } else {
        this.clearSelection();
      }
    }

    /**
     * Handle checkbox change
     */
    handleCheckboxChange(row, type, data, checked) {
      const key = `${type}-${data.id}`;

      if (checked) {
        this.selectItem(row, key, data);
      } else {
        this.deselectItem(row, key);
      }
    }

    /**
     * Get file icon HTML
     */
    getFileIcon(type) {
      const icons = {
        'image': '<i class="mm-icon mm-icon-image"></i>',
        'video': '<i class="mm-icon mm-icon-video"></i>',
        'audio': '<i class="mm-icon mm-icon-audio"></i>',
        'document': '<i class="mm-icon mm-icon-document"></i>',
        'pdf': '<i class="mm-icon mm-icon-pdf"></i>',
        'archive': '<i class="mm-icon mm-icon-archive"></i>',
        'file': '<i class="mm-icon mm-icon-file"></i>'
      };
      return icons[type] || icons['file'];
    }

    /**
     * Get file type from filename
     */
    getFileTypeFromName(filename) {
      const ext = filename.split('.').pop().toLowerCase();

      if (['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(ext)) {
        return 'image';
      } else if (['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv'].includes(ext)) {
        return 'video';
      } else if (['mp3', 'wav', 'flac', 'aac', 'ogg', 'm4a'].includes(ext)) {
        return 'audio';
      } else if (ext === 'pdf') {
        return 'pdf';
      } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {
        return 'document';
      } else if (['zip', 'rar', '7z', 'tar', 'gz'].includes(ext)) {
        return 'archive';
      }
      return 'file';
    }

    /**
     * Format file size
     */
    formatFileSize(bytes) {
      const units = ['B', 'KB', 'MB', 'GB', 'TB'];
      let i = 0;
      while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024;
        i++;
      }
      return `${bytes.toFixed(2)} ${units[i]}`;
    }

    /**
     * Format date
     */
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString();
    }

    /**
     * Show loading state
     */
    showLoading() {
      this.state.isLoading = true;
      this.elements.loading.style.display = 'flex';
    }

    /**
     * Hide loading state
     */
    hideLoading() {
      this.state.isLoading = false;
      this.elements.loading.style.display = 'none';
    }

    /**
     * Show message
     */
    showMessage(message, type = 'info') {
      // You can implement toast notifications here
      console.log(`[${type.toUpperCase()}]`, message);

      // If you have a toast library or custom implementation
      if (window.toastr) {
        window.toastr[type](message);
      } else if (this.config.onMessage) {
        this.config.onMessage(message, type);
      } else {
        // Fallback: Create a simple toast notification
        this.createToast(message, type);
      }
    }

    /**
     * Create a simple toast notification
     */
    createToast(message, type = 'info') {
      // Remove existing toasts
      const existingToasts = document.querySelectorAll('.mm-toast');
      existingToasts.forEach(toast => toast.remove());

      // Create toast element
      const toast = document.createElement('div');
      toast.className = `mm-toast mm-toast-${type}`;
      toast.innerHTML = `
        <div class="mm-toast-content">
          <span class="mm-toast-icon">${this.getToastIcon(type)}</span>
          <span class="mm-toast-message">${this.escapeHtml(message)}</span>
          <button class="mm-toast-close">&times;</button>
        </div>
      `;

      // Add styles if not already added
      if (!document.querySelector('#mm-toast-styles')) {
        this.addToastStyles();
      }

      // Add to body
      document.body.appendChild(toast);

      // Auto-remove after 5 seconds
      const autoRemove = setTimeout(() => {
        if (toast.parentNode) {
          toast.remove();
        }
      }, 5000);

      // Close button
      toast.querySelector('.mm-toast-close').addEventListener('click', () => {
        clearTimeout(autoRemove);
        toast.remove();
      });

      // Animate in
      setTimeout(() => {
        toast.classList.add('mm-toast-show');
      }, 10);
    }

    /**
     * Get toast icon based on type
     */
    getToastIcon(type) {
      const icons = {
        'success': '✓',
        'error': '✗',
        'warning': '⚠',
        'info': 'ℹ'
      };
      return icons[type] || icons['info'];
    }

    /**
     * Add toast styles to document
     */
    addToastStyles() {
      const style = document.createElement('style');
      style.id = 'mm-toast-styles';
      style.textContent = `
        .mm-toast {
          position: fixed;
          top: 20px;
          right: 20px;
          z-index: 10000;
          max-width: 350px;
          background: white;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
          border-left: 4px solid #007bff;
          transform: translateX(400px);
          transition: transform 0.3s ease;
        }

        .mm-toast.mm-toast-show {
          transform: translateX(0);
        }

        .mm-toast-success {
          border-left-color: #28a745;
        }

        .mm-toast-error {
          border-left-color: #dc3545;
        }

        .mm-toast-warning {
          border-left-color: #ffc107;
        }

        .mm-toast-info {
          border-left-color: #17a2b8;
        }

        .mm-toast-content {
          display: flex;
          align-items: center;
          padding: 16px;
          gap: 12px;
        }

        .mm-toast-icon {
          font-size: 18px;
          font-weight: bold;
          flex-shrink: 0;
        }

        .mm-toast-success .mm-toast-icon {
          color: #28a745;
        }

        .mm-toast-error .mm-toast-icon {
          color: #dc3545;
        }

        .mm-toast-warning .mm-toast-icon {
          color: #ffc107;
        }

        .mm-toast-info .mm-toast-icon {
          color: #17a2b8;
        }

        .mm-toast-message {
          flex: 1;
          font-size: 14px;
          color: #333;
          line-height: 1.4;
        }

        .mm-toast-close {
          background: none;
          border: none;
          font-size: 20px;
          color: #999;
          cursor: pointer;
          padding: 0;
          width: 24px;
          height: 24px;
          display: flex;
          align-items: center;
          justify-content: center;
          border-radius: 50%;
          transition: all 0.2s ease;
        }

        .mm-toast-close:hover {
          background: #f1f1f1;
          color: #666;
        }
      `;
      document.head.appendChild(style);
    }

    /**
     * Add modal styles to document
     */
    addModalStyles() {
      // Check if already added
      if (document.querySelector('#mm-modal-styles')) return;

      const style = document.createElement('style');
      style.id = 'mm-modal-styles';
      style.textContent = `
        .mm-preview-modal {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          z-index: 10001;
          display: flex;
          align-items: center;
          justify-content: center;
          animation: mm-modal-fadein 0.2s ease;
        }

        @keyframes mm-modal-fadein {
          from { opacity: 0; }
          to { opacity: 1; }
        }

        .mm-preview-backdrop {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.8);
          cursor: pointer;
        }

        .mm-preview-container {
          position: relative;
          background: white;
          border-radius: 12px;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
          max-width: 90vw;
          max-height: 90vh;
          display: flex;
          flex-direction: column;
          overflow: hidden;
          animation: mm-modal-scalein 0.2s ease;
        }

        @keyframes mm-modal-scalein {
          from { transform: scale(0.9); opacity: 0; }
          to { transform: scale(1); opacity: 1; }
        }

        .mm-preview-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 16px 20px;
          border-bottom: 1px solid #e5e5e5;
          background: #f8f9fa;
        }

        .mm-preview-title {
          font-size: 18px;
          font-weight: 600;
          color: #333;
          margin: 0;
        }

        .mm-preview-close {
          background: none;
          border: none;
          font-size: 24px;
          color: #666;
          cursor: pointer;
          padding: 8px;
          border-radius: 6px;
          transition: all 0.2s ease;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
        }

        .mm-preview-close:hover {
          background: #e9ecef;
          color: #333;
        }

        .mm-preview-content {
          position: relative;
          flex: 1;
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: 300px;
          max-height: 70vh;
          overflow: hidden;
        }

        .mm-preview-loading {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 40px;
          color: #666;
        }

        .mm-preview-loading .mm-spinner {
          width: 40px;
          height: 40px;
          border: 3px solid #e5e5e5;
          border-top: 3px solid #007bff;
          border-radius: 50%;
          animation: spin 1s linear infinite;
          margin-bottom: 16px;
        }

        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        .mm-preview-image {
          max-width: 100%;
          max-height: 100%;
          object-fit: contain;
          display: block;
        }

        .mm-preview-footer {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 16px 20px;
          border-top: 1px solid #e5e5e5;
          background: #f8f9fa;
        }

        .mm-preview-info {
          display: flex;
          flex-direction: column;
          gap: 4px;
        }

        .mm-preview-name {
          font-weight: 500;
          color: #333;
          font-size: 14px;
        }

        .mm-preview-size {
          font-size: 12px;
          color: #666;
        }

        .mm-preview-actions {
          display: flex;
          gap: 8px;
        }

        .mm-preview-actions .mm-btn {
          text-decoration: none;
          background: #007bff;
          color: white;
          border: none;
          border-radius: 6px;
          padding: 8px 16px;
          font-size: 14px;
          cursor: pointer;
          transition: background 0.2s ease;
          display: flex;
          align-items: center;
          gap: 6px;
        }

        .mm-preview-actions .mm-btn:hover {
          background: #0056b3;
        }

        /* Responsive design */
        @media (max-width: 768px) {
          .mm-preview-container {
            max-width: 95vw;
            max-height: 95vh;
            margin: 10px;
          }

          .mm-preview-header,
          .mm-preview-footer {
            padding: 12px 16px;
          }

          .mm-preview-content {
            min-height: 250px;
            max-height: 60vh;
          }
        }
      `;
      document.head.appendChild(style);
    }

    /**
     * Handle errors
     */
    handleError(message, error) {
      this.error(message, error);
      this.showMessage(message, 'error');

      if (this.config.onError) {
        this.config.onError(error);
      }
    }

    /**
     * Escape HTML
     */
    escapeHtml(text) {
      const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text.replace(/[&<>"']/g, m => map[m]);
    }

    /**
     * Debounce function
     */
    debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    /**
     * Log message (debug mode)
     */
    log(...args) {
      if (this.config.debug) {
        console.log('[MediaManager]', ...args);
      }
    }

    /**
     * Log error
     */
    error(...args) {
      console.error('[MediaManager]', ...args);
    }

    /**
     * Destroy the media manager
     */
    destroy() {
      // Clean up event listeners
      this.clearSelection();
      this.elements = {};
      this.state = {};

      const container = document.querySelector(this.config.container);
      if (container) {
        container.innerHTML = '';
      }
    }
  }

  /**
   * Media Picker - A specialized version for selecting media
   */
  class MediaPicker extends MediaManager {
    constructor(config = {}) {
      super({
        ...config,
        allowDelete: false,
        allowRename: false,
        onSelect: config.onSelect || null
      });

      this.multiple = config.multiple || false;
      this.fileTypes = config.fileTypes || ['all'];
      this.maxSelection = config.maxSelection || null;
    }

    /**
     * Override select item to handle picker-specific logic
     */
    selectItem(element, key, data) {
      if (!this.multiple) {
        this.clearSelection();
      }

      if (this.maxSelection && this.state.selectedItems.size >= this.maxSelection) {
        this.showMessage(`Maximum ${this.maxSelection} items can be selected`, 'warning');
        return;
      }

      super.selectItem(element, key, data);
    }

    /**
     * Get selected items data
     */
    getSelectedItems() {
      const items = [];
      this.state.selectedItems.forEach(key => {
        const [type, id] = key.split('-');
        if (type === 'file') {
          const file = this.state.files.find(f => f.id == id);
          if (file) items.push(file);
        }
      });
      return items;
    }

    /**
     * Confirm selection
     */
    confirmSelection() {
      const selected = this.getSelectedItems();

      if (selected.length === 0) {
        this.showMessage('Please select at least one file', 'warning');
        return;
      }

      if (this.config.onSelect) {
        this.config.onSelect(this.multiple ? selected : selected[0]);
      }
    }
  }

  // Export to global scope
  window.MediaManager = MediaManager;
  window.MediaPicker = MediaPicker;

})(window, document);
