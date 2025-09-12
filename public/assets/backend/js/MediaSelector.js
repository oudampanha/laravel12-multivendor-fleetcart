/**
 * MediaSelector - Reusable component for image/media selection
 * Supports both gallery selection and direct file upload
 */
class MediaSelector {
    static instances = new Map();
    static mediaManagerModal = null;
    static currentComponentId = null;
    static mediaManagerInstance = null;

    /**
     * Initialize MediaSelector component
     * @param {string} componentId - Unique component identifier
     * @param {Object} options - Configuration options
     */
    static initialize(componentId, options = {}) {
        const defaultOptions = {
            maxFileSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            showPreview: true,
            showSuccess: true,
            showError: true
        };

        const config = { ...defaultOptions, ...options };
        this.instances.set(componentId, config);

        // Initialize existing image if present
        const urlInput = document.getElementById(componentId + '_url_input');
        if (urlInput && urlInput.value) {
            this.setImagePreview(componentId, urlInput.value);
        }

        console.log('MediaSelector initialized for:', componentId);
    }

    /**
     * Open media gallery modal
     * @param {string} componentId - Component identifier
     */
    static openGallery(componentId) {
        this.currentComponentId = componentId;

        if (!this.mediaManagerModal) {
            this.createMediaManagerModal();
        }

        // Show the modal
        $(this.mediaManagerModal).modal('show');
    }

