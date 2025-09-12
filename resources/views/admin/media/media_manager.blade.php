<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>MediaManager.js - Usage Examples</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- TinyMCE -->
  <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="container mt-5">
    <h1 class="mb-4">MediaManager.js Usage Examples</h1>

    <!-- Example 1: Basic Usage -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 1: Basic Usage</h3>
      </div>
      <div class="card-body">
        <button class="btn btn-primary" id="basicExample">Open Media Manager</button>
        <div id="selectedFiles" class="mt-3"></div>

        <pre class="mt-3"><code>// Basic initialization
const mediaManager = new MediaManager({
    endpoints: {
        list: '/media/list',
        upload: '/media/upload',
        // ... other endpoints
    },
    onSelect: function(files) {
        console.log('Selected files:', files);
    }
});

// Open the media manager
document.getElementById('basicExample').addEventListener('click', function() {
    mediaManager.open();
});</code></pre>
      </div>
    </div>

    <!-- Example 2: Single File Upload Form -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 2: Single File Upload Form</h3>
      </div>
      <div class="card-body">
        <form id="singleUploadForm">
          <div class="mb-3">
            <label class="form-label">Profile Image</label>
            <div class="input-group">
              <input type="text" class="form-control" id="profileImage" readonly>
              <button class="btn btn-outline-secondary" type="button" id="selectImage">
                Select Image
              </button>
            </div>
          </div>
          <div id="imagePreview" class="mb-3"></div>
        </form>

        <pre class="mt-3"><code>// Single file selection
const singleFileManager = new MediaManager({
    multiple: false,
    acceptedTypes: 'image/*',
    onSelect: function(files) {
        if (files.length > 0) {
            const file = files[0];
            document.getElementById('profileImage').value = file.url;
            document.getElementById('imagePreview').innerHTML =
                `&lt;img src="${file.url}" style="max-width: 200px;"&gt;`;
        }
    }
});

document.getElementById('selectImage').addEventListener('click', function() {
    singleFileManager.open();
});</code></pre>
      </div>
    </div>

    <!-- Example 3: Multiple File Selection -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 3: Multiple File Selection</h3>
      </div>
      <div class="card-body">
        <button class="btn btn-primary" id="multipleFiles">Select Multiple Files</button>
        <div id="multipleFilesList" class="mt-3"></div>

        <pre class="mt-3"><code>// Multiple file selection
const multiFileManager = new MediaManager({
    multiple: true,
    onSelect: function(files) {
        let html = '&lt;h5&gt;Selected Files:&lt;/h5&gt;&lt;ul&gt;';
        files.forEach(file => {
            html += `&lt;li&gt;${file.name} - ${file.url}&lt;/li&gt;`;
        });
        html += '&lt;/ul&gt;';
        document.getElementById('multipleFilesList').innerHTML = html;
    }
});

document.getElementById('multipleFiles').addEventListener('click', function() {
    multiFileManager.open();
});</code></pre>
      </div>
    </div>

    <!-- Example 4: jQuery Plugin -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 4: jQuery Plugin Usage</h3>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <input type="text" class="form-control media-input" placeholder="Click to select media">
        </div>
        <button class="btn btn-primary media-button">Select with jQuery</button>

        <pre class="mt-3"><code>// jQuery plugin usage
$(document).ready(function() {
    // For input fields
    $('.media-input').mediaManager({
        multiple: false,
        onSelect: function(files) {
            console.log('Files selected via jQuery:', files);
        }
    });

    // For buttons
    $('.media-button').mediaManager({
        multiple: true
    }).on('mediaSelected', function(e, files) {
        console.log('Media selected event:', files);
    });
});</code></pre>
      </div>
    </div>

    <!-- Example 5: TinyMCE Integration -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 5: TinyMCE Integration</h3>
      </div>
      <div class="card-body">
        <textarea id="tinymceEditor"></textarea>

        <pre class="mt-3"><code>// TinyMCE integration
tinymce.init({
    selector: '#tinymceEditor',
    plugins: 'image link',
    toolbar: 'mediamanager | bold italic | alignleft aligncenter',
    setup: function(editor) {
        MediaManager.tinymceIntegration(editor, {
            endpoints: {
                list: '/media/list',
                upload: '/media/upload'
            }
        });
    }
});</code></pre>
      </div>
    </div>

    <!-- Example 6: Custom Configuration -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 6: Custom Configuration</h3>
      </div>
      <div class="card-body">
        <button class="btn btn-primary" id="customConfig">Custom Media Manager</button>

        <pre class="mt-3"><code>// Custom configuration
