/**
 * MediaSelector Configuration
 * This file provides route configurations for the MediaSelector component
 */
window.MediaSelectorConfig = {
    // Default endpoint configuration
    // These should be overridden in your Blade templates with actual Laravel routes
    endpoints: {
        list: '/admin/media/list',
        upload: '/admin/media/upload',
        bulkUpload: '/admin/media/bulk-upload',
        createFolder: '/admin/media/create-folder',
        renameFolder: '/admin/media/rename-folder',
        deleteFolder: '/admin/media/delete-folder',
        renameFile: '/admin/media/rename-file',
        deleteFile: '/admin/media/delete/{id}',
        moveToFolder: '/admin/media/move-to-folder',
        copyToFolder: '/admin/media/copy-to-folder',
        bulkMoveToFolder: '/admin/media/bulk-move-to-folder',
        bulkCopyToFolder: '/admin/media/bulk-copy-to-folder',
        getFolders: '/admin/media/folders'
    },

    // Default MediaManager configuration
    mediaManagerDefaults: {
        modal: false,
        multiple: false,
        showUploadButton: true,
        showCreateFolderButton: false,
        showViewControls: true,
        showSearch: true,
        showBreadcrumb: true,
        showContextMenu: false,
        maxFileSize: 5 * 1024 * 1024, // 5MB
        acceptedTypes: 'image/*'
    }
};