    /**
     * Create the media manager modal if it doesn't exist
     */
    static createMediaManagerModal() {
        // Check if modal already exists
        const existingModal = document.getElementById('reusableMediaManagerModal');
        if (existingModal) {
            this.mediaManagerModal = existingModal;
            return;
        }

        // Create modal HTML
        const modalHTML = `
            <div class="modal fade" id="reusableMediaManagerModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-images"></i> Choose Image
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-0">
                            <div id="reusableMediaManagerContainer"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="reusableSelectImageBtn" disabled>Select Image</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Append to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.mediaManagerModal = document.getElementById('reusableMediaManagerModal');

        // Initialize event handlers
        this.initializeModalEvents();
    }

    /**
     * Initialize modal event handlers
     */
    static initializeModalEvents() {
        const modal = $(this.mediaManagerModal);
        const selectBtn = document.getElementById('reusableSelectImageBtn');

        // Initialize MediaManager when modal is shown
        modal.on('shown.bs.modal', () => {
            this.initializeMediaManager();
        });

        // Clean up when modal is hidden
        modal.on('hidden.bs.modal', () => {
            this.cleanupModal();
        });

        // Handle image selection
        selectBtn.addEventListener('click', () => {
            this.handleGallerySelection();
        });
    }

    /**
     * Initialize MediaManager instance
     */
    static initializeMediaManager() {
        if (this.mediaManagerInstance) {
            return;
        }

        const container = document.getElementById('reusableMediaManagerContainer');
        if (!container) return;

        try {
            // Get configuration from global config or use defaults
            const config = window.MediaSelectorConfig || {};
            const endpoints = config.endpoints || {
                list: 'admin/media/list',
                upload: 'admin/media/upload',
                bulkUpload: 'admin/media/bulk-upload',
                createFolder: 'admin/media/create-folder',
                renameFolder: 'admin/media/rename-folder',
                deleteFolder: 'admin/media/delete-folder',
                renameFile: 'admin/media/rename-file',
                deleteFile: 'admin/media/delete/{id}'
            };

            const defaults = config.mediaManagerDefaults || {};

            this.mediaManagerInstance = new MediaManager({
                container: container,
                endpoints: endpoints,
                modal: defaults.modal !== undefined ? defaults.modal : false,
                multiple: defaults.multiple !== undefined ? defaults.multiple : false,
                showUploadButton: defaults.showUploadButton !== undefined ? defaults.showUploadButton : true,
                showCreateFolderButton: defaults.showCreateFolderButton !== undefined ? defaults.showCreateFolderButton : false,
                showViewControls: defaults.showViewControls !== undefined ? defaults.showViewControls : true,
                showSearch: defaults.showSearch !== undefined ? defaults.showSearch : true,
                showBreadcrumb: defaults.showBreadcrumb !== undefined ? defaults.showBreadcrumb : true,
                showContextMenu: defaults.showContextMenu !== undefined ? defaults.showContextMenu : false,
                maxFileSize: defaults.maxFileSize || 5 * 1024 * 1024,
                acceptedTypes: defaults.acceptedTypes || 'image/*',
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
                onSelect: (files) => {
                    const selectBtn = document.getElementById('reusableSelectImageBtn');
                    if (files && files.length > 0) {
                        this.selectedFile = files[0];
                        selectBtn.disabled = false;
                    } else {
                        this.selectedFile = null;
                        selectBtn.disabled = true;
                    }
                },
                onError: (error) => {
                    console.error('MediaManager Error:', error);
                    this.showNotification('error', 'Error', 'Failed to load media manager');
                }
            });
        } catch (error) {
            console.error('Failed to initialize MediaManager:', error);
        }
    }

    /**
     * Handle gallery image selection
     */
    static handleGallerySelection() {
        if (!this.selectedFile || !this.currentComponentId) return;

        const componentId = this.currentComponentId;
        const imageUrl = this.selectedFile.url;

        // Update the component
        this.setImagePreview(componentId, imageUrl);
        this.updateFormInputs(componentId, imageUrl, null);

        // Close modal
        $(this.mediaManagerModal).modal('hide');

        // Show success message
        this.showNotification('success', 'Image Selected!', 'Image has been updated.');
    }

    /**
     * Handle direct file upload
     * @param {string} componentId - Component identifier
     * @param {HTMLInputElement} fileInput - File input element
     */
    static handleFileUpload(componentId, fileInput) {
        if (!fileInput.files || !fileInput.files[0]) return;

        const file = fileInput.files[0];
        const config = this.instances.get(componentId) || {};

        // Validate file
        const validation = this.validateFile(file, config);
        if (!validation.valid) {
            this.showNotification('error', 'Invalid File', validation.message);
            fileInput.value = '';
            return;
        }

        // Show loading state
        this.setLoadingState(componentId, true);

        // Create FileReader for preview
        const reader = new FileReader();
        reader.onload = (e) => {
            this.setImagePreview(componentId, e.target.result);
            this.updateFormInputs(componentId, null, file);
            this.setLoadingState(componentId, false);
            this.showNotification('success', 'Image Uploaded!', 'Image preview updated.');
        };

        reader.onerror = () => {
            this.setLoadingState(componentId, false);
            this.showNotification('error', 'Upload Error', 'Failed to read the selected file.');
            fileInput.value = '';
        };

        reader.readAsDataURL(file);
    }

    /**
     * Validate uploaded file
     * @param {File} file - File to validate
     * @param {Object} config - Component configuration
     * @returns {Object} Validation result
     */
    static validateFile(file, config) {
        // Check file type
        if (!file.type.startsWith('image/')) {
            return { valid: false, message: 'Please select an image file (PNG, JPG, GIF).' };
        }

        // Check allowed types if specified
        if (config.allowedTypes && !config.allowedTypes.includes(file.type)) {
            return { valid: false, message: 'File type not allowed. Please select a valid image format.' };
        }

        // Check file size
        const maxSize = config.maxFileSize || 5 * 1024 * 1024;
        if (file.size > maxSize) {
            const maxSizeMB = Math.round(maxSize / (1024 * 1024));
            return { valid: false, message: `File size too large. Please select an image smaller than ${maxSizeMB}MB.` };
        }

        return { valid: true };
    }

    /**
     * Set image preview
     * @param {string} componentId - Component identifier
     * @param {string} imageUrl - Image URL or data URL
     */
    static setImagePreview(componentId, imageUrl) {
        const uploadArea = document.getElementById(componentId + '_upload_area');
        const uploadContent = document.getElementById(componentId + '_upload_content');
        const imagePreview = document.getElementById(componentId + '_image_preview');
        const previewImg = document.getElementById(componentId + '_preview_img');

        if (!uploadArea || !previewImg) return;

        previewImg.src = imageUrl;

        if (uploadContent) uploadContent.style.display = 'none';
        if (imagePreview) imagePreview.style.display = 'block';

        uploadArea.classList.add('has-image');
    }

    /**
     * Clear image preview
     * @param {string} componentId - Component identifier
     * @param {boolean} showNotification - Whether to show success notification
     */
    static clearImage(componentId, showNotification = true) {
        const uploadArea = document.getElementById(componentId + '_upload_area');
        const uploadContent = document.getElementById(componentId + '_upload_content');
        const imagePreview = document.getElementById(componentId + '_image_preview');
        const previewImg = document.getElementById(componentId + '_preview_img');
        const fileInput = document.querySelector(`input[data-component-id="${componentId}"]`);

        if (!uploadArea) return;

        // Clear preview
        if (previewImg) previewImg.src = '';
        if (imagePreview) imagePreview.style.display = 'none';
        if (uploadContent) uploadContent.style.display = 'block';

        uploadArea.classList.remove('has-image');

        // Clear form inputs
        this.updateFormInputs(componentId, null, null);
        if (fileInput) fileInput.value = '';

        if (showNotification) {
            this.showNotification('success', 'Image Removed!', 'Image has been removed.');
        }
    }

    /**
     * Update form inputs
     * @param {string} componentId - Component identifier
     * @param {string|null} imageUrl - Image URL
     * @param {File|null} file - File object
     */
    static updateFormInputs(componentId, imageUrl, file) {
        const urlInput = document.getElementById(componentId + '_url_input');
        const oldInput = document.getElementById(componentId + '_old_input');

        if (urlInput) {
            urlInput.value = imageUrl || '';
        }

        // Don't clear old input when setting new image, let backend handle it
        if (imageUrl && oldInput && !oldInput.value) {
            oldInput.value = imageUrl;
        }
    }

    /**
     * Set loading state
     * @param {string} componentId - Component identifier
     * @param {boolean} loading - Loading state
     */
    static setLoadingState(componentId, loading) {
        const uploadArea = document.getElementById(componentId + '_upload_area');
        if (!uploadArea) return;

        if (loading) {
            uploadArea.classList.add('loading');
        } else {
            uploadArea.classList.remove('loading');
        }
    }

    /**
     * Clean up modal state
     */
    static cleanupModal() {
        this.selectedFile = null;
        this.currentComponentId = null;

        const selectBtn = document.getElementById('reusableSelectImageBtn');
        if (selectBtn) {
            selectBtn.disabled = true;
        }
    }

    /**
     * Show notification
     * @param {string} type - Notification type (success, error, warning, info)
     * @param {string} title - Notification title
     * @param {string} message - Notification message
     */
    static showNotification(type, title, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            console.log(`${type.toUpperCase()}: ${title} - ${message}`);
        }
    }

    /**
     * Initialize component with existing image
     * @param {string} componentId - Component identifier
     * @param {string} imageUrl - Existing image URL
     */
    static initializeWithImage(componentId, imageUrl) {
        if (imageUrl && imageUrl.trim() !== '') {
            this.setImagePreview(componentId, imageUrl);
            this.updateFormInputs(componentId, imageUrl, null);
        }
    }

    /**
     * Reset component to initial state
     * @param {string} componentId - Component identifier
     */
    static reset(componentId) {
        this.clearImage(componentId, false);
    }

    /**
     * Destroy component instance
     * @param {string} componentId - Component identifier
     */
    static destroy(componentId) {
        this.instances.delete(componentId);
        this.reset(componentId);
    }

    /**
     * Get component configuration
     * @param {string} componentId - Component identifier
     * @returns {Object|null} Component configuration
     */
    static getConfig(componentId) {
        return this.instances.get(componentId) || null;
    }
}

// Make MediaSelector globally available
window.MediaSelector = MediaSelector;

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize all media selector components
    document.querySelectorAll('.media-selector-component').forEach(function(component) {
        const componentId = component.id;
        if (componentId) {
            MediaSelector.initialize(componentId);
        }
    });
});
