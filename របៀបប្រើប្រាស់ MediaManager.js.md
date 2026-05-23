# មេរៀន៖ របៀបប្រើប្រាស់ MediaManager.js សម្រាប់គ្រប់គ្រងមេឌៀ

## ១. សេចក្តីផ្តើមមេរៀន

MediaManager.js គឺជា JavaScript library មួយដែលមានមុខងារពេញលេញសម្រាប់គ្រប់គ្រង media files ក្នុង web application របស់អ្នក។ វាផ្តល់នូវ interface ដែលងាយស្រួលប្រើសម្រាប់ upload, browse, organize និង select រូបភាព និង files ផ្សេងៗទៀត។ Library នេះត្រូវបានរចនាឡើងដើម្បីធ្វើការជាមួយ Laravel backend ប៉ុន្តែអាចប្រើជាមួយ framework ផ្សេងៗបានដែរ។

## ២. គោលបំណងមេរៀន

បន្ទាប់ពីរៀនមេរៀននេះចប់ អ្នកនឹងអាច៖

- ✅ យល់ពីរចនាសម្ព័ន្ធ និងមុខងារសំខាន់ៗរបស់ MediaManager.js
- ✅ តម្លើង និង configure MediaManager ក្នុង project របស់អ្នក
- ✅ ប្រើ MediaManager ទាំងក្នុងទម្រង់ modal និង inline mode
- ✅ Customize interface និង features តាមតម្រូវការ
- ✅ បង្កើត media selector component សម្រាប់ forms
- ✅ ភ្ជាប់ជាមួយ backend API endpoints
- ✅ ដោះស្រាយបញ្ហាទូទៅដែលអាចកើតឡើង

## ៣. មេរៀនពេញលេញ

### ៣.១ ការណែនាំអំពី MediaManager.js

MediaManager.js version 2.0.0 ផ្តល់នូវ៖

**Features សំខាន់ៗ៖**

- 📁 **Folder Navigation** - បង្កើត និង navigate folders
- 📤 **File Upload** - Upload តែមួយ ឬច្រើន files ក្នុងពេលតែមួយ
- 🔍 **Search** - ស្វែងរក files តាមឈ្មោះ
- 📱 **Responsive Design** - ដំណើរការល្អលើគ្រប់ឧបករណ៍
- 🎨 **Multiple Views** - Grid និង List view
- 🖱️ **Context Menu** - Right-click menu សម្រាប់ actions
- ✏️ **File Management** - Rename, delete, copy URL
- 🎯 **Bulk Operations** - Select និង delete ច្រើន files ក្នុងពេលតែមួយ

### ៣.២ ការតម្លើង MediaManager

#### Step 1: បញ្ចូល Dependencies

```html
<!-- CSS -->
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
/>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/path/to/MediaManager.js"></script>
```

#### Step 2: បង្កើត HTML Container

```html
<!-- សម្រាប់ Inline Mode -->
<div id="mediaContainer"></div>

<!-- សម្រាប់ Modal Mode -->
<button id="openMediaManager">Open Media Manager</button>
```

#### Step 3: Initialize MediaManager

```javascript
// Inline mode - បង្ហាញក្នុង container
const mediaManager = new MediaManager({
  container: document.getElementById("mediaContainer"),
  endpoints: {
    list: "/api/media/list",
    upload: "/api/media/upload",
    deleteFile: "/api/media/delete/{id}",
  },
  onSelect: function (files) {
    console.log("Selected files:", files);
  },
});

// Modal mode - បង្ហាញជា popup
const modalManager = new MediaManager({
  modal: true,
  multiple: true,
  onSelect: function (files) {
    console.log("Selected files:", files);
  },
});
```

### ៣.៣ Configuration Options ពេញលេញ

