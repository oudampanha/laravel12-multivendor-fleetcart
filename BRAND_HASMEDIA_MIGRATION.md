# Brand Controller - HasMedia & HasTranslations Migration

## Overview
Successfully migrated `BrandController` from legacy `ImageUploadTrait` to modern **HasMedia** trait with zone-based media management and proper **HasTranslations** implementation for multilingual support.

## Changes Summary

### 1. Brand Model (`app/Models/Brand.php`)
✅ **Added MEDIA_ZONES constant**
```php
const MEDIA_ZONES = [
    'logo' => 'Brand Logo',
];
```

### 2. BrandController (`app/Http/Controllers/Backend/BrandController.php`)

#### Removed Dependencies
- ❌ `use App\Traits\ImageUploadTrait;`
- ❌ `use Illuminate\Support\Facades\Storage;`
- ❌ `use ImageUploadTrait;` (trait usage)

#### Updated Methods

##### `getDataTableData()` - DataTable Logo Display
**Before:**
```php
$logo = $brand->logo
    ? '<img src="'.asset('storage/'.$brand->logo->path).'" ...'
    : '<div>No Logo</div>';
```

**After:**
```php
$logoMedia = $brand->getFirstMediaByZone('logo');
$logo = $logoMedia
    ? '<img src="'.$logoMedia->full_url.'" ...'
    : '<div>No Logo</div>';
```

##### `store()` - Create Brand with Media
**Before:**
- Used `uploadImage()` for direct file uploads
- Handled `logo_url` from media selector manually
- Stored file paths in database column

**After:**
```php
// Validation
'logo' => 'nullable|integer|exists:media,id',

// Store media using HasMedia trait
if ($request->filled('logo')) {
    $brand->setMediaForZone($request->logo, 'logo');
}
```

**Benefits:**
- ✅ Uses polymorphic `entity_media` table
- ✅ Supports zone-based organization
- ✅ No manual file path management
- ✅ Automatic relationship tracking

##### `update()` - Update Brand Media
**Before:**
- Complex file deletion logic
- URL path conversion
- Manual `Storage::delete()` calls

**After:**
```php
// Update media
if ($request->filled('logo')) {
    $brand->setMediaForZone($request->logo, 'logo');
} elseif ($request->has('logo') && is_null($request->logo)) {
    $brand->clearZone('logo');
}
```

**Benefits:**
- ✅ Automatic old media cleanup via `setMediaForZone()`
- ✅ Clear zone with `clearZone('logo')`
- ✅ No manual file deletion needed

##### `destroy()` - Delete Brand
**Before:**
```php
if ($brand->logo) {
    Storage::disk('public')->delete($brand->logo);
}
$brand->delete();
```

**After:**
```php
// HasMedia trait automatically handles entity_media deletion
$brand->delete();
```

**Benefits:**
- ✅ `bootHasMedia()` automatically deletes `entity_media` records
- ✅ No manual cleanup required

##### `show()` & `edit()` - AJAX Response Enhancement
**Added:**
```php
$logoMedia = $brand->getFirstMediaByZone('logo');
$brandData = $brand->toArray();
$brandData['logo'] = $logoMedia ? $logoMedia->full_url : null;
$brandData['logo_id'] = $logoMedia ? $logoMedia->id : null;

return response()->json([
    'success' => true,
    'brand' => $brandData,
]);
```

**Benefits:**
- ✅ Provides both URL and media ID
- ✅ Frontend can properly populate media selector
- ✅ Maintains consistent API response

##### `byStatus()` - Filter by Logo Status
**Before:**
```php
case 'with_logo':
    $query->whereNotNull('logo');
    break;
```

**After:**
```php
case 'with_logo':
    $query->whereHas('entityMedia', function ($q) {
        $q->where('zone', 'logo');
    });
    break;
```

**Benefits:**
- ✅ Queries polymorphic relationship
- ✅ Supports multiple media zones
- ✅ Database-agnostic approach

### 3. View Updates (`resources/views/admin/brands/index.blade.php`)

#### Form Input Changes
**Added:**
```blade
<!-- Hidden input for media ID (zone-based) -->
<input type="hidden" id="logoMediaId" name="logo" value="">

<x-media-selector 
    name="logo" 
    label="" 
    :required="false" 
    preview_height="200px" />
```

#### JavaScript Updates
**Edit Brand - Logo Population:**
```javascript
// Set logo media ID (zone-based approach)
$('#logoMediaId').val(brand.logo_id || '');

// Handle logo preview
if (mediaSelector && brand.logo) {
    MediaSelector.setImagePreview(componentId, brand.logo);
}
```

## HasMedia Trait Methods Used

### Read Operations
| Method | Purpose | Usage |
|--------|---------|-------|
| `getFirstMediaByZone($zone)` | Get single media | `$brand->getFirstMediaByZone('logo')` |
| `getMediaByZone($zone)` | Get all media in zone | Returns collection |
| `hasMediaInZone($zone)` | Check if media exists | Boolean check |
| `getMediaUrlForZone($zone)` | Get media URL | Returns URL string |

