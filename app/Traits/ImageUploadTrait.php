<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;

trait ImageUploadTrait
{
  /**
   * Upload a single image
   *
   * @param Request $request
   * @param string $inputName
   * @param string $path
   * @param string $filename
   * @param string $disk
   * @param array $allowedTypes
   * @param int $maxSize
   * @return string|null
   */
  public function uploadImage(Request $request, $inputName, $path, $filename, $disk = 'public', $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 2048)
  {
    if ($request->hasFile($inputName)) {
      try {
        $file = $request->file($inputName);
        // Validate file
        $this->validateFile($file, $allowedTypes, $maxSize);
        $ext = $file->getClientOriginalExtension();
        $generatedFilename = $filename . uniqid() . '.' . $ext;
        // Store file using Storage facade
        $filePath = $file->storeAs($path, $generatedFilename, $disk);
        return $filePath; // Return full path instead of basename
      } catch (Exception $e) {
        // Log error here if needed
        return null;
      }
    }
    return null;
  }

  /**
   * Upload multiple images
   *
   * @param Request $request
   * @param string $inputName
   * @param string $path
   * @param string $filename
   * @param string $disk
   * @param array $allowedTypes
   * @param int $maxSize
   * @return array|null
   */
  public function uploadMultiImage(Request $request, $inputName, $path, $filename, $disk = 'public', $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 2048)
  {
    if ($request->hasFile($inputName)) {
      try {
        $files = $request->file($inputName);
        $filePaths = [];
        foreach ($files as $file) {
          // Validate file
          $this->validateFile($file, $allowedTypes, $maxSize);
          $ext = $file->getClientOriginalExtension();
          $generatedFilename = $filename . uniqid() . '.' . $ext;
          // Store file using Storage facade
          $filePath = $file->storeAs($path, $generatedFilename, $disk);
          $filePaths[] = $filePath; // Return full path instead of basename
        }
        return $filePaths;
      } catch (Exception $e) {
        // Log error here if needed
        return null;
      }
    }
    return null;
  }

  /**
   * Update an image
   *
   * @param Request $request
   * @param string $inputName
   * @param string $path
   * @param string $filename
   * @param string|null $oldPath
   * @param string $disk
   * @param array $allowedTypes
   * @param int $maxSize
   * @return string|null
   */
  public function updateImage(Request $request, $inputName, $path, $filename, $oldPath = null, $disk = 'public', $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], $maxSize = 2048)
  {
    if ($request->hasFile($inputName)) {
      try {
        // Delete old file if exists
        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
          Storage::disk($disk)->delete($oldPath);
        }
        $file = $request->file($inputName);
        // Validate file
        $this->validateFile($file, $allowedTypes, $maxSize);
        $ext = $file->getClientOriginalExtension();
        $generatedFilename = $filename . uniqid() . '.' . $ext;
        // Store file using Storage facade
        $filePath = $file->storeAs($path, $generatedFilename, $disk);
        return $filePath; // Return full path instead of basename
      } catch (Exception $e) {
        // Log error here if needed
        return null;
      }
    }
    return $oldPath; // Return old path as-is if no new file was uploaded
  }

  /**
   * Delete an image
   *
   * @param string $path
   * @param string $disk
   * @return bool
   */
  public function deleteImage($path, $disk = 'public')
  {
    if (Storage::disk($disk)->exists($path)) {
      return Storage::disk($disk)->delete($path);
    }
    return false;
  }

  /**
   * Get public URL for an image
   *
   * @param string $path
   * @param string $disk
   * @return string|null
   */
  public function getImageUrl($path, $disk = 'public')
  {
    if (Storage::disk($disk)->exists($path)) {
      return Storage::disk($disk)->url($path);
    }
    return null;
  }

  /**
   * Validate file
   *
   * @param \Illuminate\Http\UploadedFile $file
   * @param array $allowedTypes
   * @param int $maxSize
   * @return void
   * @throws \Exception
   */
  protected function validateFile($file, $allowedTypes, $maxSize)
  {
    $ext = strtolower($file->getClientOriginalExtension());
    if (!in_array($ext, $allowedTypes)) {
      throw new Exception("File type not allowed. Allowed types: " . implode(', ', $allowedTypes));
    }
    if ($file->getSize() > $maxSize * 1024) {
      throw new Exception("File size exceeds maximum allowed size of {$maxSize}KB");
    }
  }
}