```javascript
const config = {
  // Container configuration
  container: null, // DOM element សម្រាប់ inline mode
  modal: true, // បើក/បិទ modal mode

  // API Endpoints
  endpoints: {
    list: "/media/list",
    upload: "/media/upload",
    bulkUpload: "/media/bulk-upload",
    createFolder: "/media/create-folder",
    renameFolder: "/media/rename-folder",
    deleteFolder: "/media/delete-folder",
    renameFile: "/media/rename-file",
    deleteFile: "/media/delete/{id}",
  },

  // UI Options
  multiple: false, // អនុញ្ញាតជ្រើសរើសច្រើន files
  showUploadButton: true, // បង្ហាញ upload button
  showCreateFolderButton: true, // បង្ហាញ create folder button
  showViewControls: true, // បង្ហាញ grid/list toggle
  showSearch: true, // បង្ហាញ search box
  showBreadcrumb: true, // បង្ហាញ breadcrumb navigation
  showContextMenu: true, // បើក right-click menu

  // File Options
  maxFileSize: 10485760, // 10MB in bytes
  acceptedTypes: "image/*,.pdf,.doc,.docx",

  // Callbacks
  onSelect: function (files) {}, // ពេល select files
  onUpload: function (data) {}, // បន្ទាប់ពី upload សម្រេច
  onFolderChange: function (path) {}, // ពេលប្តូរ folder
  onDelete: function (item) {}, // បន្ទាប់ពី delete
  onError: function (error) {}, // ពេលមាន error

  // Texts (សម្រាប់ localization)
  texts: {
    title: "Media Management",
    upload: "Upload",
    createFolder: "Add Folder",
    search: "Search file name...",
    noFiles: "This folder is empty",
  },

  // Theme
  theme: {
    primaryColor: "#007bff",
    successColor: "#28a745",
    dangerColor: "#dc3545",
  },
};
```

## ៤. សារៈប្រយោជនៃការប្រើប្រាស់

### ប្រយោជន៍សំខាន់ៗ៖

1. **🚀 ងាយស្រួលដំឡើង** - គ្រាន់តែ include file និង initialize
2. **🎨 Customizable** - កែប្រែ interface តាមតម្រូវការ
3. **📱 Responsive** - ដំណើរការល្អលើទូរស័ព្ទ និងកុំព្យូទ័រ
4. **🔒 Secure** - គាំទ្រ CSRF token protection
5. **⚡ Performance** - Lazy loading និង optimized rendering
6. **🌍 Localization** - ងាយស្រួលបកប្រែទៅភាសាផ្សេងៗ
7. **🔄 Reusable** - ប្រើម្តងៗក្នុង project តែមួយ

## ៥. ពន្យល់លើឧទាហរណ៍

### ឧទាហរណ៍ទី១៖ Basic Image Selector

```html
<!DOCTYPE html>
<html>
  <head>
    <title>Image Selector Example</title>
    <meta name="csrf-token" content="YOUR_CSRF_TOKEN" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    />
  </head>
  <body>
    <!-- Form with image selector -->
    <form id="productForm">
      <div class="form-group">
        <label>Product Image</label>
        <input
          type="text"
          id="productImage"
          placeholder="Click to select image"
        />
        <button type="button" id="selectImage">Choose Image</button>
      </div>

      <div id="imagePreview"></div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="MediaManager.js"></script>
    <script>
      // Initialize MediaManager
      const imageSelector = new MediaManager({
        modal: true,
        multiple: false,
        acceptedTypes: "image/*",
        endpoints: {
          list: "/api/media/list",
          upload: "/api/media/upload",
        },
        onSelect: function (files) {
          if (files.length > 0) {
            // Update input និង preview
            document.getElementById("productImage").value = files[0].url;
            document.getElementById("imagePreview").innerHTML =
              `<img src="${files[0].url}" style="max-width: 200px;">`;
          }
        },
      });

      // Open selector on button click
      document
        .getElementById("selectImage")
        .addEventListener("click", function () {
          imageSelector.open();
        });
    </script>
  </body>
</html>
```

### ឧទាហរណ៍ទី២៖ Gallery with Upload

```javascript
// បង្កើត full-featured media gallery
const gallery = new MediaManager({
  container: document.getElementById("galleryContainer"),
  modal: false, // Inline mode
  multiple: true,
  showUploadButton: true,
  showCreateFolderButton: true,
  features: {
    upload: true,
    createFolder: true,
    rename: true,
    delete: true,
    dragDrop: true,
    multiSelect: true,
  },
  onUpload: function (response) {
    Swal.fire("Success", "Files uploaded successfully!", "success");
  },
  onDelete: function (item) {
    console.log("Deleted:", item);
  },
});

// Initialize gallery
gallery.init();
```

### ឧទាហរណ៍ទី៣៖ Integration with Laravel

