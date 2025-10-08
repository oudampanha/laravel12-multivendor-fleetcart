/**
 * MediaManagerComponent.js - A reusable media management component
 * @version 2.0.0
 * @author Laravel Media Management System
 * @license MIT
 *
 * This is a simplified, reusable version of the MediaManager that can be easily
 * integrated into forms, modals, and other layouts.
 *
 * Usage Examples:
 *
 * 1. Simple file picker:
 *    const picker = new MediaManagerComponent('#my-container', {
 *      mode: 'picker',
 *      multiple: false,
 *      onSelect: (file) => console.log('Selected:', file)
 *    });
 *
 * 2. Image selector for forms:
 *    const imageSelector = new MediaManagerComponent('#image-field', {
 *      mode: 'selector',
 *      fileTypes: ['image'],
 *      onSelect: (file) => {
 *        document.getElementById('image_url').value = file.url;
 *        document.getElementById('preview').src = file.url;
 *      }
 *    });
 *
 * 3. Full media manager with multiple selection:
 *    const manager = new MediaManagerComponent('#media-manager', {
 *      mode: 'full',
 *      multiple: true,
 *      onSelect: (files) => console.log('Selected files:', files)
 *    });
 *
 * Features:
 * - Select all/uncheck all functionality (table header checkbox)
 * - Bulk action buttons (Select All / Clear Selection) when multiple=true
 * - Keyboard shortcuts: Ctrl+A (select all), Escape (clear selection)
 * - Indeterminate checkbox state for partial selections
 * - Respects maxSelection limits
 */

