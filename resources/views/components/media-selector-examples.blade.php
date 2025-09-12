{{-- 
    Media Selector Component Usage Examples
    
    This file demonstrates different ways to use the media-selector component
    You can reference these examples when implementing the component in your forms
--}}

<!-- Example 1: Basic Usage -->
<div class="col-md-6">
    <x-media-selector name="image" label="Image" />
</div>

<!-- Example 2: Profile Picture with Custom Settings -->
<div class="col-md-4">
    <x-media-selector 
        name="profile_image" 
        label="Profile Picture"
        :required="true"
        preview_height="200px"
        placeholder_text="Choose profile image"
        upload_text="Upload new photo"
    />
</div>

<!-- Example 3: Product Image with Larger Preview -->
<div class="col-md-8">
    <x-media-selector 
        name="product_image" 
        label="Product Image"
        preview_height="300px"
        preview_width="100%"
        max_size="10MB"
        placeholder_text="Select product image from gallery"
        upload_text="Upload product image"
    />
</div>

<!-- Example 4: Gallery Only (No Upload Button) -->
<div class="col-md-6">
    <x-media-selector 
        name="gallery_image" 
        label="Choose from Gallery"
        :show_upload="false"
        :show_gallery="true"
        placeholder_text="Browse gallery"
    />
</div>

<!-- Example 5: Upload Only (No Gallery Button) -->
<div class="col-md-6">
    <x-media-selector 
        name="upload_image" 
        label="Upload New Image"
        :show_upload="true"
        :show_gallery="false"
        upload_text="Select file to upload"
    />
</div>

<!-- Example 6: Compact Version without Remove Button -->
<div class="col-md-4">
    <x-media-selector 
        name="thumbnail" 
        label="Thumbnail"
        preview_height="120px"
        :show_remove="false"
        placeholder_text="Add thumbnail"
    />
</div>

<!-- Example 7: With Custom CSS Classes -->
<div class="col-md-6">
    <x-media-selector 
        name="banner_image" 
        label="Banner Image"
        preview_height="180px"
        container_class="border rounded p-3"
        preview_class="shadow-sm"
        placeholder_text="Select banner image"
    />
</div>

<!-- Example 8: Pre-populated with Existing Image -->
<div class="col-md-6">
    <x-media-selector 
        name="existing_image" 
        label="Existing Image"
        value="{{ $existingImageUrl ?? '' }}"
        placeholder_text="Change existing image"
    />
</div>

<!-- Example 9: Multiple File Types (if supported) -->
<div class="col-md-6">
    <x-media-selector 
        name="media_file" 
        label="Media File"
        accept="image/*,video/*"
        max_size="20MB"
        placeholder_text="Select media file"
    />
</div>

<!-- Example 10: Minimal Version -->
<div class="col-md-4">
    <x-media-selector 
        name="icon" 
        preview_height="80px"
        placeholder_text="Add icon"
        upload_text="Upload"
    />
</div>

{{-- 
    JavaScript Usage Examples:
    
    // Initialize with existing image
    MediaSelector.initializeWithImage('media_selector_abc123', '/path/to/image.jpg');
    
    // Clear image programmatically
    MediaSelector.clearImage('media_selector_abc123');
    
    // Reset component
    MediaSelector.reset('media_selector_abc123');
    
    // Get component configuration
    const config = MediaSelector.getConfig('media_selector_abc123');
    
    // Initialize with custom options
    MediaSelector.initialize('media_selector_abc123', {
        maxFileSize: 10 * 1024 * 1024, // 10MB
        allowedTypes: ['image/jpeg', 'image/png'],
        showPreview: true
    });
--}}