```php
// Laravel Controller
namespace App\Http\Controllers;

class MediaController extends Controller
{
    public function list(Request $request)
    {
        $path = $request->get('path', '');

        $files = Storage::disk('public')->files($path);
        $folders = Storage::disk('public')->directories($path);

        return response()->json([
            'success' => true,
            'data' => [
                'files' => array_map(function($file) {
                    return [
                        'id' => md5($file),
                        'name' => basename($file),
                        'url' => Storage::url($file),
                        'size' => Storage::size($file),
                        'mime_type' => Storage::mimeType($file)
                    ];
                }, $files),
                'folders' => array_map(function($folder) {
                    return [
                        'name' => basename($folder),
                        'path' => $folder
                    ];
                }, $folders)
            ]
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240'
        ]);

        $path = $request->file('file')->store('uploads', 'public');

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => [
                'url' => Storage::url($path)
            ]
        ]);
    }
}
```

## ៦. លំហាត់សម្រាប់អនុវត្ត

### លំហាត់ទី១៖ បង្កើត Product Form with Image

**ការណែនាំ៖** បង្កើត form សម្រាប់ add product ដែលមាន image selector

```html
<!-- Exercise 1: Product Form -->
<form id="addProductForm">
  <div class="form-group">
    <label>Product Name</label>
    <input type="text" name="name" required />
  </div>

  <div class="form-group">
    <label>Product Image</label>
    <div id="productImageSelector"></div>
  </div>

  <div class="form-group">
    <label>Gallery Images (Multiple)</label>
    <div id="galleryImagesSelector"></div>
  </div>

  <button type="submit">Save Product</button>
</form>

<script>
  // TODO: Initialize MediaManager សម្រាប់ទាំងពីរ selectors
  // Hint: ប្រើ different configurations សម្រាប់ single និង multiple selection
</script>
```

### លំហាត់ទី២៖ Custom File Filter

**ការណែនាំ៖** បង្កើត MediaManager ដែលបង្ហាញតែ PDF files

```javascript
// Exercise 2: PDF Document Manager
// TODO: Configure MediaManager ដើម្បី៖
// 1. Accept តែ PDF files
// 2. Show custom icon សម្រាប់ PDFs
// 3. Add download button ក្នុង context menu
```

### លំហាត់ទី៣៖ Bulk Upload Interface

**ការណែនាំ៖** បង្កើត drag-and-drop upload area

```javascript
// Exercise 3: Drag & Drop Uploader
// TODO: Create custom upload interface ដែល៖
// 1. Support drag & drop សម្រាប់ multiple files
// 2. Show upload progress
// 3. Display thumbnails មុនពេល upload
```

## ៧. បកស្រាយលំហាត់អនុវត្តទាំងអស់

### ដំណោះស្រាយលំហាត់ទី១៖

```javascript
// Product Form with Image Selectors
document.addEventListener("DOMContentLoaded", function () {
  // Single image selector សម្រាប់ main product image
  const mainImageManager = new MediaManager({
    container: document.getElementById("productImageSelector"),
    modal: false,
    multiple: false,
    showUploadButton: true,
    acceptedTypes: "image/*",
    texts: {
      title: "Select Product Image",
      noFiles: "No images available",
    },
    onSelect: function (files) {
      if (files.length > 0) {
        // Store selected image URL ក្នុង hidden input
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "product_image";
        input.value = files[0].url;
        document.getElementById("addProductForm").appendChild(input);

        // Show preview
        const preview = `
                    <div class="image-preview">
                        <img src="${files[0].url}" style="max-width: 200px;">
                        <p>${files[0].name}</p>
                    </div>
                `;
        document.getElementById("productImageSelector").innerHTML = preview;
      }
    },
  });

  // Multiple image selector សម្រាប់ gallery
  const galleryManager = new MediaManager({
    container: document.getElementById("galleryImagesSelector"),
    modal: false,
    multiple: true, // អនុញ្ញាតជ្រើសរើសច្រើនរូប
    showUploadButton: true,
    features: {
      multiSelect: true,
      dragDrop: true,
    },
    texts: {
      title: "Select Gallery Images",
      select: "Add to Gallery",
    },
    onSelect: function (files) {
      // Clear existing gallery inputs
      const existingInputs = document.querySelectorAll(
        'input[name="gallery_images[]"]',
      );
      existingInputs.forEach((input) => input.remove());

      // Add selected images
      files.forEach((file) => {
        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "gallery_images[]";
        input.value = file.url;
        document.getElementById("addProductForm").appendChild(input);
      });

      // Show gallery preview
      const previews = files
        .map(
          (file) => `
                <div class="gallery-item" style="display: inline-block; margin: 5px;">
                    <img src="${file.url}" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
            `,
        )
        .join("");

      document.getElementById("galleryImagesSelector").innerHTML = `
                <div class="gallery-preview">
                    <p>${files.length} images selected</p>
                    <div class="gallery-grid">${previews}</div>
                </div>
            `;
    },
  });

  // Initialize both managers
  mainImageManager.init();
  galleryManager.init();

  // Form submission
  document
    .getElementById("addProductForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      // Send to server
      fetch("/api/products", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire("Success", "Product added successfully!", "success");
          }
        });
    });
});
```

