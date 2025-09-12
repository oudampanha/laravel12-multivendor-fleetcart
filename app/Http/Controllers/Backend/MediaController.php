<?php

namespace App\Http\Controllers\Backend;

use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
  /**
   * Initialize and ensure media directory exists
   */
  public function __construct()
  {
    $mediaPath = storage_path('app/public/media');
    if (!File::exists($mediaPath)) {
      File::makeDirectory($mediaPath, 0755, true);
    }
  }

  /**
   * Display media management page
   */
  public function index(Request $request)
  {
    // Return the view for regular page load
    $media = Media::paginate(20);
    return view('admin.media.index', compact('media'));
  }

  /**
   * List directory contents (folders and files)
   */
  public function list(Request $request)
  {
    $path = $request->get('path', '');
    $fullPath = storage_path('app/public/media/' . $path);

    // Ensure directory exists
    if (!File::exists($fullPath)) {
      File::makeDirectory($fullPath, 0755, true);
    }

    $folders = [];
    $files = [];

    // Get folders
    if (File::exists($fullPath)) {
      $directories = File::directories($fullPath);
      foreach ($directories as $dir) {
        $folders[] = [
          'name' => basename($dir),
          'path' => $path ? $path . '/' . basename($dir) : basename($dir),
          'modified' => date('Y-m-d H:i:s', filemtime($dir))
        ];
      }
    }

    // Get files from database
    $query = Media::query();

    // Filter by folder path
    if ($path) {
      $query->where('folder_path', $path);
    } else {
      $query->whereNull('folder_path')->orWhere('folder_path', '');
    }

    // Include user information if needed
    $mediaFiles = $query->with('user')->get();

    foreach ($mediaFiles as $file) {
      $files[] = [
        'id' => $file->id,
        'name' => $file->original_name,
        'url' => $file->full_url, // Use the accessor for better URL handling
        'type' => $file->file_type, // This will be 'image', 'video', etc.
        'size' => $file->formatted_size,
        'extension' => $file->file_extension,
        'modified' => $file->created_at->format('Y-m-d H:i:s'),
        'user' => $file->user ? $file->user->name : 'Unknown',
        'is_image' => $file->is_image, // Add explicit image check
        'mime_type' => $file->mime_type, // Include MIME type for better detection
        'path' => $file->file_path // Include file path for debugging
      ];
    }

    return response()->json([
      'success' => true,
      'data' => [
        'folders' => $folders,
        'files' => $files
      ]
    ]);
  }

  /**
   * Create a new folder
   */
  public function createFolder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|regex:/^[a-zA-Z0-9_-]+$/',
      'path' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid folder name. Use only letters, numbers, hyphens, and underscores.'
      ], 400);
    }

    $path = $request->get('path', '');
    $folderName = $request->get('name');

    // Build full folder path
    $relativePath = $path ? $path . '/' . $folderName : $folderName;
    $folderPath = storage_path('app/public/media/' . $relativePath);

    if (File::exists($folderPath)) {
      return response()->json([
        'success' => false,
        'message' => 'Folder already exists'
      ], 400);
    }

    try {
      File::makeDirectory($folderPath, 0755, true);

      return response()->json([
        'success' => true,
        'message' => 'Folder created successfully'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to create folder: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Rename a folder
   */
  public function renameFolder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'path' => 'required|string',
      'new_name' => 'required|regex:/^[a-zA-Z0-9_-]+$/'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid folder name'
      ], 400);
    }

    $oldPath = storage_path('app/public/media/' . $request->path);
    $parentPath = dirname($oldPath);
    $newPath = $parentPath . '/' . $request->new_name;

    if (!File::exists($oldPath)) {
      return response()->json([
        'success' => false,
        'message' => 'Folder not found'
      ], 404);
    }

    if (File::exists($newPath)) {
      return response()->json([
        'success' => false,
        'message' => 'A folder with this name already exists'
      ], 400);
    }

    try {
      File::move($oldPath, $newPath);

      // Update database records for files in this folder
      $oldRelativePath = str_replace(storage_path('app/public/media/'), '', $oldPath);
      $newRelativePath = str_replace(storage_path('app/public/media/'), '', $newPath);

      Media::where('folder_path', 'LIKE', $oldRelativePath . '%')
        ->update([
          'folder_path' => \DB::raw("REPLACE(folder_path, '$oldRelativePath', '$newRelativePath')")
        ]);

      return response()->json([
        'success' => true,
        'message' => 'Folder renamed successfully'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to rename folder: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Delete a folder
   */
  public function deleteFolder(Request $request)
  {
    $path = $request->get('path');

    if (!$path) {
      return response()->json([
        'success' => false,
        'message' => 'Path is required'
      ], 400);
    }

    $folderPath = storage_path('app/public/media/' . $path);

    if (!File::exists($folderPath)) {
      return response()->json([
        'success' => false,
        'message' => 'Folder not found'
      ], 404);
    }

    try {
      // Check if folder is empty
      $files = File::files($folderPath);
      $dirs = File::directories($folderPath);

      if (count($files) > 0 || count($dirs) > 0) {
        return response()->json([
          'success' => false,
          'message' => 'Cannot delete non-empty folder'
        ], 400);
      }

      File::deleteDirectory($folderPath);

      return response()->json([
        'success' => true,
        'message' => 'Folder deleted successfully'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to delete folder: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Rename a file
   */
  public function renameFile(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'id' => 'required|exists:media,id',
      'new_name' => 'required|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid request'
      ], 400);
    }

    try {
      $media = Media::findOrFail($request->id);

      // Keep the extension from the original file
      $extension = $media->file_extension;
      $newName = pathinfo($request->new_name, PATHINFO_FILENAME) . '.' . $extension;

      $media->original_name = $newName;
      $media->save();

      return response()->json([
        'success' => true,
        'message' => 'File renamed successfully'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to rename file: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Upload a single file
   */
  public function upload(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'file' => 'required|file|max:10240', // Max 10MB
      'path' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

    try {
      $file = $request->file('file');
      $folderPath = $request->get('path', '');
      $disk = 'public'; // Default disk

      // Generate unique filename
      $extension = $file->getClientOriginalExtension();
      $fileName = Str::random(20) . '_' . time() . '.' . $extension;
      $originalName = $file->getClientOriginalName();

      // Determine file type and mime type
      $mimeType = $file->getMimeType();
      $fileType = explode('/', $mimeType)[0]; // image, video, application, etc.

      // Determine storage path
      $storagePath = 'media/' . ($folderPath ? $folderPath . '/' : '') . $fileName;

      // Store file
      $path = $file->storeAs(
        dirname($storagePath),
        basename($storagePath),
        $disk
      );

      // Generate file URL with correct base URL
      $fileUrl = Storage::disk($disk)->url($path);

      // Ensure URL uses correct base URL for development
      if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
        $fileUrl = config('app.url') . (str_starts_with($fileUrl, '/') ? '' : '/') . $fileUrl;
      } else {
        // Check if URL has correct port for development
        $currentUrl = config('app.url');
        $urlParts = parse_url($fileUrl);
        $configParts = parse_url($currentUrl);

        if (
          isset($configParts['port']) &&
          (!isset($urlParts['port']) || $urlParts['port'] != $configParts['port'])
        ) {
          $correctUrl = $configParts['scheme'] . '://' . $configParts['host'];
          if (isset($configParts['port'])) {
            $correctUrl .= ':' . $configParts['port'];
          }
          $correctUrl .= $urlParts['path'];
          $fileUrl = $correctUrl;
        }
      }

      // Save to database
      $media = Media::create([
        'file_name' => $fileName,
        'original_name' => $originalName,
        'file_path' => $path,
        'file_url' => $fileUrl,
        'folder_path' => $folderPath,
        'mime_type' => $mimeType,
        'file_extension' => $extension,
        'file_size' => $file->getSize(),
        'disk' => $disk,
        'file_type' => $fileType,
        'user_id' => Auth::id(),
        'metadata' => [
          'uploaded_by' => Auth::user() ? Auth::user()->name : 'Guest',
          'ip_address' => $request->ip(),
          'user_agent' => $request->userAgent(),
          'upload_date' => now()->toDateTimeString()
        ]
      ]);

      return response()->json([
        'success' => true,
        'message' => 'File uploaded successfully!',
        'data' => [
          'id' => $media->id,
          'url' => $media->file_url,
          'name' => $media->original_name,
          'size' => $media->formatted_size,
          'extension' => $media->file_extension
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to upload file: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Upload multiple files
   */
  public function bulkUpload(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'files' => 'required|array',
      'files.*' => 'file|max:10240',
      'path' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

    $uploadedFiles = [];
    $failedFiles = [];
    $folderPath = $request->get('path', '');
    $disk = 'public';

    foreach ($request->file('files') as $file) {
      try {
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(20) . '_' . time() . '_' . uniqid() . '.' . $extension;

        // Determine storage path
        $storagePath = 'media/' . ($folderPath ? $folderPath . '/' : '') . $fileName;

        $path = $file->storeAs(
          dirname($storagePath),
          basename($storagePath),
          $disk
        );

        // Generate file URL with correct base URL
        $fileUrl = Storage::disk($disk)->url($path);

        // Ensure URL uses correct base URL for development
        if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
          $fileUrl = config('app.url') . (str_starts_with($fileUrl, '/') ? '' : '/') . $fileUrl;
        } else {
          // Check if URL has correct port for development
          $currentUrl = config('app.url');
          $urlParts = parse_url($fileUrl);
          $configParts = parse_url($currentUrl);

          if (
            isset($configParts['port']) &&
            (!isset($urlParts['port']) || $urlParts['port'] != $configParts['port'])
          ) {
            $correctUrl = $configParts['scheme'] . '://' . $configParts['host'];
            if (isset($configParts['port'])) {
              $correctUrl .= ':' . $configParts['port'];
            }
            $correctUrl .= $urlParts['path'];
            $fileUrl = $correctUrl;
          }
        }

        $mimeType = $file->getMimeType();

        $media = Media::create([
          'file_name' => $fileName,
          'original_name' => $file->getClientOriginalName(),
          'file_path' => $path,
          'file_url' => $fileUrl,
          'folder_path' => $folderPath,
          'mime_type' => $mimeType,
          'file_extension' => $extension,
          'file_size' => $file->getSize(),
          'disk' => $disk,
          'file_type' => explode('/', $mimeType)[0],
          'user_id' => Auth::id(),
          'metadata' => [
            'uploaded_by' => Auth::user() ? Auth::user()->name : 'Guest',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'upload_date' => now()->toDateTimeString()
          ]
        ]);

        $uploadedFiles[] = [
          'id' => $media->id,
          'name' => $media->original_name,
          'url' => $media->file_url,
          'extension' => $media->file_extension
        ];
      } catch (\Exception $e) {
        $failedFiles[] = [
          'name' => $file->getClientOriginalName(),
          'error' => $e->getMessage()
        ];
      }
    }

    return response()->json([
      'success' => true,
      'uploaded' => $uploadedFiles,
      'failed' => $failedFiles,
      'message' => sprintf(
        'Uploaded: %d files, Failed: %d files',
        count($uploadedFiles),
        count($failedFiles)
      )
    ]);
  }

  /**
   * Delete a file
   */
  public function delete($id)
  {
    try {
      $media = Media::findOrFail($id);

      // Delete file from storage
      Storage::disk($media->disk)->delete($media->file_path);

      // Delete from database
      $media->delete();

      return response()->json([
        'success' => true,
        'message' => 'File deleted successfully!'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to delete file: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Move file to folder
   */
  public function moveToFolder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'file_id' => 'required|exists:media,id',
      'destination_folder' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid request data'
      ], 400);
    }

    try {
      $media = Media::findOrFail($request->file_id);
      $destinationFolder = $request->destination_folder ?: '';

      // Get old file path info
      $oldPath = $media->file_path;
      $fileName = basename($oldPath);

      // Build new path
      $newPath = 'media/' . ($destinationFolder ? $destinationFolder . '/' : '') . $fileName;

      // Check if destination already exists
      if (Storage::disk($media->disk)->exists($newPath)) {
        return response()->json([
          'success' => false,
          'message' => 'A file with this name already exists in the destination folder'
        ], 400);
      }

      // Move the physical file
      Storage::disk($media->disk)->move($oldPath, $newPath);

      // Update database
      $media->folder_path = $destinationFolder;
      $media->file_path = $newPath;

      // Update file URL
      $newUrl = Storage::disk($media->disk)->url($newPath);
      if (!filter_var($newUrl, FILTER_VALIDATE_URL)) {
        $newUrl = config('app.url') . (str_starts_with($newUrl, '/') ? '' : '/') . $newUrl;
      }
      $media->file_url = $newUrl;

      $media->save();

      return response()->json([
        'success' => true,
        'message' => 'File moved successfully!',
        'data' => [
          'id' => $media->id,
          'new_path' => $newPath,
          'new_folder' => $destinationFolder,
          'new_url' => $media->file_url
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to move file: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Copy file to folder
   */
  public function copyToFolder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'file_id' => 'required|exists:media,id',
      'destination_folder' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid request data'
      ], 400);
    }

    try {
      $originalMedia = Media::findOrFail($request->file_id);
      $destinationFolder = $request->destination_folder ?: '';

      // Get original file info
      $originalPath = $originalMedia->file_path;
      $extension = $originalMedia->file_extension;

      // Generate unique filename for copy
      $newFileName = Str::random(20) . '_' . time() . '.' . $extension;
      $newPath = 'media/' . ($destinationFolder ? $destinationFolder . '/' : '') . $newFileName;

      // Copy the physical file
      Storage::disk($originalMedia->disk)->copy($originalPath, $newPath);

      // Generate new URL
      $newUrl = Storage::disk($originalMedia->disk)->url($newPath);
      if (!filter_var($newUrl, FILTER_VALIDATE_URL)) {
        $newUrl = config('app.url') . (str_starts_with($newUrl, '/') ? '' : '/') . $newUrl;
      }

      // Create new database record
      $newMedia = $originalMedia->replicate();
      $newMedia->file_name = $newFileName;
      $newMedia->file_path = $newPath;
      $newMedia->file_url = $newUrl;
      $newMedia->folder_path = $destinationFolder;
      $newMedia->user_id = Auth::id();
      $newMedia->created_at = now();
      $newMedia->updated_at = now();

      // Update metadata
      $metadata = $originalMedia->metadata ?: [];
      $metadata['copied_from'] = $originalMedia->id;
      $metadata['copied_at'] = now()->toDateTimeString();
      $metadata['copied_by'] = Auth::user() ? Auth::user()->name : 'Unknown';
      $newMedia->metadata = $metadata;

      $newMedia->save();

      return response()->json([
        'success' => true,
        'message' => 'File copied successfully!',
        'data' => [
          'id' => $newMedia->id,
          'name' => $newMedia->original_name,
          'path' => $newPath,
          'folder' => $destinationFolder,
          'url' => $newMedia->file_url,
          'size' => $newMedia->formatted_size
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to copy file: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get folder list for selection
   */
  public function getFolders(Request $request)
  {
    $currentPath = $request->get('exclude_path', '');
    $basePath = storage_path('app/public/media');

    try {
      $folders = $this->getFolderTree($basePath, '', $currentPath);

      return response()->json([
        'success' => true,
        'folders' => $folders
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to load folders: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Get folder tree recursively
   */
  private function getFolderTree($basePath, $currentPath = '', $excludePath = '')
  {
    $folders = [
      [
        'name' => 'Root',
        'path' => '',
        'level' => 0
      ]
    ];

    $fullPath = $basePath . ($currentPath ? '/' . $currentPath : '');

    if (File::exists($fullPath)) {
      $directories = File::directories($fullPath);

      foreach ($directories as $dir) {
        $folderName = basename($dir);
        $folderPath = $currentPath ? $currentPath . '/' . $folderName : $folderName;

        // Skip if this is the folder we want to exclude (for move operations)
        if ($excludePath && $folderPath === $excludePath) {
          continue;
        }

        $folders[] = [
          'name' => $folderName,
          'path' => $folderPath,
          'level' => substr_count($folderPath, '/') + 1
        ];

        // Get subfolders recursively
        $subfolders = $this->getFolderTree($basePath, $folderPath, $excludePath);
        // Remove the root folder from subfolders since we already have it
        array_shift($subfolders);
        $folders = array_merge($folders, $subfolders);
      }
    }

    return $folders;
  }

  /**
   * Bulk move files to folder
   */
  public function bulkMoveToFolder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'file_ids' => 'required|array|min:1',
      'file_ids.*' => 'exists:media,id',
      'destination_folder' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid request data',
        'errors' => $validator->errors()
      ], 400);
    }

    $destinationFolder = $request->destination_folder ?: '';
    $fileIds = $request->file_ids;
    $successCount = 0;
    $failedFiles = [];

    try {
      foreach ($fileIds as $fileId) {
        try {
          $media = Media::findOrFail($fileId);

          // Get old file path info
          $oldPath = $media->file_path;
          $fileName = basename($oldPath);

          // Build new path
          $newPath = 'media/' . ($destinationFolder ? $destinationFolder . '/' : '') . $fileName;

          // Check if destination already exists
          if (Storage::disk($media->disk)->exists($newPath)) {
            $failedFiles[] = [
              'id' => $fileId,
              'name' => $media->original_name,
              'error' => 'File already exists in destination folder'
            ];
            continue;
          }

          // Move the physical file
          Storage::disk($media->disk)->move($oldPath, $newPath);

          // Update database
          $media->folder_path = $destinationFolder;
          $media->file_path = $newPath;

          // Update file URL
          $newUrl = Storage::disk($media->disk)->url($newPath);
          if (!filter_var($newUrl, FILTER_VALIDATE_URL)) {
            $newUrl = config('app.url') . (str_starts_with($newUrl, '/') ? '' : '/') . $newUrl;
          }
          $media->file_url = $newUrl;

          $media->save();
          $successCount++;
        } catch (\Exception $e) {
          $media = Media::find($fileId);
          $failedFiles[] = [
            'id' => $fileId,
            'name' => $media ? $media->original_name : 'Unknown',
            'error' => $e->getMessage()
          ];
        }
      }

      $folderName = $destinationFolder ?: 'Root';
      $message = "Moved {$successCount} file(s) to \"{$folderName}\"";
      if (!empty($failedFiles)) {
        $message .= ". " . count($failedFiles) . " file(s) failed to move.";
      }

      return response()->json([
        'success' => true,
        'message' => $message,
        'data' => [
          'success_count' => $successCount,
          'failed_count' => count($failedFiles),
          'failed_files' => $failedFiles,
          'destination_folder' => $destinationFolder
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to move files: ' . $e->getMessage()
      ], 500);
    }
  }

  /**
   * Bulk copy files to folder
   */
  public function bulkCopyToFolder(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'file_ids' => 'required|array|min:1',
      'file_ids.*' => 'exists:media,id',
      'destination_folder' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => 'Invalid request data',
        'errors' => $validator->errors()
      ], 400);
    }

    $destinationFolder = $request->destination_folder ?: '';
    $fileIds = $request->file_ids;
    $successCount = 0;
    $failedFiles = [];
    $copiedFiles = [];

    try {
      foreach ($fileIds as $fileId) {
        try {
          $originalMedia = Media::findOrFail($fileId);

          // Get original file info
          $originalPath = $originalMedia->file_path;
          $extension = $originalMedia->file_extension;

          // Generate unique filename for copy
          $newFileName = Str::random(20) . '_' . time() . '_' . uniqid() . '.' . $extension;
          $newPath = 'media/' . ($destinationFolder ? $destinationFolder . '/' : '') . $newFileName;

          // Copy the physical file
          Storage::disk($originalMedia->disk)->copy($originalPath, $newPath);

          // Generate new URL
          $newUrl = Storage::disk($originalMedia->disk)->url($newPath);
          if (!filter_var($newUrl, FILTER_VALIDATE_URL)) {
            $newUrl = config('app.url') . (str_starts_with($newUrl, '/') ? '' : '/') . $newUrl;
          }

          // Create new database record
          $newMedia = $originalMedia->replicate();
          $newMedia->file_name = $newFileName;
          $newMedia->file_path = $newPath;
          $newMedia->file_url = $newUrl;
          $newMedia->folder_path = $destinationFolder;
          $newMedia->user_id = Auth::id();
          $newMedia->created_at = now();
          $newMedia->updated_at = now();

          // Update metadata
          $metadata = $originalMedia->metadata ?: [];
          $metadata['copied_from'] = $originalMedia->id;
          $metadata['copied_at'] = now()->toDateTimeString();
          $metadata['copied_by'] = Auth::user() ? Auth::user()->name : 'Unknown';
          $newMedia->metadata = $metadata;

          $newMedia->save();
          $successCount++;

          $copiedFiles[] = [
            'id' => $newMedia->id,
            'name' => $newMedia->original_name,
            'url' => $newMedia->file_url
          ];
        } catch (\Exception $e) {
          $media = Media::find($fileId);
          $failedFiles[] = [
            'id' => $fileId,
            'name' => $media ? $media->original_name : 'Unknown',
            'error' => $e->getMessage()
          ];
        }
      }

      $folderName = $destinationFolder ?: 'Root';
      $message = "Copied {$successCount} file(s) to \"{$folderName}\"";
      if (!empty($failedFiles)) {
        $message .= ". " . count($failedFiles) . " file(s) failed to copy.";
      }

      return response()->json([
        'success' => true,
        'message' => $message,
        'data' => [
          'success_count' => $successCount,
          'failed_count' => count($failedFiles),
          'failed_files' => $failedFiles,
          'copied_files' => $copiedFiles,
          'destination_folder' => $destinationFolder
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to copy files: ' . $e->getMessage()
      ], 500);
    }
  }
}
