<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'original_name',
        'file_path',
        'file_url',
        'folder_path',
        'mime_type',
        'file_extension',
        'file_size',
        'disk',
        'file_type',
        'metadata',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'entity', 'entity_media', 'file_id', 'entity_id')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'entity', 'entity_media', 'file_id', 'entity_id')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function brands(): MorphToMany
    {
        return $this->morphedByMany(Brand::class, 'entity', 'entity_media', 'file_id', 'entity_id')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function vendors(): MorphToMany
    {
        return $this->morphedByMany(Vendor::class, 'entity', 'entity_media', 'file_id', 'entity_id')
            ->withPivot('zone')
            ->withTimestamps();
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('file_type', $type);
    }

    public function scopeByExtension($query, string $extension)
    {
        return $query->where('file_extension', $extension);
    }

    public function scopeImages($query)
    {
        return $query->where('file_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('file_type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('file_type', 'document');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    public function scopeLargestFirst($query)
    {
        return $query->orderBy('file_size', 'desc');
    }

    public function scopeSmallestFirst($query)
    {
        return $query->orderBy('file_size', 'asc');
    }

    public function isImage(): bool
    {
        return $this->file_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->file_type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->file_type === 'document';
    }

    public function getUrl(): string
    {
        if ($this->file_url) {
            return $this->file_url;
        }

        return Storage::disk($this->disk)->url($this->file_path);
    }

    public function getFullPath(): string
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    public function exists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function getDimensions(): ?array
    {
        if (! $this->isImage() || ! isset($this->metadata['dimensions'])) {
            return null;
        }

        return $this->metadata['dimensions'];
    }

    public function getWidth(): ?int
    {
        $dimensions = $this->getDimensions();

        return $dimensions ? $dimensions['width'] : null;
    }

    public function getHeight(): ?int
    {
        $dimensions = $this->getDimensions();

        return $dimensions ? $dimensions['height'] : null;
    }

    public function hasMetadata(string $key): bool
    {
        return isset($this->metadata[$key]);
    }

    public function getMetadata(string $key, $default = null)
    {
        return $this->metadata[$key] ?? $default;
    }

    public function setMetadata(string $key, $value): void
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
    }

    public function delete(): bool
    {
        // Delete the physical file
        if ($this->exists()) {
            Storage::disk($this->disk)->delete($this->file_path);
        }

        // Delete the database record
        return parent::delete();
    }

    public static function createFromUpload($file, string $folder = 'uploads', ?int $userId = null): self
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        $fileName = uniqid().'.'.$extension;
        $filePath = $folder.'/'.$fileName;

        $disk = config('filesystems.default', 'local');
        $file->storeAs($folder, $fileName, $disk);

        $fileType = static::getFileType($mimeType);
        $fileUrl = Storage::disk($disk)->url($filePath);

        $metadata = [];

        // Add image dimensions if it's an image
        if ($fileType === 'image') {
            $dimensions = getimagesize($file->getPathname());
            if ($dimensions) {
                $metadata['dimensions'] = [
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                ];
            }
        }

        return static::create([
            'user_id' => $userId,
            'file_name' => $fileName,
            'original_name' => $originalName,
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'folder_path' => $folder,
            'mime_type' => $mimeType,
            'file_extension' => $extension,
            'file_size' => $size,
            'disk' => $disk,
            'file_type' => $fileType,
            'metadata' => $metadata,
        ]);
    }

    protected static function getFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'document';
    }

    /**
     * Accessor for formatted file size
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];

        if ($bytes === 0) {
            return '0 Bytes';
        }

        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2).' '.$sizes[$i];
    }

    /**
     * Accessor for full URL (fallback if file_url is empty)
     */
    public function getFullUrlAttribute()
    {
        // If file_url is already set, use it
        if ($this->file_url) {
            // Ensure URL is absolute and uses correct base URL
            if (filter_var($this->file_url, FILTER_VALIDATE_URL)) {
                // Check if URL has correct port for development
                $currentUrl = config('app.url', url('/'));
                $urlParts = parse_url($this->file_url);
                $configParts = parse_url($currentUrl);

                // If ports don't match, rebuild the URL
                if (
                    isset($configParts['port']) &&
                    (! isset($urlParts['port']) || $urlParts['port'] != $configParts['port'])
                ) {
                    $correctUrl = $configParts['scheme'].'://'.$configParts['host'];
                    if (isset($configParts['port'])) {
                        $correctUrl .= ':'.$configParts['port'];
                    }
                    $correctUrl .= $urlParts['path'];

                    return $correctUrl;
                }

                return $this->file_url;
            }
            // If it's a relative URL, make it absolute with correct base URL
            if (str_starts_with($this->file_url, '/')) {
                return config('app.url', url('/')).$this->file_url;
            }

            return config('app.url', url('/')).'/'.$this->file_url;
        }

        // Otherwise generate it from storage
        try {
            $url = Storage::disk($this->disk ?? 'public')->url($this->file_path);

            // Ensure URL uses the correct base URL from config
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                // If it's a relative URL, make it absolute
                return config('app.url', url('/')).(str_starts_with($url, '/') ? '' : '/').$url;
            }

            // Check if URL has correct port for development
            $currentUrl = config('app.url', url('/'));
            $urlParts = parse_url($url);
            $configParts = parse_url($currentUrl);

            // If ports don't match, rebuild the URL
            if (
                isset($configParts['port']) &&
                (! isset($urlParts['port']) || $urlParts['port'] != $configParts['port'])
            ) {
                $correctUrl = $configParts['scheme'].'://'.$configParts['host'];
                if (isset($configParts['port'])) {
                    $correctUrl .= ':'.$configParts['port'];
                }
                $correctUrl .= $urlParts['path'];

                return $correctUrl;
            }

            return $url;
        } catch (\Exception $e) {
            // Fallback to a basic URL construction with correct base URL
            return config('app.url', url('/')).'/storage/'.$this->file_path;
        }
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute()
    {
        return in_array($this->file_type, ['image']);
    }

    /**
     * Check if file is a video
     */
    public function getIsVideoAttribute()
    {
        return in_array($this->file_type, ['video']);
    }

    /**
     * Check if file is a document
     */
    public function getIsDocumentAttribute()
    {
        return in_array($this->file_extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);
    }

    /**
     * Get file icon based on type
     */
    public function getIconClassAttribute()
    {
        $icons = [
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc' => 'fas fa-file-word text-primary',
            'docx' => 'fas fa-file-word text-primary',
            'xls' => 'fas fa-file-excel text-success',
            'xlsx' => 'fas fa-file-excel text-success',
            'ppt' => 'fas fa-file-powerpoint text-warning',
            'pptx' => 'fas fa-file-powerpoint text-warning',
            'zip' => 'fas fa-file-archive text-warning',
            'rar' => 'fas fa-file-archive text-warning',
            'mp4' => 'fas fa-file-video text-info',
            'avi' => 'fas fa-file-video text-info',
            'mp3' => 'fas fa-file-audio text-info',
            'wav' => 'fas fa-file-audio text-info',
        ];

        if ($this->file_type === 'image') {
            return 'fas fa-file-image text-primary';
        }

        return $icons[$this->file_extension] ?? 'fas fa-file text-secondary';
    }

    /**
     * Scope a query to only include files from a specific folder
     */
    public function scopeInFolder($query, $folderPath)
    {
        if ($folderPath) {
            return $query->where('folder_path', $folderPath);
        }

        return $query->whereNull('folder_path')->orWhere('folder_path', '');
    }

    /**
     * Scope a query to only include files by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include files of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('file_type', $type);
    }

    /**
     * Move media file to a different folder
     */
    public function moveToFolder($destinationFolder = null)
    {
        $destinationFolder = $destinationFolder ?: '';
        $oldPath = $this->file_path;
        $fileName = basename($oldPath);

        // Build new path
        $newPath = 'media/'.($destinationFolder ? $destinationFolder.'/' : '').$fileName;

        // Check if destination already exists
        if (Storage::disk($this->disk)->exists($newPath)) {
            throw new \Exception('A file with this name already exists in the destination folder');
        }

        // Move the physical file
        Storage::disk($this->disk)->move($oldPath, $newPath);

        // Update database
        $this->folder_path = $destinationFolder;
        $this->file_path = $newPath;

        // Update file URL
        $newUrl = Storage::disk($this->disk)->url($newPath);
        if (! filter_var($newUrl, FILTER_VALIDATE_URL)) {
            $newUrl = config('app.url').(str_starts_with($newUrl, '/') ? '' : '/').$newUrl;
        }
        $this->file_url = $newUrl;

        return $this->save();
    }

    /**
     * Copy media file to a different folder
     */
    public function copyToFolder($destinationFolder = null)
    {
        $destinationFolder = $destinationFolder ?: '';

        // Generate unique filename for copy
        $extension = $this->file_extension;
        $newFileName = \Illuminate\Support\Str::random(20).'_'.time().'.'.$extension;
        $newPath = 'media/'.($destinationFolder ? $destinationFolder.'/' : '').$newFileName;

        // Copy the physical file
        Storage::disk($this->disk)->copy($this->file_path, $newPath);

        // Generate new URL
        $newUrl = Storage::disk($this->disk)->url($newPath);
        if (! filter_var($newUrl, FILTER_VALIDATE_URL)) {
            $newUrl = config('app.url').(str_starts_with($newUrl, '/') ? '' : '/').$newUrl;
        }

        // Create new database record
        $newMedia = $this->replicate();
        $newMedia->file_name = $newFileName;
        $newMedia->file_path = $newPath;
        $newMedia->file_url = $newUrl;
        $newMedia->folder_path = $destinationFolder;
        $newMedia->user_id = auth()->id();
        $newMedia->created_at = now();
        $newMedia->updated_at = now();

        // Update metadata
        $metadata = $this->metadata ?: [];
        $metadata['copied_from'] = $this->id;
        $metadata['copied_at'] = now()->toDateTimeString();
        $metadata['copied_by'] = auth()->user() ? auth()->user()->name : 'Unknown';
        $newMedia->metadata = $metadata;

        $newMedia->save();

        return $newMedia;
    }

    /**
     * Check if file can be moved to destination folder
     */
    public function canMoveToFolder($destinationFolder = null)
    {
        $destinationFolder = $destinationFolder ?: '';
        $fileName = basename($this->file_path);
        $newPath = 'media/'.($destinationFolder ? $destinationFolder.'/' : '').$fileName;

        return ! Storage::disk($this->disk)->exists($newPath);
    }

    /**
     * Get folder display name
     */
    public function getFolderDisplayNameAttribute()
    {
        return $this->folder_path ?: 'Root';
    }

    /**
     * Delete the file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($media) {
            // Delete the actual file from storage
            if ($media->disk && $media->file_path) {
                Storage::disk($media->disk)->delete($media->file_path);
            }
        });
    }
}