### ដំណោះស្រាយលំហាត់ទី២៖

```javascript
// PDF Document Manager
const pdfManager = new MediaManager({
  container: document.getElementById("pdfContainer"),
  modal: false,
  acceptedTypes: ".pdf", // Accept តែ PDF files
  showUploadButton: true,

  // Custom endpoints សម្រាប់ PDFs
  endpoints: {
    list: "/api/documents/list",
    upload: "/api/documents/upload",
    deleteFile: "/api/documents/delete/{id}",
  },

  // Custom texts
  texts: {
    title: "PDF Document Manager",
    upload: "Upload PDF",
    noFiles: "No PDF documents found",
    uploadHere: "Drag and drop PDF files here",
  },

  // Override file preview សម្រាប់ PDFs
  onSelect: function (files) {
    files.forEach((file) => {
      // Custom PDF preview
      const preview = `
                <div class="pdf-preview">
                    <i class="fas fa-file-pdf" style="font-size: 48px; color: #dc3545;"></i>
                    <h4>${file.name}</h4>
                    <div class="pdf-actions">
                        <a href="${file.url}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="${file.url}" download class="btn btn-success">
                            <i class="fas fa-download"></i> Download
                        </a>
                    </div>
                </div>
            `;

      // Display preview
      document.getElementById("pdfPreview").innerHTML = preview;
    });
  },
});

// Extend context menu សម្រាប់ PDFs
pdfManager.handleContextAction = function (action) {
  const original = MediaManager.prototype.handleContextAction;

  if (action === "download") {
    // Custom download logic សម្រាប់ PDFs
    const item = this.currentContextItem.item;

    // Create download link
    const link = document.createElement("a");
    link.href = item.url;
    link.download = item.name;
    link.click();

    // Track download
    fetch("/api/documents/track-download", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ document_id: item.id }),
    });
  } else {
    // Use original handler សម្រាប់ actions ផ្សេងទៀត
    original.call(this, action);
  }
};

pdfManager.init();
```

### ដំណោះស្រាយលំហាត់ទី៣៖