### Write Operations
| Method | Purpose | Usage |
|--------|---------|-------|
| `setMediaForZone($id, $zone)` | Set single media | `$brand->setMediaForZone($request->logo, 'logo')` |
| `syncMediaForZone($ids, $zone)` | Sync multiple media | For galleries |
| `clearZone($zone)` | Remove all media | `$brand->clearZone('logo')` |

## HasTranslations Implementation

### Model Configuration
```php
protected array $translatable = [
    'name',
    'description',
];

// These fields MUST NOT be in $fillable
protected $fillable = [
    'slug',
    'is_active',
];
```

### Controller Usage
```php
// Store/Update translations
if ($request->has('name')) {
    $brand->setTranslation('name', $request->name, app()->getLocale());
}
if ($request->has('description')) {
    $brand->setTranslation('description', $request->description, app()->getLocale());
}
```

### Retrieve Translations
```php
// In views or controllers
$brand->name  // Auto-resolves to current locale
$brand->getTranslation('name', 'en')  // Specific locale
```

## Database Schema

### entity_media Table
```sql
CREATE TABLE entity_media (
    id BIGINT PRIMARY KEY,
    file_id BIGINT,                -- References media.id
    entity_type VARCHAR(255),       -- App\Models\Brand
    entity_id BIGINT,              -- brand.id
    zone VARCHAR(100),             -- 'logo'
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES media(id)
);
```

### translations Table
```sql
CREATE TABLE translations (
    id BIGINT PRIMARY KEY,
    translatable_type VARCHAR(255),  -- App\Models\Brand
    translatable_id BIGINT,          -- brand.id
    locale VARCHAR(10),              -- 'en', 'km', etc.
    key VARCHAR(255),                -- 'name', 'description'
    value TEXT,                      -- Translated content
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## Migration Benefits

### Performance
- ✅ Reduced database columns (no `logo` column needed)
- ✅ Eager loading support: `$brand->load('entityMedia.media')`
- ✅ Efficient queries via relationships

### Maintainability
- ✅ Centralized media logic in trait
- ✅ Consistent API across all models
- ✅ No duplicate code

### Scalability
- ✅ Easy to add new zones (gallery, banner, etc.)
- ✅ Support multiple media per zone
- ✅ Polymorphic - works with any model

### Data Integrity
- ✅ Foreign key constraints
- ✅ Automatic cleanup on deletion
- ✅ Transaction-safe operations

## Testing Checklist

### CRUD Operations
- [ ] Create brand with logo
- [ ] Create brand without logo
- [ ] Update brand logo (replace existing)
- [ ] Update brand logo (add when none exists)
- [ ] Clear brand logo
- [ ] Delete brand (verify entity_media cleanup)

### Translations
- [ ] Create brand with name translation
- [ ] Update brand name translation
- [ ] Verify fallback to default locale
- [ ] Test multiple languages

### DataTable
- [ ] Verify logo displays correctly
- [ ] Test filter by "With Logo"
- [ ] Test filter by "Without Logo"
- [ ] Search by brand name (translations)

### Edge Cases
- [ ] Upload media, then delete from media library (orphan check)
- [ ] Rapid create/update operations
- [ ] Concurrent edits by multiple users

## Common Patterns for Other Models

### Product with Featured + Gallery
```php
// Model
const MEDIA_ZONES = [
    'featured' => 'Featured Image',
    'gallery' => 'Gallery Images',
];

// Controller
$product->setMediaForZone($request->featured, 'featured');
$product->syncMediaForZone($request->gallery, 'gallery');
```

### Category with Icon + Banner
```php
const MEDIA_ZONES = [
    'icon' => 'Category Icon',
    'banner' => 'Category Banner',
];
```

### User Avatar
```php
const MEDIA_ZONES = [
    'avatar' => 'Profile Picture',
];
```

## Troubleshooting

### Logo not displaying in DataTable
- Check `getFirstMediaByZone('logo')` returns media
- Verify `full_url` accessor in Media model
- Ensure `entity_media` records exist

### Media not attaching
- Validate media ID exists: `exists:media,id`
- Check `entity_media` table has correct columns
- Verify HasMedia trait is loaded on model

### Translations not saving
- Ensure field NOT in `$fillable` array
- Verify field IS in `$translatable` array
- Check `translations` table structure

## References

- **HasMedia Trait**: `app/Traits/HasMedia.php`
- **HasTranslations Trait**: `app/Traits/HasTranslations.php`
- **Media Model**: `app/Models/Media.php`
- **EntityMedia Model**: `app/Models/EntityMedia.php`
- **Migration Guide**: `.github/copilot-instructions.md`

---

**Migration Completed**: November 24, 2025  
**Author**: GitHub Copilot  
**Pattern**: Zone-based polymorphic media + multilingual translations