const customManager = new MediaManager({
    // API endpoints
    endpoints: {
        list: '/api/media/list',
        upload: '/api/media/upload',
        createFolder: '/api/media/folder/create'
    },

    // UI options
    modal: true,
    multiple: true,
    maxFileSize: 5 * 1024 * 1024, // 5MB
    acceptedTypes: '.jpg,.png,.pdf',

    // Custom texts (localization)
    texts: {
        title: 'File Browser',
        upload: 'Upload Files',
        select: 'Choose Files',
        cancel: 'Close',
        noFiles: 'Empty folder'
    },

    // Callbacks
    onSelect: function(files) {
        console.log('Selected:', files);
        Swal.fire('Success', `${files.length} files selected`, 'success');
    },

    onUpload: function(response) {
        console.log('Upload complete:', response);
    },

    onError: function(error) {
        console.error('Error occurred:', error);
        Swal.fire('Error', error.message, 'error');
    },

    onClose: function() {
        console.log('Media manager closed');
    }
});

document.getElementById('customConfig').addEventListener('click', function() {
    customManager.open();
});</code></pre>
      </div>
    </div>

    <!-- Example 7: Direct Upload -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 7: Direct Upload (No Modal)</h3>
      </div>
      <div class="card-body">
        <input type="file" id="directUpload" class="form-control" multiple>
        <div id="uploadResult" class="mt-3"></div>

        <pre class="mt-3"><code>// Direct upload without modal
const uploadManager = new MediaManager({
    modal: false,
    endpoints: {
        upload: '/media/upload'
    }
});

document.getElementById('directUpload').addEventListener('change', function(e) {
    const files = e.target.files;

    // Use MediaManager's upload functionality
    uploadManager.handleFiles(files);

    // Handle response
    uploadManager.options.onUpload = function(response) {
        document.getElementById('uploadResult').innerHTML =
            `&lt;div class="alert alert-success"&gt;
                Upload successful! Uploaded ${response.data.length} files.
            &lt;/div&gt;`;
    };
});</code></pre>
      </div>
    </div>

    <!-- Example 8: Vue.js Integration -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 8: Vue.js Integration</h3>
      </div>
      <div class="card-body">
        <pre><code>// Vue.js component
&lt;template&gt;
    &lt;div&gt;
        &lt;button @click="openMediaManager" class="btn btn-primary"&gt;
            Select Media
        &lt;/button&gt;
        &lt;div v-if="selectedFile"&gt;
            Selected: {{ selectedFile . name }}
            &lt;img :src="selectedFile.url" style="max-width: 200px;"&gt;
        &lt;/div&gt;
    &lt;/div&gt;
&lt;/template&gt;

&lt;script&gt;
export default {
    data() {
        return {
            selectedFile: null,
            mediaManager: null
        }
    },

    mounted() {
        this.mediaManager = new MediaManager({
            multiple: false,
            onSelect: (files) => {
                if (files.length > 0) {
                    this.selectedFile = files[0];
                }
            }
        });
    },

    methods: {
        openMediaManager() {
            this.mediaManager.open();
        }
    }
}
&lt;/script&gt;</code></pre>
      </div>
    </div>

    <!-- Example 9: React Integration -->
    <div class="card mb-4">
      <div class="card-header">
        <h3>Example 9: React Integration</h3>
      </div>
      <div class="card-body">
        <pre><code>// React component
import React, { useEffect, useRef, useState } from 'react';

function MediaSelector() {
    const [selectedFiles, setSelectedFiles] = useState([]);
    const mediaManagerRef = useRef(null);

    useEffect(() => {
        // Initialize MediaManager
        mediaManagerRef.current = new MediaManager({
            multiple: true,
            onSelect: (files) => {
                setSelectedFiles(files);
            }
        });

        return () => {
            // Cleanup if needed
        };
    }, []);

    const handleOpenManager = () => {
        mediaManagerRef.current.open();
    };

    return (
        &lt;div&gt;
            &lt;button onClick={handleOpenManager} className="btn btn-primary"&gt;
                Select Files
            &lt;/button&gt;

            {selectedFiles.length > 0 && (
                &lt;div className="selected-files"&gt;
                    &lt;h5&gt;Selected Files:&lt;/h5&gt;
                    {selectedFiles.map(file => (
                        &lt;div key={file.id}&gt;
                            {file.name} - {file.url}
                        &lt;/div&gt;
                    ))}
                &lt;/div&gt;
            )}
        &lt;/div&gt;
    );
}