(function(window, document) {
  'use strict';

  class MediaManagerComponent {
    constructor(container, options = {}) {
      this.container = typeof container === 'string' ? document.querySelector(container) : container;
      if (!this.container) {
        throw new Error('MediaManagerComponent: Container element not found');
      }

      // Default configuration
      this.config = {
        // API endpoints
        baseUrl: options.baseUrl || '/admin/media',
        csrfToken: options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,

        // Component mode: 'full', 'picker', 'selector', 'upload'
        mode: options.mode || 'picker',

        // File selection options
        multiple: options.multiple !== undefined ? options.multiple : false,
        maxSelection: options.maxSelection || null,
        fileTypes: options.fileTypes || ['all'], // ['image', 'video', 'audio', 'document', 'pdf']
        maxFileSize: options.maxFileSize || 52428800, // 50MB default
        acceptedFiles: options.acceptedFiles || '*',

        // UI options
        showUpload: options.showUpload !== false,
        showFolders: options.showFolders !== false,
        showSearch: options.showSearch !== false,
        showViewToggle: options.showViewToggle !== false,
        defaultView: options.defaultView || 'grid', // 'grid' or 'list'
        compact: options.compact || false,
        height: options.height || null,

        // Callbacks
        onSelect: options.onSelect || null,
        onUpload: options.onUpload || null,
        onError: options.onError || null,
        onReady: options.onReady || null,

        // Labels and text
        labels: {
          title: options.labels?.title || 'Select Media',
          selectButton: options.labels?.selectButton || 'Select',
          cancelButton: options.labels?.cancelButton || 'Cancel',
          uploadButton: options.labels?.uploadButton || 'Upload',
          searchPlaceholder: options.labels?.searchPlaceholder || 'Search files...',
          emptyMessage: options.labels?.emptyMessage || 'No files found',
          ...options.labels
        },

        // Advanced options
        debug: options.debug || false
      };

      // State
      this.state = {
        currentFolderId: null,
        selectedItems: new Set(),
        files: [],
        folders: [],
        isLoading: false,
        isInitialized: false,
        currentView: this.config.defaultView
      };

      // Initialize
      this.init();
    }

    /**
     * Initialize the component
     */
    async init() {
      try {
        this.log('Initializing MediaManagerComponent...', this.config);

        // Add CSS if not already added
        this.ensureCSS();

        // Render UI based on mode
        this.renderUI();

        // Setup event listeners
        this.setupEventListeners();

        // Load initial content
        await this.loadContents();

        this.state.isInitialized = true;

        // Trigger ready callback
        if (this.config.onReady) {
          this.config.onReady(this);
        }

        this.log('MediaManagerComponent initialized successfully');
      } catch (error) {
        this.error('Failed to initialize MediaManagerComponent:', error);
        this.handleError('Initialization failed', error);
      }
    }

    /**
     * Render UI based on mode
     */
    renderUI() {
      this.container.innerHTML = this.getTemplate();
      this.cacheElements();
    }

    /**
     * Get HTML template based on mode
     */
    getTemplate() {
      const baseClass = `mmc-container mmc-mode-${this.config.mode}${this.config.compact ? ' mmc-compact' : ''}`;
      const heightStyle = this.config.height ? `height: ${this.config.height}` : '';

      switch (this.config.mode) {
        case 'selector':
          return this.getSelectorTemplate(baseClass, heightStyle);
        case 'upload':
          return this.getUploadTemplate(baseClass, heightStyle);
        case 'full':
          return this.getFullTemplate(baseClass, heightStyle);
        default: // picker
          return this.getPickerTemplate(baseClass, heightStyle);
      }
    }

    /**
     * Get picker template (default)
     */
    getPickerTemplate(baseClass, heightStyle) {
      return `
        <div class="${baseClass}" style="${heightStyle}">
          <div class="mmc-header">
            <div class="mmc-header-left">
              <h4 class="mmc-title">${this.config.labels.title}</h4>
              ${this.config.showSearch ? `
              <div class="mmc-search">
                <input type="text" class="mmc-search-input" placeholder="${this.config.labels.searchPlaceholder}" />
                <button class="mmc-search-btn" type="button">
                  <i class="mm-icon mm-icon-search"></i>
                </button>
              </div>
              ` : ''}
            </div>
            ${this.config.showViewToggle ? `
            <div class="mmc-header-right">
              <div class="mmc-view-toggle">
                <button class="mmc-btn mmc-view-btn ${this.state.currentView === 'grid' ? 'active' : ''}" data-view="grid" title="Grid View">
                  <i class="mm-icon mm-icon-grid"></i>
                </button>
                <button class="mmc-btn mmc-view-btn ${this.state.currentView === 'list' ? 'active' : ''}" data-view="list" title="List View">
                  <i class="mm-icon mm-icon-list"></i>
                </button>
              </div>
            </div>
            ` : ''}
          </div>

          ${this.config.showFolders ? `
          <nav class="mmc-breadcrumb">
            <a href="#" class="mmc-breadcrumb-item" data-folder-id="">
              <i class="mm-icon mm-icon-home"></i> Home
            </a>
          </nav>
          ` : ''}

          <div class="mmc-content">
            <div class="mmc-loading" style="display: none;">
              <div class="mm-spinner"></div>
              <p>Loading...</p>
            </div>

            <!-- Grid View -->
            <div class="mmc-view mmc-grid-view" style="${this.state.currentView !== 'grid' ? 'display: none;' : ''}">
              <div class="mmc-grid"></div>
            </div>

            <!-- List View -->
            <div class="mmc-view mmc-list-view" style="${this.state.currentView !== 'list' ? 'display: none;' : ''}">
              <table class="mmc-table">
                <thead>
                  <tr>
                    <th width="40">
                      <input type="checkbox" class="mmc-select-all-checkbox" title="Select all files (Ctrl+A)" />
                    </th>
                    <th width="50">Type</th>
                    <th>Name</th>
                    <th width="120">Modified</th>
                    <th width="100">Size</th>
                    <th width="100">Actions</th>
                  </tr>
                </thead>
                <tbody class="mmc-list-items"></tbody>
              </table>
            </div>

            <div class="mmc-empty" style="display: none;">
              <i class="mm-icon mm-icon-folder-empty"></i>
              <p>${this.config.labels.emptyMessage}</p>
            </div>
          </div>

          <div class="mmc-footer">
            <div class="mmc-selection-info">
              <span class="mmc-selected-count">0 selected</span>
              ${this.config.multiple ? `
              <div class="mmc-bulk-actions">
                <button class="mmc-btn mmc-btn-sm mmc-btn-select-all" type="button" title="Select all files (Ctrl+A)">
                  <i class="mm-icon mm-icon-check-all"></i> Select All
                </button>
                <button class="mmc-btn mmc-btn-sm mmc-btn-clear-selection" type="button" title="Clear selection (Esc)">
                  <i class="mm-icon mm-icon-clear"></i> Clear
                </button>
              </div>
              ` : ''}
            </div>
            <div class="mmc-actions">
              ${this.config.showUpload ? `
              <button class="mmc-btn mmc-btn-upload" type="button">
                <i class="mm-icon mm-icon-upload"></i> ${this.config.labels.uploadButton}
              </button>
              ` : ''}
              <button class="mmc-btn mmc-btn-select" type="button" disabled>
                ${this.config.labels.selectButton}
              </button>
            </div>
          </div>
        </div>
      `;
    }

    /**
     * Get selector template (inline form field)
     */
    getSelectorTemplate(baseClass, heightStyle) {
      const browseText = this.config.multiple ? 'Browse Media' : 'Browse';
      const placeholderText = this.config.multiple ? 'Click to select multiple media' : 'Click to select media';

      return `
        <div class="${baseClass}" style="${heightStyle}">
          <div class="mmc-selector-trigger">
            <div class="mmc-selected-preview">
              <div class="mmc-placeholder">
                <i class="mm-icon mm-icon-image"></i>
                <span>${placeholderText}</span>
              </div>
            </div>
            <button class="mmc-btn mmc-browse-btn" type="button">
              <i class="mm-icon mm-icon-folder"></i> ${browseText}
            </button>
          </div>

          <!-- Hidden picker modal -->
          <div class="mmc-modal" style="display: none;">
            <div class="mmc-modal-backdrop"></div>
            <div class="mmc-modal-content">
              ${this.getPickerTemplate('mmc-container mmc-mode-picker', 'height: 500px')}
            </div>
          </div>
        </div>
      `;
    }

    /**
     * Get upload template
     */
    getUploadTemplate(baseClass, heightStyle) {
      return `
        <div class="${baseClass}" style="${heightStyle}">
          <div class="mmc-upload-zone">
            <div class="mmc-dropzone">
              <div class="mmc-upload-icon">
                <i class="mm-icon mm-icon-cloud-upload-alt"></i>
              </div>
              <div class="mmc-upload-text">
                <h4>Drop files here or click to browse</h4>
                <p>Upload multiple files at once</p>
              </div>
              <input type="file" class="mmc-file-input" multiple style="display: none;" />
            </div>

            <div class="mmc-upload-progress" style="display: none;">
              <div class="mmc-progress-bar">
                <div class="mmc-progress-fill"></div>
              </div>
              <div class="mmc-upload-status"></div>
            </div>

            <div class="mmc-upload-list"></div>
          </div>
        </div>
      `;
    }

    /**
     * Get full template
     */
    getFullTemplate(baseClass, heightStyle) {
      return `
        <div class="${baseClass}" style="${heightStyle}">
          <div class="mmc-toolbar">
            <div class="mmc-toolbar-left">
              <div class="mmc-search">
                <input type="text" class="mmc-search-input" placeholder="${this.config.labels.searchPlaceholder}" />
                <button class="mmc-search-btn" type="button">
                  <i class="mm-icon mm-icon-search"></i>
                </button>
              </div>
            </div>
            <div class="mmc-toolbar-right">
              ${this.config.showViewToggle ? `
              <div class="mmc-view-toggle">
                <button class="mmc-btn mmc-view-btn ${this.state.currentView === 'grid' ? 'active' : ''}" data-view="grid" title="Grid View">
                  <i class="mm-icon mm-icon-grid"></i>
                </button>
                <button class="mmc-btn mmc-view-btn ${this.state.currentView === 'list' ? 'active' : ''}" data-view="list" title="List View">
                  <i class="mm-icon mm-icon-list"></i>
                </button>
              </div>
              ` : ''}
              <div class="mmc-actions">
                <button class="mmc-btn mmc-btn-upload" type="button">
                  <i class="mm-icon mm-icon-upload"></i> Upload
                </button>
                <button class="mmc-btn mmc-btn-new-folder" type="button">
                  <i class="mm-icon mm-icon-folder-plus"></i> New Folder
                </button>
              </div>
            </div>
          </div>

          <nav class="mmc-breadcrumb">
            <a href="#" class="mmc-breadcrumb-item" data-folder-id="">
              <i class="mm-icon mm-icon-home"></i> Home
            </a>
          </nav>

          <div class="mmc-content">
            <div class="mmc-loading" style="display: none;">
              <div class="mm-spinner"></div>
              <p>Loading...</p>
            </div>

            <!-- Grid View -->
            <div class="mmc-view mmc-grid-view" style="${this.state.currentView !== 'grid' ? 'display: none;' : ''}">
              <div class="mmc-grid"></div>
            </div>

            <!-- List View -->
            <div class="mmc-view mmc-list-view" style="${this.state.currentView !== 'list' ? 'display: none;' : ''}">
              <table class="mmc-table">
                <thead>
                  <tr>
                    <th width="40">
                      <input type="checkbox" class="mmc-select-all-checkbox" title="Select all files (Ctrl+A)" />
                    </th>
                    <th width="50">Type</th>
                    <th>Name</th>
                    <th width="120">Modified</th>
                    <th width="100">Size</th>
                    <th width="100">Actions</th>
                  </tr>
                </thead>
                <tbody class="mmc-list-items"></tbody>
              </table>
            </div>

            <div class="mmc-empty" style="display: none;">
              <i class="mm-icon mm-icon-folder-empty"></i>
              <p>This folder is empty</p>
            </div>
          </div>
        </div>
      `;
    }

    /**
     * Cache DOM elements
     */
    cacheElements() {
      this.elements = {
        container: this.container,
        header: this.container.querySelector('.mmc-header'),
        toolbar: this.container.querySelector('.mmc-toolbar'),
        searchInput: this.container.querySelector('.mmc-search-input'),
        searchBtn: this.container.querySelector('.mmc-search-btn'),
        breadcrumb: this.container.querySelector('.mmc-breadcrumb'),
        content: this.container.querySelector('.mmc-content'),
        gridView: this.container.querySelector('.mmc-grid-view'),
        grid: this.container.querySelector('.mmc-grid'),
        listView: this.container.querySelector('.mmc-list-view'),
        listItems: this.container.querySelector('.mmc-list-items'),
        selectAllCheckbox: this.container.querySelector('.mmc-select-all-checkbox'),
        viewToggle: this.container.querySelectorAll('.mmc-view-btn'),
        loading: this.container.querySelector('.mmc-loading'),
        empty: this.container.querySelector('.mmc-empty'),
        footer: this.container.querySelector('.mmc-footer'),
        selectedCount: this.container.querySelector('.mmc-selected-count'),
        selectBtn: this.container.querySelector('.mmc-btn-select'),
        selectAllBtn: this.container.querySelector('.mmc-btn-select-all'),
        clearSelectionBtn: this.container.querySelector('.mmc-btn-clear-selection'),
        uploadBtn: this.container.querySelector('.mmc-btn-upload'),
        newFolderBtn: this.container.querySelector('.mmc-btn-new-folder'),

        // Selector mode elements
        selectorTrigger: this.container.querySelector('.mmc-selector-trigger'),
        selectedPreview: this.container.querySelector('.mmc-selected-preview'),
        browseBtn: this.container.querySelector('.mmc-browse-btn'),
        modal: this.container.querySelector('.mmc-modal'),
        modalBackdrop: this.container.querySelector('.mmc-modal-backdrop'),

        // Upload mode elements
        dropzone: this.container.querySelector('.mmc-dropzone'),
        fileInput: this.container.querySelector('.mmc-file-input'),
        uploadProgress: this.container.querySelector('.mmc-upload-progress'),
        progressBar: this.container.querySelector('.mmc-progress-fill'),
        uploadStatus: this.container.querySelector('.mmc-upload-status'),
        uploadList: this.container.querySelector('.mmc-upload-list')
      };
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
      // Search
      if (this.elements.searchInput) {
        this.elements.searchInput.addEventListener('input', this.debounce(() => {
          this.performSearch();
        }, 300));
      }

      if (this.elements.searchBtn) {
        this.elements.searchBtn.addEventListener('click', () => {
          this.performSearch();
        });
      }

      // Breadcrumb navigation
      if (this.elements.breadcrumb) {
        this.elements.breadcrumb.addEventListener('click', (e) => {
          if (e.target.classList.contains('mmc-breadcrumb-item')) {
            e.preventDefault();
            const folderId = e.target.dataset.folderId;
            this.navigateToFolder(folderId || null);
          }
        });
      }

      // View toggle
      if (this.elements.viewToggle) {
        this.elements.viewToggle.forEach(btn => {
          btn.addEventListener('click', (e) => {
            const view = e.currentTarget.dataset.view;
            this.toggleView(view);
          });
        });
      }

      // Select all checkbox
      if (this.elements.selectAllCheckbox) {
        this.elements.selectAllCheckbox.addEventListener('change', (e) => {
          this.handleSelectAll(e.target.checked);
        });
      }

      // Action buttons
      if (this.elements.selectBtn) {
        this.elements.selectBtn.addEventListener('click', () => {
          this.confirmSelection();
        });
      }

      // Bulk action buttons
      if (this.elements.selectAllBtn) {
        this.elements.selectAllBtn.addEventListener('click', () => {
          this.selectAllFiles();
          if (this.elements.selectAllCheckbox) {
            this.elements.selectAllCheckbox.checked = true;
          }
        });
      }

      if (this.elements.clearSelectionBtn) {
        this.elements.clearSelectionBtn.addEventListener('click', () => {
          this.clearSelection();
        });
      }

      if (this.elements.uploadBtn) {
        this.elements.uploadBtn.addEventListener('click', () => {
          this.showUploadMode();
        });
      }

      if (this.elements.newFolderBtn) {
        this.elements.newFolderBtn.addEventListener('click', () => {
          this.createNewFolder();
        });
      }

      // Selector mode
      if (this.config.mode === 'selector') {
        this.setupSelectorMode();
      }

      // Upload mode
      if (this.config.mode === 'upload' || this.elements.dropzone) {
        this.setupUploadMode();
      }

      // Keyboard shortcuts
      this.setupKeyboardShortcuts();
    }

    /**
     * Setup selector mode events
     */
    setupSelectorMode() {
      if (this.elements.selectorTrigger) {
        this.elements.selectorTrigger.addEventListener('click', () => {
          this.showModal();
        });
      }

      if (this.elements.modalBackdrop) {
        this.elements.modalBackdrop.addEventListener('click', () => {
          this.hideModal();
        });
      }

      // Override onSelect to close modal
      const originalOnSelect = this.config.onSelect;
      this.config.onSelect = (files) => {
        this.updateSelectorPreview(files);
        this.hideModal();
        if (originalOnSelect) {
          originalOnSelect(files);
        }
      };
    }

    /**
     * Setup keyboard shortcuts
     */
    setupKeyboardShortcuts() {
      // Only add keyboard shortcuts if this component is focused or visible
      this.container.addEventListener('keydown', (e) => {
        // Ctrl+A or Cmd+A for select all
        if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
          e.preventDefault();
          if (this.config.multiple && this.state.files.length > 0) {
            if (this.elements.selectAllCheckbox) {
              this.elements.selectAllCheckbox.checked = true;
              this.handleSelectAll(true);
            }
          }
        }

        // Escape to clear selection
        if (e.key === 'Escape') {
          e.preventDefault();
          this.clearSelection();
        }
      });

      // Make container focusable for keyboard events
      if (!this.container.hasAttribute('tabindex')) {
        this.container.setAttribute('tabindex', '0');
      }
    }

    /**
     * Setup upload mode events
     */
    setupUploadMode() {
      if (this.elements.dropzone && this.elements.fileInput) {
        // Click to browse
        this.elements.dropzone.addEventListener('click', () => {
          this.elements.fileInput.click();
        });

        // File input change
        this.elements.fileInput.addEventListener('change', (e) => {
          this.handleFileSelect(e.target.files);
        });

        // Drag and drop
        this.setupDragAndDrop();
      }
    }

    /**
     * Load folder contents
     */
    async loadContents(folderId = null) {
      try {
        this.log('Loading contents for folder:', folderId);
        this.showLoading();
        this.state.currentFolderId = folderId;

        const url = `${this.config.baseUrl}/contents?folder_id=${folderId || ''}`;
        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) throw new Error('Failed to load contents');

        const data = await response.json();
        this.log('Loaded data:', data);

        // Filter files by type if specified
        this.state.files = this.filterFilesByType(data.files || []);
        this.state.folders = this.config.showFolders ? (data.folders || []) : [];

        this.log('Filtered files:', this.state.files.length, 'folders:', this.state.folders.length);

        this.updateBreadcrumb(data.breadcrumb || []);
        this.renderContent();

      } catch (error) {
        this.handleError('Failed to load contents', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Filter files by configured file types
     */
    filterFilesByType(files) {
      if (!this.config.fileTypes || this.config.fileTypes.includes('all')) {
        return files;
      }

      return files.filter(file => {
        return this.config.fileTypes.includes(file.type);
      });
    }

    /**
     * Render content based on current view
     */
    renderContent() {
      // Show empty state if no content
      const isEmpty = this.state.folders.length === 0 && this.state.files.length === 0;
      if (this.elements.empty) {
        this.elements.empty.style.display = isEmpty ? 'flex' : 'none';
      }

      if (isEmpty) return;

      // Show/hide views based on current view
      if (this.elements.gridView) {
        this.elements.gridView.style.display = this.state.currentView === 'grid' ? 'block' : 'none';
      }
      if (this.elements.listView) {
        this.elements.listView.style.display = this.state.currentView === 'list' ? 'block' : 'none';
      }

      // Render current view
      if (this.state.currentView === 'grid') {
        this.renderGridView();
      } else {
        this.renderListView();
      }

      // Restore selection state after rendering
      this.restoreSelectionState();
      this.updateSelectionUI();
    }

    /**
     * Restore selection state after rendering
     */
    restoreSelectionState() {
      this.state.selectedItems.forEach(key => {
        const [type, id] = key.split('-');
        if (type === 'file') {
          // Restore grid view selection
          const gridItem = this.container.querySelector(`.mmc-item[data-type="file"][data-id="${id}"]`);
          if (gridItem) {
            gridItem.classList.add('mmc-selected');
          }

          // Restore list view selection
          const listRow = this.container.querySelector(`.mmc-list-row[data-type="file"][data-id="${id}"]`);
          if (listRow) {
            listRow.classList.add('mmc-selected');
            const checkbox = listRow.querySelector('.mmc-item-checkbox');
            if (checkbox) {
              checkbox.checked = true;
            }
          }
        }
      });
    }

    /**
     * Render grid view
     */
    renderGridView() {
      if (!this.elements.grid) return;

      this.elements.grid.innerHTML = '';

      // Render folders first
      this.state.folders.forEach(folder => {
        const item = this.createGridItem('folder', folder);
        this.elements.grid.appendChild(item);
      });

      // Render files
      this.state.files.forEach(file => {
        const item = this.createGridItem('file', file);
        this.elements.grid.appendChild(item);
      });
    }

    /**
     * Render list view
     */
    renderListView() {
      if (!this.elements.listItems) return;

      this.elements.listItems.innerHTML = '';

      // Render folders first
      this.state.folders.forEach(folder => {
        const row = this.createListRow('folder', folder);
        this.elements.listItems.appendChild(row);
      });

      // Render files
      this.state.files.forEach(file => {
        const row = this.createListRow('file', file);
        this.elements.listItems.appendChild(row);
      });
    }

    /**
     * Create grid item element
     */
    createGridItem(type, data) {
      const div = document.createElement('div');
      div.className = `mmc-item mmc-${type}`;
      div.dataset.type = type;
      div.dataset.id = data.id;

      const isSelectable = this.config.mode === 'picker' || this.config.mode === 'selector';
      if (isSelectable) {
        div.classList.add('mmc-selectable');
      }

      if (type === 'folder') {
        div.innerHTML = `
          <div class="mmc-item-icon">
            <i class="mm-icon mm-icon-folder"></i>
          </div>
          <div class="mmc-item-name">${this.escapeHtml(data.name)}</div>
          <div class="mmc-item-meta">${data.item_count?.total || 0} items</div>
        `;

        div.addEventListener('dblclick', () => {
          this.navigateToFolder(data.id);
        });
      } else {
        // File item
        let preview = '';
        if (data.type === 'image' && data.url) {
          preview = `
            <div class="mmc-item-preview">
              <img src="${data.url}" alt="${this.escapeHtml(data.original_name)}" loading="lazy" />
            </div>
          `;
        } else {
          preview = `
            <div class="mmc-item-icon">
              ${this.getFileIcon(data.type)}
            </div>
          `;
        }

        div.innerHTML = `
          ${preview}
          <div class="mmc-item-name">${this.escapeHtml(data.original_name)}</div>
          <div class="mmc-item-meta">${data.human_size}</div>
          ${isSelectable ? '<div class="mmc-item-check"><i class="mm-icon mm-icon-check"></i></div>' : ''}
        `;
      }

      if (isSelectable) {
        div.addEventListener('click', () => {
          if (type === 'file') {
            this.toggleSelection(div, data);
          }
        });
      }

      return div;
    }

    /**
     * Create list row element
     */
    createListRow(type, data) {
      const tr = document.createElement('tr');
      tr.className = 'mmc-list-row';
      tr.dataset.type = type;
      tr.dataset.id = data.id;

      const isSelectable = this.config.mode === 'picker' || this.config.mode === 'selector';

      if (type === 'folder') {
        // Folders are not selectable, so no checkbox
        tr.innerHTML = `
          <td></td>
          <td class="mmc-type-cell">
            <i class="mm-icon mm-icon-folder"></i>
          </td>
          <td class="mmc-name-cell">${this.escapeHtml(data.name)}</td>
          <td class="mmc-date-cell">${this.formatDate(data.updated_at)}</td>
          <td class="mmc-size-cell">-</td>
          <td class="mmc-actions-cell">
            <button class="mmc-btn mmc-btn-sm mmc-btn-open" title="Open">
              <i class="mm-icon mm-icon-open"></i>
            </button>
          </td>
        `;

        tr.querySelector('.mmc-btn-open')?.addEventListener('click', () => {
          this.navigateToFolder(data.id);
        });

        tr.addEventListener('dblclick', () => {
          this.navigateToFolder(data.id);
        });
      } else {
        // File row
        const typeIcon = data.type === 'image' && data.url
          ? `<img src="${data.url}" class="mmc-list-thumbnail" alt="${this.escapeHtml(data.original_name)}" />`
          : this.getFileIcon(data.type);

        // Files can have checkboxes if selectable
        const checkboxCell = isSelectable ?
          `<td><input type="checkbox" class="mmc-item-checkbox" /></td>` :
          `<td></td>`;

        tr.innerHTML = `
          ${checkboxCell}
          <td class="mmc-type-cell">${typeIcon}</td>
          <td class="mmc-name-cell">${this.escapeHtml(data.original_name)}</td>
          <td class="mmc-date-cell">${this.formatDate(data.updated_at)}</td>
          <td class="mmc-size-cell">${data.human_size}</td>
          <td class="mmc-actions-cell">
            <button class="mmc-btn mmc-btn-sm mmc-btn-preview" title="Preview">
              <i class="mm-icon mm-icon-eye"></i>
            </button>
            <a href="${this.config.baseUrl}/download/${data.id}" class="mmc-btn mmc-btn-sm mmc-btn-download" title="Download">
              <i class="mm-icon mm-icon-download"></i>
            </a>
          </td>
        `;

        // Add event listeners for files
        if (isSelectable) {
          const checkbox = tr.querySelector('.mmc-item-checkbox');
          if (checkbox) {
            checkbox.addEventListener('change', (e) => {
              e.stopPropagation();
              // Prevent select all checkbox from triggering individual item changes
              if (e.target !== this.elements.selectAllCheckbox) {
                this.handleCheckboxChange(tr, data, e.target.checked);
              }
            });
          }

          tr.addEventListener('click', (e) => {
            // Don't toggle selection if clicking on actions, checkbox, or checkbox cell
            if (!e.target.closest('.mmc-actions-cell') &&
                !e.target.closest('.mmc-item-checkbox') &&
                !e.target.closest('td:first-child')) {
              this.toggleSelection(tr, data);
            }
          });
        }

        // Add preview button functionality
        const previewBtn = tr.querySelector('.mmc-btn-preview');
        if (previewBtn) {
          previewBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.previewFile(data);
          });
        }
      }

      return tr;
    }

    /**
     * Toggle item selection
     */
    toggleSelection(element, data) {
      const key = `file-${data.id}`;

      if (this.state.selectedItems.has(key)) {
        // Deselect
        element.classList.remove('mmc-selected');
        this.state.selectedItems.delete(key);

        // Also uncheck checkbox if in list view
        const checkbox = element.querySelector('.mmc-item-checkbox');
        if (checkbox) {
          checkbox.checked = false;
        }
      } else {
        // Select
        if (!this.config.multiple) {
          // Clear previous selections for single select
          this.clearSelection();
        }

        if (this.config.maxSelection && this.state.selectedItems.size >= this.config.maxSelection) {
          this.showMessage(`Maximum ${this.config.maxSelection} files can be selected`, 'warning');
          return;
        }

        element.classList.add('mmc-selected');
        this.state.selectedItems.add(key);

        // Also check checkbox if in list view
        const checkbox = element.querySelector('.mmc-item-checkbox');
        if (checkbox) {
          checkbox.checked = true;
        }
      }

      this.updateSelectionUI();
    }

    /**
     * Clear all selections
     */
    clearSelection() {
      this.container.querySelectorAll('.mmc-selected').forEach(el => {
        el.classList.remove('mmc-selected');
      });

      // Also uncheck all checkboxes in list view
      this.container.querySelectorAll('.mmc-item-checkbox').forEach(checkbox => {
        checkbox.checked = false;
      });

      this.state.selectedItems.clear();
      this.updateSelectionUI();
    }

    /**
     * Handle select all checkbox
     */
    handleSelectAll(checked) {
      if (checked) {
        this.selectAllFiles();
      } else {
        this.clearSelection();
      }
    }

    /**
     * Select all files
     */
    selectAllFiles() {
      if (!this.config.multiple) {
        this.showMessage('Multiple selection is not enabled', 'warning');
        if (this.elements.selectAllCheckbox) {
          this.elements.selectAllCheckbox.checked = false;
        }
        return;
      }

      // Clear current selection first (but don't trigger UI update yet)
      this.state.selectedItems.clear();

      // Select all files (not folders)
      this.state.files.forEach(file => {
        if (this.config.maxSelection && this.state.selectedItems.size >= this.config.maxSelection) {
          return; // Stop if max selection reached
        }

        const key = `file-${file.id}`;
        this.state.selectedItems.add(key);

        // Update UI elements for grid view
        const gridItem = this.container.querySelector(`.mmc-item[data-type="file"][data-id="${file.id}"]`);
        if (gridItem) {
          gridItem.classList.add('mmc-selected');
        }

        // Update UI elements for list view
        const listRow = this.container.querySelector(`.mmc-list-row[data-type="file"][data-id="${file.id}"]`);
        if (listRow) {
          listRow.classList.add('mmc-selected');
          const checkbox = listRow.querySelector('.mmc-item-checkbox');
          if (checkbox) {
            checkbox.checked = true;
          }
        }
      });

      this.updateSelectionUI();

      // Show message if max selection was reached
      if (this.config.maxSelection && this.state.selectedItems.size >= this.config.maxSelection) {
        this.showMessage(`Maximum ${this.config.maxSelection} files selected`, 'info');
      }
    }

    /**
     * Update selection UI
     */
    updateSelectionUI() {
      const count = this.state.selectedItems.size;
      const totalFiles = this.state.files.length;

      if (this.elements.selectedCount) {
        this.elements.selectedCount.textContent = `${count} selected`;
      }

      if (this.elements.selectBtn) {
        this.elements.selectBtn.disabled = count === 0;
      }

      // Update bulk action buttons
      if (this.elements.selectAllBtn) {
        this.elements.selectAllBtn.disabled = totalFiles === 0 || count === totalFiles;
        this.elements.selectAllBtn.style.display = totalFiles === 0 ? 'none' : '';
      }

      if (this.elements.clearSelectionBtn) {
        this.elements.clearSelectionBtn.disabled = count === 0;
        this.elements.clearSelectionBtn.style.display = count === 0 ? 'none' : '';
      }

      // Update select all checkbox state
      if (this.elements.selectAllCheckbox) {
        if (count === 0) {
          this.elements.selectAllCheckbox.checked = false;
          this.elements.selectAllCheckbox.indeterminate = false;
        } else if (count === totalFiles && totalFiles > 0) {
          this.elements.selectAllCheckbox.checked = true;
          this.elements.selectAllCheckbox.indeterminate = false;
        } else {
          this.elements.selectAllCheckbox.checked = false;
          this.elements.selectAllCheckbox.indeterminate = true;
        }
      }
    }

    /**
     * Confirm selection
     */
    confirmSelection() {
      const selectedFiles = this.getSelectedFiles();

      if (selectedFiles.length === 0) {
        this.showMessage('Please select at least one file', 'warning');
        return;
      }

      if (this.config.onSelect) {
        const result = this.config.multiple ? selectedFiles : selectedFiles[0];
        this.config.onSelect(result);
      }
    }

    /**
     * Get selected files data
     */
    getSelectedFiles() {
      const files = [];
      this.state.selectedItems.forEach(key => {
        const [type, id] = key.split('-');
        if (type === 'file') {
          const file = this.state.files.find(f => f.id == id);
          if (file) files.push(file);
        }
      });
      return files;
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
    updateBreadcrumb(breadcrumb) {
      if (!this.elements.breadcrumb) return;

      this.elements.breadcrumb.innerHTML = `
        <a href="#" class="mmc-breadcrumb-item" data-folder-id="">
          <i class="mm-icon mm-icon-home"></i> Home
        </a>
      `;

      breadcrumb.forEach(item => {
        const separator = document.createElement('span');
        separator.className = 'mmc-breadcrumb-separator';
        separator.textContent = ' / ';
        this.elements.breadcrumb.appendChild(separator);

        const link = document.createElement('a');
        link.href = '#';
        link.className = 'mmc-breadcrumb-item';
        link.dataset.folderId = item.id;
        link.textContent = item.name;
        this.elements.breadcrumb.appendChild(link);
      });
    }

    /**
     * Perform search
     */
    async performSearch() {
      if (!this.elements.searchInput) return;

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
        this.state.files = this.filterFilesByType(data.files || []);
        this.state.folders = this.config.showFolders ? (data.folders || []) : [];

        this.renderContent();

      } catch (error) {
        this.handleError('Search failed', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Show modal (selector mode)
     */
    showModal() {
      this.log('showModal called');
      if (this.elements.modal) {
        this.log('Modal element found, showing modal');
        this.elements.modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        // Ensure modal content is properly initialized
        this.ensureModalContent();

        // Always refresh content when modal opens to ensure latest data
        if (this.state.isInitialized) {
          this.log('Component is initialized, loading contents');
          this.loadContents(this.state.currentFolderId);
        } else {
          this.log('Component not yet initialized');
        }
      } else {
        this.log('No modal element found');
      }
    }

    /**
     * Ensure modal content is properly set up
     */
    ensureModalContent() {
      if (!this.elements.modal) {
        this.log('No modal element found');
        return;
      }

      const modalContent = this.elements.modal.querySelector('.mmc-modal-content');
      if (!modalContent) {
        this.log('No modal content found');
        return;
      }

      // Check if modal content needs to be reset
      const pickerContainer = modalContent.querySelector('.mmc-container');
      this.log('Picker container check:', !!pickerContainer, pickerContainer?.innerHTML?.trim()?.length || 0);

      if (!pickerContainer || pickerContainer.innerHTML.trim() === '') {
        this.log('Reinitializing modal content');

        // Preserve current view state
        const currentView = this.state.currentView || this.config.defaultView;
        this.log('Preserving view state:', currentView);

        modalContent.innerHTML = this.getPickerTemplate('mmc-container mmc-mode-picker', 'height: 500px');

        // Re-cache elements for picker mode
        this.cacheElements();
        this.setupEventListeners();

        // Restore view state
        this.state.currentView = currentView;
        this.log('Restored view state:', this.state.currentView);

        // Update view toggle buttons to match current view
        if (this.elements.viewToggle) {
          this.elements.viewToggle.forEach(btn => {
            if (btn.dataset.view === currentView) {
              btn.classList.add('active');
            } else {
              btn.classList.remove('active');
            }
          });
        }
      } else {
        this.log('Modal content is already properly set up');
      }
    }

    /**
     * Hide modal (selector mode)
     */
    hideModal() {
      if (this.elements.modal) {
        this.elements.modal.style.display = 'none';
        document.body.style.overflow = '';

        // Clean up any pending files if in upload mode
        if (this._pendingFiles) {
          this.cancelUpload();
        }

        // Clear any selections when modal closes
        this.clearSelection();
      }
    }

    /**
     * Update selector preview
     */
    updateSelectorPreview(files) {
      if (!this.elements.selectedPreview) return;

      if (!files || (Array.isArray(files) && files.length === 0)) {
        this.elements.selectedPreview.innerHTML = `
          <div class="mmc-placeholder">
            <i class="mm-icon mm-icon-image"></i>
            <span>Click to select media</span>
          </div>
        `;
        return;
      }

      const fileArray = Array.isArray(files) ? files : [files];

      if (this.config.multiple && fileArray.length > 1) {
        // Multiple files selected - show summary
        const firstFile = fileArray[0];
        this.elements.selectedPreview.innerHTML = `
          <div class="mmc-multiple-selection">
            ${firstFile.type === 'image' && firstFile.url ?
              `<img src="${firstFile.url}" alt="${this.escapeHtml(firstFile.original_name)}" />` :
              `<div class="mmc-selected-icon">${this.getFileIcon(firstFile.type)}</div>`
            }
            <div class="mmc-selected-count">+${fileArray.length - 1} more</div>
            <div class="mmc-selected-name">${fileArray.length} files selected</div>
          </div>
        `;
      } else {
        // Single file selected - show normally
        const file = fileArray[0];
        if (file.type === 'image' && file.url) {
          this.elements.selectedPreview.innerHTML = `
            <img src="${file.url}" alt="${this.escapeHtml(file.original_name)}" />
            <div class="mmc-selected-name">${this.escapeHtml(file.original_name)}</div>
          `;
        } else {
          this.elements.selectedPreview.innerHTML = `
            <div class="mmc-selected-icon">
              ${this.getFileIcon(file.type)}
            </div>
            <div class="mmc-selected-name">${this.escapeHtml(file.original_name)}</div>
          `;
        }
      }
    }

    /**
     * Show upload mode
     */
    showUploadMode() {
      // For components that support upload mode
      if (this.config.mode === 'upload') {
        // Already in upload mode
        return;
      }

      // For other modes, show upload modal or switch to upload view
      if (this.elements.modal) {
        // Show upload in modal with header and close button
        const uploadModal = document.createElement('div');
        uploadModal.innerHTML = `
          <div class="mmc-modal-header">
            <h4 class="mmc-modal-title">Upload Files</h4>
            <button class="mmc-modal-close" type="button">
              <i class="mm-icon mm-icon-close"></i>
            </button>
          </div>
          <div class="mmc-modal-body">
            ${this.getUploadTemplate('mmc-container mmc-mode-upload', 'height: 400px')}
          </div>
        `;

        const modalContent = this.elements.modal.querySelector('.mmc-modal-content');
        if (modalContent) {
          modalContent.innerHTML = '';
          modalContent.appendChild(uploadModal);

          // Re-cache upload elements
          const uploadContainer = uploadModal.querySelector('.mmc-container');
          this.elements.dropzone = uploadContainer.querySelector('.mmc-dropzone');
          this.elements.fileInput = uploadContainer.querySelector('.mmc-file-input');
          this.elements.uploadProgress = uploadContainer.querySelector('.mmc-upload-progress');
          this.elements.progressBar = uploadContainer.querySelector('.mmc-progress-fill');
          this.elements.uploadStatus = uploadContainer.querySelector('.mmc-upload-status');
          this.elements.uploadList = uploadContainer.querySelector('.mmc-upload-list');

          // Setup upload mode events
          this.setupUploadMode();

          // Setup close button
          const closeBtn = uploadModal.querySelector('.mmc-modal-close');
          closeBtn?.addEventListener('click', () => {
            this.hideModal();
          });

          // Add modal header styles if not already added
          this.addModalHeaderStyles();

          this.showModal();
        }
      } else {
        this.log('Upload mode requested but no modal available');
      }
    }

    /**
     * Setup drag and drop
     */
    setupDragAndDrop() {
      if (!this.elements.dropzone) return;

      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        this.elements.dropzone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
        });
      });

      this.elements.dropzone.addEventListener('dragenter', () => {
        this.elements.dropzone.classList.add('mmc-dragover');
      });

      this.elements.dropzone.addEventListener('dragleave', () => {
        this.elements.dropzone.classList.remove('mmc-dragover');
      });

      this.elements.dropzone.addEventListener('drop', (e) => {
        this.elements.dropzone.classList.remove('mmc-dragover');
        this.handleFileSelect(e.dataTransfer.files);
      });
    }

    /**
     * Handle file selection for upload
     */
    handleFileSelect(files) {
      const validFiles = this.validateFiles(Array.from(files));

      if (validFiles.length === 0) {
        this.showMessage('No valid files selected', 'warning');
        return;
      }

      // Store files for upload
      this._pendingFiles = validFiles;
      this.displaySelectedFiles(validFiles);
    }

    /**
     * Validate files
     */
    validateFiles(files) {
      const maxFileSize = this.config.maxFileSize || 52428800; // 50MB default
      const acceptedFiles = this.config.acceptedFiles || '*';

      return files.filter(file => {
        // Check file size
        if (file.size > maxFileSize) {
          this.showMessage(`File "${file.name}" exceeds maximum size of ${this.formatFileSize(maxFileSize)}`, 'error');
          return false;
        }

        // Check file type if specified
        if (acceptedFiles !== '*') {
          const accepted = acceptedFiles.split(',').map(t => t.trim());
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
     * Display selected files for upload
     */
    displaySelectedFiles(files) {
      if (!this.elements.uploadList) return;

      // Clear existing previews
      this.elements.uploadList.innerHTML = '';

      // Create preview items
      files.forEach((file, index) => {
        const preview = this.createFilePreview(file, index);
        this.elements.uploadList.appendChild(preview);
      });

      // Add upload actions
      const actions = document.createElement('div');
      actions.className = 'mmc-upload-actions';
      actions.innerHTML = `
        <button class="mmc-btn mmc-btn-primary mmc-btn-start-upload">
          <i class="mm-icon mm-icon-upload"></i> Upload ${files.length} file(s)
        </button>
        <button class="mmc-btn mmc-btn-cancel-upload">Cancel</button>
      `;
      this.elements.uploadList.appendChild(actions);

      // Setup action listeners
      actions.querySelector('.mmc-btn-start-upload')?.addEventListener('click', () => {
        this.uploadFiles(this._pendingFiles);
      });

      actions.querySelector('.mmc-btn-cancel-upload')?.addEventListener('click', () => {
        this.cancelUpload();
      });
    }

    /**
     * Create file preview element
     */
    createFilePreview(file, index) {
      const div = document.createElement('div');
      div.className = 'mmc-file-preview';
      div.dataset.index = index;

      const isImage = file.type.startsWith('image/');
      const fileType = this.getFileTypeFromName(file.name);

      let preview = '';
      if (isImage) {
        const url = URL.createObjectURL(file);
        preview = `
          <div class="mmc-preview-thumbnail">
            <img src="${url}" alt="${this.escapeHtml(file.name)}" />
            <button class="mmc-preview-remove" data-index="${index}" title="Remove file">
              <i class="mm-icon mm-icon-close"></i>
            </button>
          </div>
        `;
      } else {
        preview = `
          <div class="mmc-preview-thumbnail mmc-preview-file">
            ${this.getFileIcon(fileType)}
            <button class="mmc-preview-remove" data-index="${index}" title="Remove file">
              <i class="mm-icon mm-icon-close"></i>
            </button>
          </div>
        `;
      }

      div.innerHTML = `
        ${preview}
        <div class="mmc-preview-name">${this.escapeHtml(file.name)}</div>
        <div class="mmc-preview-size">${this.formatFileSize(file.size)}</div>
      `;

      // Remove button handler
      div.querySelector('.mmc-preview-remove')?.addEventListener('click', (e) => {
        e.stopPropagation();
        this.removeFileFromUpload(parseInt(e.currentTarget.dataset.index));
      });

      return div;
    }

    /**
     * Remove file from upload list
     */
    removeFileFromUpload(index) {
      if (!this._pendingFiles || index < 0 || index >= this._pendingFiles.length) return;

      // Clean up object URL for images
      const previewItem = this.elements.uploadList.querySelector(`[data-index="${index}"]`);
      if (previewItem) {
        const img = previewItem.querySelector('img');
        if (img && img.src.startsWith('blob:')) {
          URL.revokeObjectURL(img.src);
        }
      }

      // Remove from array
      this._pendingFiles.splice(index, 1);

      // Update display
      if (this._pendingFiles.length > 0) {
        this.displaySelectedFiles(this._pendingFiles);
      } else {
        this.cancelUpload();
      }
    }

    /**
     * Upload files to server
     */
    async uploadFiles(files) {
      if (!files || files.length === 0) return;

      const formData = new FormData();
      files.forEach((file, index) => {
        formData.append('files[]', file);
      });

      if (this.state.currentFolderId) {
        formData.append('folder_id', this.state.currentFolderId);
      }

      try {
        // Show progress
        if (this.elements.uploadProgress) {
          this.elements.uploadProgress.style.display = 'block';
        }
        if (this.elements.uploadStatus) {
          this.elements.uploadStatus.textContent = `Uploading ${files.length} file(s)...`;
        }
        if (this.elements.progressBar) {
          this.elements.progressBar.style.width = '0%';
        }

        // Disable upload button
        const uploadButton = this.elements.uploadList?.querySelector('.mmc-btn-start-upload');
        if (uploadButton) {
          uploadButton.disabled = true;
          uploadButton.innerHTML = '<i class="mm-icon mm-icon-upload"></i> Uploading...';
        }

        // Animate progress bar
        let progress = 0;
        const progressInterval = setInterval(() => {
          progress += Math.random() * 20;
          if (progress > 90) progress = 90;
          if (this.elements.progressBar) {
            this.elements.progressBar.style.width = progress + '%';
          }
        }, 200);

        const response = await fetch(`${this.config.baseUrl}/upload`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': this.config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });

        // Complete progress animation
        clearInterval(progressInterval);
        if (this.elements.progressBar) {
          this.elements.progressBar.style.width = '100%';
        }

        if (!response.ok) {
          const errorText = await response.text();
          let errorMessage = 'Upload failed';
          try {
            const errorJson = JSON.parse(errorText);
            errorMessage = errorJson.message || errorJson.error || errorMessage;
          } catch (e) {
            errorMessage = errorText || errorMessage;
          }
          throw new Error(errorMessage);
        }

        const result = await response.json();

        // Update status to success
        if (this.elements.uploadStatus) {
          this.elements.uploadStatus.textContent = `Successfully uploaded ${result.files?.length || 0} file(s)!`;
          this.elements.uploadStatus.style.color = '#28a745';
        }

        // Success callback
        if (this.config.onUpload) {
          this.config.onUpload(result.files);
        }

        this.showMessage(`Successfully uploaded ${result.files?.length || 0} file(s)`, 'success');

        // Wait a moment to show success, then cleanup
        setTimeout(() => {
          this.cancelUpload();
          this.refresh(); // Reload current folder
        }, 1500);

      } catch (error) {
        if (this.elements.uploadStatus) {
          this.elements.uploadStatus.textContent = 'Upload failed!';
          this.elements.uploadStatus.style.color = '#dc3545';
        }
        if (this.elements.progressBar) {
          this.elements.progressBar.style.width = '0%';
        }

        // Re-enable upload button
        const uploadButton = this.elements.uploadList?.querySelector('.mmc-btn-start-upload');
        if (uploadButton) {
          uploadButton.disabled = false;
          uploadButton.innerHTML = `<i class="mm-icon mm-icon-upload"></i> Upload ${files.length} file(s)`;
        }

        this.handleError('Upload failed', error);
      } finally {
        setTimeout(() => {
          if (this.elements.uploadProgress) {
            this.elements.uploadProgress.style.display = 'none';
          }
          if (this.elements.uploadStatus) {
            this.elements.uploadStatus.style.color = '';
          }
          this._pendingFiles = null;
        }, 2000);
      }
    }

    /**
     * Cancel upload and cleanup
     */
    cancelUpload() {
      // Clean up pending files and object URLs
      if (this._pendingFiles) {
        this._pendingFiles.forEach(file => {
          const previews = this.elements.uploadList?.querySelectorAll('.mmc-file-preview img');
          previews?.forEach(img => {
            if (img.src.startsWith('blob:')) {
              URL.revokeObjectURL(img.src);
            }
          });
        });
        this._pendingFiles = null;
      }

      // Clear upload list
      if (this.elements.uploadList) {
        this.elements.uploadList.innerHTML = '';
      }

      // Hide progress
      if (this.elements.uploadProgress) {
        this.elements.uploadProgress.style.display = 'none';
      }
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
     * Show/hide loading state
     */
    showLoading() {
      this.state.isLoading = true;
      if (this.elements.loading) {
        this.elements.loading.style.display = 'flex';
      }
    }

    hideLoading() {
      this.state.isLoading = false;
      if (this.elements.loading) {
        this.elements.loading.style.display = 'none';
      }
    }

    /**
     * Utility methods
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

    showMessage(message, type = 'info') {
      console.log(`[${type.toUpperCase()}]`, message);

      // Create a simple toast notification
      this.createToast(message, type);
    }

    /**
     * Create a simple toast notification
     */
    createToast(message, type = 'info') {
      // Remove existing toasts
      const existingToasts = document.querySelectorAll('.mmc-toast');
      existingToasts.forEach(toast => toast.remove());

      // Create toast element
      const toast = document.createElement('div');
      toast.className = `mmc-toast mmc-toast-${type}`;
      toast.innerHTML = `
        <div class="mmc-toast-content">
          <span class="mmc-toast-icon">${this.getToastIcon(type)}</span>
          <span class="mmc-toast-message">${this.escapeHtml(message)}</span>
          <button class="mmc-toast-close">&times;</button>
        </div>
      `;

      // Add styles if not already added
      if (!document.querySelector('#mmc-toast-styles')) {
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
      toast.querySelector('.mmc-toast-close').addEventListener('click', () => {
        clearTimeout(autoRemove);
        toast.remove();
      });

      // Animate in
      setTimeout(() => {
        toast.classList.add('mmc-toast-show');
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
      style.id = 'mmc-toast-styles';
      style.textContent = `
        .mmc-toast {
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

        .mmc-toast.mmc-toast-show {
          transform: translateX(0);
        }

        .mmc-toast-success {
          border-left-color: #28a745;
        }

        .mmc-toast-error {
          border-left-color: #dc3545;
        }

        .mmc-toast-warning {
          border-left-color: #ffc107;
        }

        .mmc-toast-info {
          border-left-color: #17a2b8;
        }

        .mmc-toast-content {
          display: flex;
          align-items: center;
          padding: 16px;
          gap: 12px;
        }

        .mmc-toast-icon {
          font-size: 18px;
          font-weight: bold;
          flex-shrink: 0;
        }

        .mmc-toast-success .mmc-toast-icon {
          color: #28a745;
        }

        .mmc-toast-error .mmc-toast-icon {
          color: #dc3545;
        }

        .mmc-toast-warning .mmc-toast-icon {
          color: #ffc107;
        }

        .mmc-toast-info .mmc-toast-icon {
          color: #17a2b8;
        }

        .mmc-toast-message {
          flex: 1;
          font-size: 14px;
          color: #333;
          line-height: 1.4;
        }

        .mmc-toast-close {
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

        .mmc-toast-close:hover {
          background: #f1f1f1;
          color: #666;
        }
      `;
      document.head.appendChild(style);
    }

    handleError(message, error) {
      this.error(message, error);
      this.showMessage(message, 'error');

      if (this.config.onError) {
        this.config.onError(error, message);
      }
    }

    log(...args) {
      if (this.config.debug) {
        console.log('[MediaManagerComponent]', ...args);
      }
    }

    error(...args) {
      console.error('[MediaManagerComponent]', ...args);
    }

    /**
     * Ensure CSS is loaded
     */
    ensureCSS() {
      if (!document.querySelector('#mmc-styles')) {
        const existingCSS = document.querySelector('link[href*="MediaManager.css"]');
        if (!existingCSS) {
          const link = document.createElement('link');
          link.id = 'mmc-styles';
          link.rel = 'stylesheet';
          link.href = '/css/MediaManagerComponent.css';
          document.head.appendChild(link);
        }
      }

      // Add icon styles if not already added
      if (!document.querySelector('#mmc-icon-styles')) {
        this.addIconStyles();
      }
    }

    /**
     * Add icon styles to document
     */
    addIconStyles() {
      const style = document.createElement('style');
      style.id = 'mmc-icon-styles';
      style.textContent = `
        /* Media Manager Icons */
        .mm-icon {
          display: inline-block;
          width: 1em;
          height: 1em;
          stroke-width: 0;
          stroke: currentColor;
          fill: currentColor;
          vertical-align: middle;
        }

        /* Home Icon */
        .mm-icon-home:before {
          content: "🏠";
        }

        /* Folder Icons */
        .mm-icon-folder:before {
          content: "📁";
        }

        .mm-icon-folder-plus:before {
          content: "📁+";
        }

        .mm-icon-folder-empty:before {
          content: "📂";
        }

        /* File Type Icons */
        .mm-icon-file:before {
          content: "📄";
        }

        .mm-icon-image:before {
          content: "🖼️";
        }

        .mm-icon-video:before {
          content: "🎥";
        }

        .mm-icon-audio:before {
          content: "🎵";
        }

        .mm-icon-document:before {
          content: "📝";
        }

        .mm-icon-pdf:before {
          content: "📋";
        }

        .mm-icon-archive:before {
          content: "🗜️";
        }

        /* Action Icons */
        .mm-icon-upload:before {
          content: "⬆️";
        }

        .mm-icon-download:before {
          content: "⬇️";
        }

        .mm-icon-cloud-upload-alt:before {
          content: "☁️⬆️";
        }

        .mm-icon-search:before {
          content: "🔍";
        }

        .mm-icon-eye:before {
          content: "👁️";
        }

        .mm-icon-open:before {
          content: "📂";
        }

        .mm-icon-edit:before {
          content: "✏️";
        }

        .mm-icon-trash:before,
        .mm-icon-delete:before {
          content: "🗑️";
        }

        .mm-icon-close:before {
          content: "✕";
        }

        .mm-icon-check:before {
          content: "✓";
        }

        .mm-icon-check-all:before {
          content: "☑️";
        }

        .mm-icon-clear:before {
          content: "🗑️";
        }

        /* View Icons */
        .mm-icon-grid:before {
          content: "⊞";
        }

        .mm-icon-list:before {
          content: "☰";
        }

        /* Additional Icons */
        .mm-icon-spinner {
          animation: mmc-icon-spin 1s linear infinite;
        }

        .mm-icon-spinner:before {
          content: "⟳";
        }

        @keyframes mmc-icon-spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        /* Fallback for browsers that don't support emoji */
        @supports not (content: "🏠") {
          .mm-icon-home:before { content: "[HOME]"; }
          .mm-icon-folder:before { content: "[FOLDER]"; }
          .mm-icon-folder-plus:before { content: "[NEW FOLDER]"; }
          .mm-icon-folder-empty:before { content: "[EMPTY]"; }
          .mm-icon-file:before { content: "[FILE]"; }
          .mm-icon-image:before { content: "[IMG]"; }
          .mm-icon-video:before { content: "[VID]"; }
          .mm-icon-audio:before { content: "[AUD]"; }
          .mm-icon-document:before { content: "[DOC]"; }
          .mm-icon-pdf:before { content: "[PDF]"; }
          .mm-icon-archive:before { content: "[ZIP]"; }
          .mm-icon-upload:before { content: "[UP]"; }
          .mm-icon-download:before { content: "[DOWN]"; }
          .mm-icon-cloud-upload-alt:before { content: "[UPLOAD]"; }
          .mm-icon-search:before { content: "[SEARCH]"; }
          .mm-icon-eye:before { content: "[VIEW]"; }
          .mm-icon-open:before { content: "[OPEN]"; }
          .mm-icon-edit:before { content: "[EDIT]"; }
          .mm-icon-trash:before,
          .mm-icon-delete:before { content: "[DEL]"; }
          .mm-icon-close:before { content: "[X]"; }
          .mm-icon-check:before { content: "[✓]"; }
          .mm-icon-check-all:before { content: "[ALL]"; }
          .mm-icon-clear:before { content: "[CLR]"; }
          .mm-icon-grid:before { content: "[GRID]"; }
          .mm-icon-list:before { content: "[LIST]"; }
          .mm-icon-spinner:before { content: "[...]"; }
        }

        /* Icon sizing and spacing */
        .mmc-btn .mm-icon {
          margin-right: 0.5em;
        }

        .mmc-btn .mm-icon:only-child {
          margin-right: 0;
        }

        /* Select all checkbox styling */
        .mmc-select-all-checkbox {
          cursor: pointer;
          transform: scale(1.1);
        }

        .mmc-select-all-checkbox:indeterminate {
          opacity: 0.8;
        }

        /* Bulk action buttons styling */
        .mmc-bulk-actions {
          display: flex;
          gap: 8px;
          margin-left: 16px;
        }

        .mmc-btn-sm {
          padding: 4px 8px;
          font-size: 12px;
          border-radius: 4px;
        }

        .mmc-btn-select-all {
          background: #28a745;
          color: white;
          border: 1px solid #28a745;
        }

        .mmc-btn-select-all:hover:not(:disabled) {
          background: #218838;
          border-color: #1e7e34;
        }

        .mmc-btn-clear-selection {
          background: #6c757d;
          color: white;
          border: 1px solid #6c757d;
        }

        .mmc-btn-clear-selection:hover:not(:disabled) {
          background: #5a6268;
          border-color: #545b62;
        }

        .mmc-btn-sm:disabled {
          opacity: 0.6;
          cursor: not-allowed;
        }

        .mmc-selection-info {
          display: flex;
          align-items: center;
          flex-wrap: wrap;
          gap: 8px;
        }

        /* List view improvements */
        .mmc-list-row.mmc-selected {
          background-color: #e3f2fd;
        }

        .mmc-list-row:hover {
          background-color: #f5f5f5;
        }

        .mmc-list-row.mmc-selected:hover {
          background-color: #bbdefb;
        }

        .mmc-item-checkbox {
          cursor: pointer;
          transform: scale(1.1);
        }

        .mmc-list-thumbnail {
          width: 32px;
          height: 32px;
          object-fit: cover;
          border-radius: 4px;
        }

        /* Multiple selection preview styling */
        .mmc-multiple-selection {
          position: relative;
          display: flex;
          flex-direction: column;
          align-items: center;
          text-align: center;
        }

        .mmc-multiple-selection img {
          width: 80px;
          height: 80px;
          object-fit: cover;
          border-radius: 8px;
          margin-bottom: 8px;
        }

        .mmc-multiple-selection .mmc-selected-icon {
          font-size: 3em;
          margin-bottom: 8px;
          color: #6c757d;
        }

        .mmc-selected-count {
          position: absolute;
          top: -5px;
          right: -5px;
          background: #007bff;
          color: white;
          font-size: 11px;
          padding: 2px 6px;
          border-radius: 10px;
          font-weight: bold;
        }

        .mmc-selected-name {
          font-size: 12px;
          color: #6c757d;
          margin-top: 4px;
        }

        .mmc-item-icon .mm-icon {
          font-size: 2em;
          color: #6c757d;
        }

        .mmc-type-cell .mm-icon {
          font-size: 1.2em;
          color: #6c757d;
        }

        /* Spinner styles */
        .mm-spinner {
          width: 40px;
          height: 40px;
          border: 3px solid #e5e5e5;
          border-top: 3px solid #007bff;
          border-radius: 50%;
          animation: mmc-spin 1s linear infinite;
        }

        @keyframes mmc-spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
      `;
      document.head.appendChild(style);
    }

    /**
     * Add modal header styles
     */
    addModalHeaderStyles() {
      if (document.querySelector('#mmc-modal-header-styles')) return;

      const style = document.createElement('style');
      style.id = 'mmc-modal-header-styles';
      style.textContent = `
        .mmc-modal-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 16px 20px;
          border-bottom: 1px solid #e5e5e5;
          background: #f8f9fa;
          border-radius: 12px 12px 0 0;
        }

        .mmc-modal-title {
          font-size: 18px;
          font-weight: 600;
          color: #333;
          margin: 0;
        }

        .mmc-modal-close {
          background: none;
          border: none;
          font-size: 24px;
          color: #666;
          cursor: pointer;
          padding: 8px;
          border-radius: 6px;
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        }

        .mmc-modal-close:hover {
          background: #e9ecef;
          color: #333;
        }

        .mmc-modal-body {
          padding: 0;
          overflow: auto;
        }

        /* Enhance modal content layout */
        .mmc-modal-content {
          background: white;
          border-radius: 12px;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
          max-width: 90vw;
          max-height: 90vh;
          width: 800px;
          display: flex;
          flex-direction: column;
          overflow: hidden;
          margin: 20px;
        }

        /* Modal backdrop styles */
        .mmc-modal {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          z-index: 10000;
          display: flex;
          align-items: center;
          justify-content: center;
          animation: mmc-modal-fadein 0.2s ease;
        }

        .mmc-modal-backdrop {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.5);
          cursor: pointer;
        }

        @keyframes mmc-modal-fadein {
          from { opacity: 0; }
          to { opacity: 1; }
        }

        /* Responsive design */
        @media (max-width: 768px) {
          .mmc-modal-content {
            width: 95vw;
            max-height: 95vh;
            margin: 10px;
          }

          .mmc-modal-header {
            padding: 12px 16px;
          }
        }
      `;
      document.head.appendChild(style);
    }

    /**
     * Toggle view between grid and list
     */
    toggleView(view) {
      if (view === this.state.currentView) return;

      this.state.currentView = view;

      // Update button states
      if (this.elements.viewToggle) {
        this.elements.viewToggle.forEach(btn => {
          if (btn.dataset.view === view) {
            btn.classList.add('active');
          } else {
            btn.classList.remove('active');
          }
        });
      }

      // Re-render content with new view
      this.renderContent();
    }

    /**
     * Handle checkbox change in list view
     */
    handleCheckboxChange(element, data, checked) {
      const key = `file-${data.id}`;

      if (checked) {
        if (!this.config.multiple) {
          this.clearSelection();
        }

        if (this.config.maxSelection && this.state.selectedItems.size >= this.config.maxSelection) {
          element.querySelector('.mmc-item-checkbox').checked = false;
          this.showMessage(`Maximum ${this.config.maxSelection} files can be selected`, 'warning');
          return;
        }

        element.classList.add('mmc-selected');
        this.state.selectedItems.add(key);
      } else {
        element.classList.remove('mmc-selected');
        this.state.selectedItems.delete(key);
      }

      this.updateSelectionUI();
    }

    /**
     * Format date for display
     */
    formatDate(dateString) {
      if (!dateString) return '-';

      const date = new Date(dateString);
      const now = new Date();
      const diffTime = Math.abs(now - date);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

      if (diffDays === 1) {
        return 'Today';
      } else if (diffDays === 2) {
        return 'Yesterday';
      } else if (diffDays <= 7) {
        return `${diffDays} days ago`;
      } else {
        return date.toLocaleDateString();
      }
    }

    /**
     * Preview file
     */
    previewFile(file) {
      if (file.type === 'image' && file.url) {
        this.showImagePreview(file);
      } else {
        // For non-images, open download link
        window.open(`${this.config.baseUrl}/download/${file.id}`, '_blank');
      }
    }

    /**
     * Show image preview modal
     */
    showImagePreview(file) {
      // Create preview modal if it doesn't exist
      if (!document.querySelector('.mmc-preview-modal')) {
        this.createPreviewModal();
      }

      const modal = document.querySelector('.mmc-preview-modal');
      const img = modal.querySelector('.mmc-preview-image');
      const title = modal.querySelector('.mmc-preview-title');
      const name = modal.querySelector('.mmc-preview-name');
      const size = modal.querySelector('.mmc-preview-size');
      const download = modal.querySelector('.mmc-preview-download');
      const loading = modal.querySelector('.mmc-preview-loading');

      // Set up modal content
      if (title) title.textContent = 'Image Preview';
      if (name) name.textContent = file.original_name;
      if (size) size.textContent = file.human_size;
      if (download) download.href = `${this.config.baseUrl}/download/${file.id}`;

      // Show loading
      if (loading) loading.style.display = 'flex';
      if (img) img.style.display = 'none';

      // Show modal
      modal.style.display = 'flex';

      // Load image
      if (img) {
        img.onload = () => {
          if (loading) loading.style.display = 'none';
          img.style.display = 'block';
        };
        img.onerror = () => {
          if (loading) loading.style.display = 'none';
          this.showMessage('Failed to load image', 'error');
        };
        img.src = file.url;
      }
    }

    /**
     * Create image preview modal
     */
    createPreviewModal() {
      const modal = document.createElement('div');
      modal.className = 'mmc-preview-modal';
      modal.style.display = 'none';
      modal.innerHTML = `
        <div class="mmc-preview-backdrop"></div>
        <div class="mmc-preview-container">
          <div class="mmc-preview-header">
            <span class="mmc-preview-title">Image Preview</span>
            <button class="mmc-preview-close" type="button">
              <i class="mm-icon mm-icon-close"></i>
            </button>
          </div>
          <div class="mmc-preview-content">
            <div class="mmc-preview-loading">
              <div class="mmc-spinner"></div>
              <p>Loading...</p>
            </div>
            <img class="mmc-preview-image" style="display: none;" />
          </div>
          <div class="mmc-preview-footer">
            <div class="mmc-preview-info">
              <span class="mmc-preview-name"></span>
              <span class="mmc-preview-size"></span>
            </div>
            <div class="mmc-preview-actions">
              <a class="mmc-btn mmc-btn-sm mmc-btn-download" target="_blank">
                <i class="mm-icon mm-icon-download"></i> Download
              </a>
            </div>
          </div>
        </div>
      `;

      document.body.appendChild(modal);

      // Add modal styles
      this.addPreviewModalStyles();

      // Event listeners
      modal.querySelector('.mmc-preview-backdrop')?.addEventListener('click', () => {
        this.hidePreviewModal();
      });

      modal.querySelector('.mmc-preview-close')?.addEventListener('click', () => {
        this.hidePreviewModal();
      });

      // Escape key to close
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
          this.hidePreviewModal();
        }
      });
    }

    /**
     * Hide preview modal
     */
    hidePreviewModal() {
      const modal = document.querySelector('.mmc-preview-modal');
      if (modal) {
        modal.style.display = 'none';
        const img = modal.querySelector('.mmc-preview-image');
        if (img) {
          img.src = '';
          img.style.display = 'none';
        }
      }
    }

    /**
     * Add preview modal styles
     */
    addPreviewModalStyles() {
      if (document.querySelector('#mmc-preview-modal-styles')) return;

      const style = document.createElement('style');
      style.id = 'mmc-preview-modal-styles';
      style.textContent = `
        .mmc-preview-modal {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          z-index: 10001;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .mmc-preview-backdrop {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.8);
          cursor: pointer;
        }

        .mmc-preview-container {
          position: relative;
          background: white;
          border-radius: 12px;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
          max-width: 90vw;
          max-height: 90vh;
          display: flex;
          flex-direction: column;
          overflow: hidden;
        }

        .mmc-preview-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 16px 20px;
          border-bottom: 1px solid #e5e5e5;
          background: #f8f9fa;
        }

        .mmc-preview-title {
          font-size: 18px;
          font-weight: 600;
          color: #333;
          margin: 0;
        }

        .mmc-preview-close {
          background: none;
          border: none;
          font-size: 24px;
          color: #666;
          cursor: pointer;
          padding: 8px;
          border-radius: 6px;
          width: 40px;
          height: 40px;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .mmc-preview-close:hover {
          background: #e9ecef;
          color: #333;
        }

        .mmc-preview-content {
          position: relative;
          flex: 1;
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: 300px;
          max-height: 70vh;
          overflow: hidden;
        }

        .mmc-preview-loading {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding: 40px;
          color: #666;
        }

        .mmc-preview-loading .mmc-spinner {
          width: 40px;
          height: 40px;
          border: 3px solid #e5e5e5;
          border-top: 3px solid #007bff;
          border-radius: 50%;
          animation: mmc-spin 1s linear infinite;
          margin-bottom: 16px;
        }

        @keyframes mmc-spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }

        .mmc-preview-image {
          max-width: 100%;
          max-height: 100%;
          object-fit: contain;
          display: block;
        }

        .mmc-preview-footer {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 16px 20px;
          border-top: 1px solid #e5e5e5;
          background: #f8f9fa;
        }

        .mmc-preview-info {
          display: flex;
          flex-direction: column;
          gap: 4px;
        }

        .mmc-preview-name {
          font-weight: 500;
          color: #333;
          font-size: 14px;
        }

        .mmc-preview-size {
          font-size: 12px;
          color: #666;
        }

        .mmc-preview-actions {
          display: flex;
          gap: 8px;
        }

        .mmc-preview-actions .mmc-btn {
          text-decoration: none;
          background: #007bff;
          color: white;
          border: none;
          border-radius: 6px;
          padding: 8px 16px;
          font-size: 14px;
          cursor: pointer;
          display: flex;
          align-items: center;
          gap: 6px;
        }

        .mmc-preview-actions .mmc-btn:hover {
          background: #0056b3;
        }
      `;
      document.head.appendChild(style);
    }

    /**
     * Delete item (files/folders)
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

        this.showMessage(`${type === 'folder' ? 'Folder' : 'File'} deleted successfully`, 'success');
        this.refresh();

      } catch (error) {
        this.handleError('Delete failed', error);
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
        this.refresh();

      } catch (error) {
        this.handleError('Rename failed', error);
      } finally {
        this.hideLoading();
      }
    }

    /**
     * Public API methods
     */

    // Get current selection
    getSelection() {
      return this.getSelectedFiles();
    }

    // Clear selection
    clear() {
      this.clearSelection();
    }

    // Refresh content
    refresh() {
      return this.loadContents(this.state.currentFolderId);
    }

    // Destroy component
    destroy() {
      if (this.container) {
        this.container.innerHTML = '';
      }
      this.state = {};
      this.elements = {};
    }

    // Set configuration
    setConfig(newConfig) {
      Object.assign(this.config, newConfig);
    }
  }

  // Export to global scope
  window.MediaManagerComponent = MediaManagerComponent;

  // jQuery plugin wrapper (optional)
  if (window.jQuery) {
    window.jQuery.fn.mediaManagerComponent = function(options) {
      return this.each(function() {
        const $this = window.jQuery(this);
        let instance = $this.data('mediaManagerComponent');

        if (!instance) {
          instance = new MediaManagerComponent(this, options);
          $this.data('mediaManagerComponent', instance);
        }

        return instance;
      });
    };
  }

})(window, document);