```javascript
// Advanced Drag & Drop Uploader
class AdvancedUploader {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.files = [];
    this.init();
  }

  init() {
    // Create upload interface
    this.container.innerHTML = `
            <div class="advanced-upload-area">
                <div class="upload-dropzone" id="dropzone">
                    <i class="fas fa-cloud-upload-alt fa-3x"></i>
                    <h3>Drag & Drop Files Here</h3>
                    <p>or click to browse</p>
                    <input type="file" id="fileInput" multiple style="display: none;">
                </div>
                
                <div class="file-queue" id="fileQueue"></div>
                
                <div class="upload-controls">
                    <button id="clearBtn" class="btn btn-secondary">Clear All</button>
                    <button id="uploadBtn" class="btn btn-primary">Upload All</button>
                </div>
                
                <div class="upload-progress" id="uploadProgress" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        `;

    this.bindEvents();
  }

  bindEvents() {
    const dropzone = document.getElementById("dropzone");
    const fileInput = document.getElementById("fileInput");

    // Click to browse
    dropzone.addEventListener("click", () => fileInput.click());

    // File selection
    fileInput.addEventListener("change", (e) => {
      this.handleFiles(e.target.files);
    });

    // Drag events
    dropzone.addEventListener("dragover", (e) => {
      e.preventDefault();
      dropzone.classList.add("dragover");
    });

    dropzone.addEventListener("dragleave", () => {
      dropzone.classList.remove("dragover");
    });

    dropzone.addEventListener("drop", (e) => {
      e.preventDefault();
      dropzone.classList.remove("dragover");
      this.handleFiles(e.dataTransfer.files);
    });

    // Control buttons
    document.getElementById("clearBtn").addEventListener("click", () => {
      this.clearFiles();
    });

    document.getElementById("uploadBtn").addEventListener("click", () => {
      this.uploadAll();
    });
  }

  handleFiles(fileList) {
    Array.from(fileList).forEach((file) => {
      // Validate file
      if (file.size > 10 * 1024 * 1024) {
        Swal.fire("Error", `${file.name} is too large (max 10MB)`, "error");
        return;
      }

      // Add to queue with preview
      const fileId = Date.now() + Math.random();
      const fileObj = {
        id: fileId,
        file: file,
        progress: 0,
        status: "pending",
      };

      this.files.push(fileObj);

      // Create preview
      this.createPreview(fileObj);
    });

    this.updateQueue();
  }

  createPreview(fileObj) {
    const reader = new FileReader();

    reader.onload = (e) => {
      const preview = document.createElement("div");
      preview.className = "file-preview-item";
      preview.id = `file-${fileObj.id}`;

      let thumbnail = "";
      if (fileObj.file.type.startsWith("image/")) {
        thumbnail = `<img src="${e.target.result}" class="thumbnail">`;
      } else {
        thumbnail = `<i class="fas fa-file fa-2x"></i>`;
      }

      preview.innerHTML = `
                <div class="preview-content">
                    ${thumbnail}
                    <div class="file-info">
                        <p class="file-name">${fileObj.file.name}</p>
                        <p class="file-size">${this.formatSize(fileObj.file.size)}</p>
                    </div>
                    <button class="remove-file" data-id="${fileObj.id}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="file-progress" style="display: none;">
                    <div class="progress-bar" style="width: 0%"></div>
                </div>
            `;

      document.getElementById("fileQueue").appendChild(preview);

      // Remove button
      preview.querySelector(".remove-file").addEventListener("click", (e) => {
        this.removeFile(fileObj.id);
      });
    };

    if (fileObj.file.type.startsWith("image/")) {
      reader.readAsDataURL(fileObj.file);
    } else {
      reader.onload({ target: { result: null } });
    }
  }

  removeFile(fileId) {
    this.files = this.files.filter((f) => f.id !== fileId);
    document.getElementById(`file-${fileId}`).remove();
    this.updateQueue();
  }

  clearFiles() {
    this.files = [];
    document.getElementById("fileQueue").innerHTML = "";
    this.updateQueue();
  }

  updateQueue() {
    const count = this.files.length;
    const uploadBtn = document.getElementById("uploadBtn");

    if (count > 0) {
      uploadBtn.textContent = `Upload ${count} File${count > 1 ? "s" : ""}`;
      uploadBtn.disabled = false;
    } else {
      uploadBtn.textContent = "Upload All";
      uploadBtn.disabled = true;
    }
  }

  async uploadAll() {
    if (this.files.length === 0) return;

    const progressDiv = document.getElementById("uploadProgress");
    const progressBar = progressDiv.querySelector(".progress-bar");

    progressDiv.style.display = "block";

    let completed = 0;
    const total = this.files.length;

    for (const fileObj of this.files) {
      if (fileObj.status === "completed") continue;

      // Show individual progress
      const fileDiv = document.getElementById(`file-${fileObj.id}`);
      const fileProgress = fileDiv.querySelector(".file-progress");
      const fileProgressBar = fileDiv.querySelector(".progress-bar");

      fileProgress.style.display = "block";

      // Upload file
      const formData = new FormData();
      formData.append("file", fileObj.file);

      try {
        const xhr = new XMLHttpRequest();

        // Track upload progress
        xhr.upload.addEventListener("progress", (e) => {
          if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            fileProgressBar.style.width = percentComplete + "%";
            fileObj.progress = percentComplete;
          }
        });

        // Upload complete
        xhr.addEventListener("load", () => {
          if (xhr.status === 200) {
            fileObj.status = "completed";
            fileDiv.classList.add("upload-success");
            completed++;

            // Update overall progress
            const overallProgress = (completed / total) * 100;
            progressBar.style.width = overallProgress + "%";

            if (completed === total) {
              Swal.fire(
                "Success",
                "All files uploaded successfully!",
                "success",
              );
              setTimeout(() => this.clearFiles(), 2000);
            }
          }
        });

        xhr.open("POST", "/api/media/upload");
        xhr.setRequestHeader(
          "X-CSRF-TOKEN",
          document.querySelector('meta[name="csrf-token"]').content,
        );
        xhr.send(formData);
      } catch (error) {
        fileObj.status = "error";
        fileDiv.classList.add("upload-error");
        console.error("Upload error:", error);
      }
    }
  }

  formatSize(bytes) {
    const sizes = ["Bytes", "KB", "MB", "GB"];
    if (bytes === 0) return "0 Bytes";
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return Math.round((bytes / Math.pow(1024, i)) * 100) / 100 + " " + sizes[i];
  }
}

// Initialize uploader
const uploader = new AdvancedUploader("uploaderContainer");
```