export default MediaSelector;</code></pre>
      </div>
    </div>
  </div>

  <!-- Include MediaManager.js -->
  <script src="path/to/MediaManager.js"></script>

  <script>
    // Initialize examples
    document.addEventListener('DOMContentLoaded', function() {
      // Example 1: Basic Usage
      const basicManager = new MediaManager({
        endpoints: {
          list: '/media/list',
          upload: '/media/upload',
          bulkUpload: '/media/bulk-upload',
          createFolder: '/media/create-folder'
        },
        onSelect: function(files) {
          let html = '<h5>Selected Files:</h5><ul>';
          files.forEach(file => {
            html += `<li>${file.name} - <a href="${file.url}" target="_blank">View</a></li>`;
          });
          html += '</ul>';
          document.getElementById('selectedFiles').innerHTML = html;
        }
      });

      document.getElementById('basicExample').addEventListener('click', function() {
        basicManager.open();
      });

      // Example 2: Single File
      const singleFileManager = new MediaManager({
        multiple: false,
        acceptedTypes: 'image/*',
        endpoints: {
          list: '/media/list',
          upload: '/media/upload'
        },
        onSelect: function(files) {
          if (files.length > 0) {
            const file = files[0];
            document.getElementById('profileImage').value = file.url;
            document.getElementById('imagePreview').innerHTML =
              `<img src="${file.url}" class="img-thumbnail" style="max-width: 200px;">`;
          }
        }
      });

      document.getElementById('selectImage').addEventListener('click', function() {
        singleFileManager.open();
      });

      // Example 3: Multiple Files
      const multiFileManager = new MediaManager({
        multiple: true,
        endpoints: {
          list: '/media/list',
          upload: '/media/upload'
        },
        onSelect: function(files) {
          let html = '<h5>Selected Files:</h5><div class="row">';
          files.forEach(file => {
            html += `
                            <div class="col-md-3 mb-2">
                                <div class="card">
                                    ${file.type === 'image'
                                        ? `<img src="${file.url}" class="card-img-top" style="height: 100px; object-fit: cover;">`
                                        : `<div class="card-body text-center"><i class="fas fa-file fa-3x"></i></div>`}
                                    <div class="card-body p-2">
                                        <small>${file.name}</small>
                                    </div>
                                </div>
                            </div>
                        `;
          });
          html += '</div>';
          document.getElementById('multipleFilesList').innerHTML = html;
        }
      });

      document.getElementById('multipleFiles').addEventListener('click', function() {
        multiFileManager.open();
      });

      // Example 4: jQuery Plugin
      if (typeof jQuery !== 'undefined') {
        $('.media-input').mediaManager({
          multiple: false,
          endpoints: {
            list: '/media/list',
            upload: '/media/upload'
          }
        });

        $('.media-button').mediaManager({
          multiple: true,
          endpoints: {
            list: '/media/list',
            upload: '/media/upload'
          }
        }).on('mediaSelected', function(e, files) {
          Swal.fire('Selected', `${files.length} files selected via jQuery`, 'success');
        });
      }

      // Example 5: TinyMCE
      if (typeof tinymce !== 'undefined') {
        tinymce.init({
          selector: '#tinymceEditor',
          height: 300,
          plugins: 'image link code',
          toolbar: 'mediamanager | bold italic | alignleft aligncenter alignright | code',
          setup: function(editor) {
            MediaManager.tinymceIntegration(editor, {
              endpoints: {
                list: '/media/list',
                upload: '/media/upload'
              }
            });
          }
        });
      }

      // Example 6: Custom Configuration
      const customManager = new MediaManager({
        endpoints: {
          list: '/media/list',
          upload: '/media/upload',
          createFolder: '/media/create-folder'
        },
        multiple: true,
        maxFileSize: 5 * 1024 * 1024,
        acceptedTypes: '.jpg,.png,.pdf',
        texts: {
          title: 'Custom File Browser',
          upload: 'Upload New Files',
          select: 'Choose Selected Files',
          cancel: 'Close Window',
          noFiles: 'This folder is empty'
        },
        onSelect: function(files) {
          Swal.fire({
            icon: 'success',
            title: 'Files Selected',
            text: `You selected ${files.length} file(s)`,
            timer: 2000
          });
        },
        onUpload: function(response) {
          console.log('Upload complete:', response);
        },
        onError: function(error) {
          Swal.fire('Error', error.message || 'An error occurred', 'error');
        }
      });

      document.getElementById('customConfig').addEventListener('click', function() {
        customManager.open();
      });
    });
  </script>
</body>

</html>