### CSS សម្រាប់ Exercises

```css
/* Styles សម្រាប់ exercises */
.advanced-upload-area {
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.upload-dropzone {
  border: 3px dashed #dee2e6;
  border-radius: 8px;
  padding: 40px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
}

.upload-dropzone.dragover {
  border-color: #007bff;
  background: #e7f3ff;
}

.file-queue {
  margin-top: 20px;
  max-height: 400px;
  overflow-y: auto;
}

.file-preview-item {
  display: flex;
  flex-direction: column;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  padding: 10px;
  margin-bottom: 10px;
}

.preview-content {
  display: flex;
  align-items: center;
  gap: 10px;
}

.thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
}

.file-info {
  flex: 1;
}

.file-name {
  font-weight: 500;
  margin: 0;
}

.file-size {
  color: #6c757d;
  font-size: 0.875rem;
  margin: 0;
}

.remove-file {
  background: #dc3545;
  color: white;
  border: none;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  cursor: pointer;
}

.file-progress {
  margin-top: 10px;
  height: 4px;
  background: #e9ecef;
  border-radius: 2px;
  overflow: hidden;
}

.file-progress .progress-bar {
  height: 100%;
  background: #007bff;
  transition: width 0.3s;
}

.upload-success {
  border-color: #28a745;
  background: #d4edda;
}

.upload-error {
  border-color: #dc3545;
  background: #f8d7da;
}

.upload-controls {
  margin-top: 20px;
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

.upload-progress {
  margin-top: 20px;
}

.progress {
  height: 20px;
  background: #e9ecef;
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(90deg, #007bff, #0056b3);
  transition: width 0.3s;
}
```

## ៨. លទ្ធផលសិក្សា

បន្ទាប់ពីបញ្ចប់មេរៀននេះ និងធ្វើលំហាត់ទាំងអស់ អ្នកនឹង៖

✅ **អាចតម្លើង MediaManager** ក្នុង project ណាមួយ
✅ **យល់ពី configuration options** ទាំងអស់
✅ **អាចបង្កើត custom file selectors** សម្រាប់ forms
✅ **ដឹងពីរបៀប integrate** ជាមួយ backend APIs
✅ **អាច customize interface** តាមតម្រូវការ
✅ **អាចដោះស្រាយបញ្ហា** ដែលអាចកើតឡើង
✅ **អាចបង្កើត advanced features** ដូចជា bulk upload

## ៩. សង្ខេបមេរៀន

MediaManager.js គឺជា library ដ៏មានអានុភាពសម្រាប់គ្រប់គ្រង media files ក្នុង web applications។ វាផ្តល់នូវ៖

### 🔑 ចំណុចសំខាន់ៗ៖

1. **Flexible Modes** - ប្រើបានទាំង modal និង inline
2. **Rich Features** - Upload, browse, organize, search
3. **Easy Integration** - ងាយស្រួល integrate ជាមួយ frameworks
4. **Customizable** - កែប្រែបានគ្រប់ aspects
5. **Responsive** - ដំណើរការល្អលើគ្រប់ devices

### 💡 Best Practices៖

- ✅ តែងតែ set CSRF token សម្រាប់ security
- ✅ Validate files ទាំង client និង server side
- ✅ ប្រើ callbacks ដើម្បី handle events
- ✅ Customize texts សម្រាប់ localization
- ✅ Test លើ devices ផ្សេងៗ

### 🚀 Next Steps៖

1. សាកល្បង integrate MediaManager ក្នុង project របស់អ្នក
2. Customize interface តាម brand របស់អ្នក
3. បង្កើត custom features តាមតម្រូវការ
4. Share feedback និង contribute improvements

MediaManager.js ធ្វើឱ្យការគ្រប់គ្រង media files ក្លាយជារឿងងាយស្រួល និងមានប្រសិទ្ធភាព។ ចាប់ផ្តើមប្រើវាថ្ងៃនេះដើម្បីពង្រឹង user experience ក្នុង application របស់អ្នក! 🎉
