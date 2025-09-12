# Language Translation System Guide

This comprehensive translation system allows any model in the multi-vendor Laravel application to support multilingual content using polymorphic relationships.

## System Components

### 1. **Translation Model** (`app/Models/Translation.php`)
- Polymorphic relationship with all translatable models
- Stores translations with locale, field, and value
- Indexed for optimal performance

### 2. **HasTranslations Trait** (`app/Traits/HasTranslations.php`)
- Main trait for making models translatable
- Provides translation methods and automatic caching
- Handles fallback to default locale

### 3. **TranslationService** (`app/Services/TranslationService.php`)
- Business logic for translation management
- Bulk operations, import/export, statistics
- Validation and locale management

### 4. **TranslationManagementController** (`app/Http/Controllers/Backend/TranslationManagementController.php`)
- Backend interface for managing translations
- CRUD operations, bulk updates, import/export

## Database Structure

```sql
-- Existing translations table from migration
CREATE TABLE translations (
    id BIGINT UNSIGNED PRIMARY KEY,
    translatable_type VARCHAR(255),      -- Model class name
    translatable_id BIGINT UNSIGNED,     -- Model ID
    locale VARCHAR(2),                   -- Language code (en, es, fr, etc.)
    field VARCHAR(255),                  -- Field name being translated
    value LONGTEXT,                      -- Translated content
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    UNIQUE KEY unique_translation (translatable_type, translatable_id, locale, field),
    INDEX translatable_index (translatable_type, translatable_id),
    INDEX locale_index (locale)
);
```

## Adding Translation Support to Models

### Step 1: Use the Trait
```php
<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{
    use HasTranslations;

    protected $fillable = [
        'slug',
        'is_active',
        // ... other fields
    ];

    /**
     * Fields that can be translated
     */
    protected array $translatable = [
        'name',
        'description',
        'meta_title',
        'meta_description',
        'content',
    ];
}
```

### Step 2: Using Translations in Code

#### **Basic Translation Operations**
```php
$product = Product::find(1);

// Set translations
$product->setTranslation('name', 'Product Name', 'en');
$product->setTranslation('name', 'Nombre del Producto', 'es');
$product->setTranslation('name', 'Nom du Produit', 'fr');

// Get translations
$englishName = $product->getTranslation('name', 'en');
$spanishName = $product->getTranslation('name', 'es');

// Get current locale translation (uses App::getLocale())
$localizedName = $product->getTranslation('name');

// Set multiple translations at once
$product->setTranslations([
    'name' => 'Product Name',
    'description' => 'Product Description',
], 'en');
```

#### **Working with Locales**
```php
// Set current locale for the model instance
$product->setCurrentLocale('es');
$spanishName = $product->name; // Returns Spanish translation automatically

// Get translated version of model
$spanishProduct = $product->translateTo('es');

// Get all available locales for this model
$availableLocales = $product->getAvailableLocales(); // ['en', 'es', 'fr']

// Check if translation exists
if ($product->hasTranslation('name', 'es')) {
    echo "Spanish translation exists";
}

// Get translation completeness (percentage)
$completeness = $product->getTranslationCompleteness('es'); // 75.5
```

#### **Advanced Operations**
```php
// Get all translations for a specific locale
$allSpanishTranslations = $product->getTranslations('es');
// Returns: ['name' => '...', 'description' => '...']

// Get model with all translations
$productWithAllTranslations = $product->withAllTranslations();
// Returns: ['en' => [...], 'es' => [...], 'fr' => [...]]

// Copy translations from another model
$newProduct = new Product();
$newProduct->copyTranslationsFrom($product, ['name', 'description']);

// Delete specific translation
$product->deleteTranslation('name', 'es');

// Delete all translations for a locale
$product->deleteLocaleTranslations('es');
```

## Using the Translation Service

```php
use App\Services\TranslationService;

$translationService = app(TranslationService::class);

// Get translation statistics
$stats = $translationService->getTranslationStats();
/*
Returns:
[
    'total_translations' => 1500,
    'by_locale' => ['en' => 500, 'es' => 400, 'fr' => 300],
    'by_model' => ['App\Models\Product' => 800, 'App\Models\Category' => 400],
    'completion_rates' => ['en' => 100, 'es' => 80, 'fr' => 60]
]
*/

// Find missing translations
$missing = $translationService->findMissingTranslations('App\Models\Product', 'es');

// Auto-translate using a callback (integrate with Google Translate, DeepL, etc.)
$count = $translationService->autoTranslate(
    'App\Models\Product',
    'en',
    'es',
    function ($text, $sourceLocale, $targetLocale) {
        // Your translation API call here
        return translateText($text, $sourceLocale, $targetLocale);
    }
);

// Bulk translate multiple models
$models = Product::take(10)->get();
$fields = ['name', 'description'];
$translations = [
    1 => ['name' => 'Producto 1', 'description' => 'Descripción 1'],
    2 => ['name' => 'Producto 2', 'description' => 'Descripción 2'],
];
$count = $translationService->bulkTranslate($models, $fields, 'es', $translations);

// Export translations
$exported = $translationService->exportTranslations('App\Models\Product', 'es');

// Import translations
$count = $translationService->importTranslations('App\Models\Product', $exported, 'es');
```

## Frontend Usage

### In Blade Templates
```blade
{{-- Display translated content with fallback --}}
<h1>{{ $product->getTranslation('name') }}</h1>
<p>{{ $product->getTranslation('description') }}</p>

{{-- Display in specific locale --}}
<h1>{{ $product->getTranslation('name', 'es') }}</h1>

{{-- Check if translation exists --}}
@if($product->hasTranslation('name', 'es'))
    <p class="translated">{{ $product->getTranslation('name', 'es') }}</p>
@else
    <p class="untranslated">{{ $product->getTranslation('name', 'en') }}</p>
@endif

{{-- Show all available translations --}}
@foreach($product->getAvailableLocales() as $locale)
    <div class="translation-{{ $locale }}">
        <strong>{{ $locale }}:</strong> 
        {{ $product->getTranslation('name', $locale) }}
    </div>
@endforeach
```

### Translation Forms
```blade
{{-- Multi-language form --}}
<form method="POST" action="{{ route('products.store') }}">
    @csrf
    
    @foreach($supportedLocales as $locale)
        <div class="translation-group">
            <h4>{{ $locale }}</h4>
            
            <div class="form-group">
                <label>Name ({{ $locale }})</label>
                <input type="text" name="translations[{{ $locale }}][name]" 
                       value="{{ old('translations.'.$locale.'.name', $product->getTranslation('name', $locale)) }}">
            </div>
            
            <div class="form-group">
                <label>Description ({{ $locale }})</label>
                <textarea name="translations[{{ $locale }}][description]">{{ old('translations.'.$locale.'.description', $product->getTranslation('description', $locale)) }}</textarea>
            </div>
        </div>
    @endforeach
    
    <button type="submit">Save Translations</button>
</form>
```

### Controller Handling
```php
public function store(Request $request)
{
    $product = Product::create($request->only(['slug', 'price', 'is_active']));
    
    // Save translations
    if ($request->has('translations')) {
        foreach ($request->translations as $locale => $translations) {
            if (!empty(array_filter($translations))) {
                $product->setTranslations($translations, $locale);
            }
        }
    }
    
    return redirect()->route('products.index');
}
```

## Models Ready for Translation

Based on the migration structure, these models can immediately use translations:

### **Product Model**
```php
protected array $translatable = [
    'name',
    'description',
    'short_description',
    'meta_title',
    'meta_description',
    'meta_keywords',
    'specifications',
];
```

### **Category Model**
```php
protected array $translatable = [
    'name',
    'description',
    'meta_title',
    'meta_description',
    'meta_keywords',
];
```

### **Brand Model**
```php
protected array $translatable = [
    'name',
    'description',
];
```

### **Page Model**
```php
protected array $translatable = [
    'title',
    'content',
    'meta_title',
    'meta_description',
    'meta_keywords',
];
```

### **BlogPost Model**
```php
protected array $translatable = [
    'title',
    'content',
    'excerpt',
    'meta_title',
    'meta_description',
    'meta_keywords',
];
```

### **Tag Model**
```php
protected array $translatable = [
    'name',
    'description',
];
```

### **Attribute Model**
```php
protected array $translatable = [
    'name',
    'description',
];
```

### **Option Model**
```php
protected array $translatable = [
    'name',
    'description',
    'placeholder',
];
```

## Configuration

### Supported Locales
Add to `config/app.php`:
```php
'supported_locales' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'ar', 'zh', 'ja', 'ko'],
```

### Cache Configuration
Translations are automatically cached. Configure cache duration in the service:
```php
// In TranslationService
protected int $cacheDuration = 60; // minutes
```

## Performance Considerations

### 1. **Eager Loading Translations**
```php
// Load products with their translations
$products = Product::with('translations')->get();

// Load translations for specific locale only
$products = Product::with(['translations' => function($query) {
    $query->where('locale', app()->getLocale());
}])->get();
```

### 2. **Caching Strategy**
- Translations are automatically cached per field/locale combination
- Cache is cleared when translations are updated
- Use Redis for better cache performance in production

### 3. **Database Optimization**
- Translations table has optimal indexes for performance
- Consider partitioning by locale for very large datasets

## API Integration

### Translation API Endpoints
```php
// Get model translations
GET /api/models/{type}/{id}/translations/{locale}

// Update model translations  
PUT /api/models/{type}/{id}/translations/{locale}

// Delete model translations
DELETE /api/models/{type}/{id}/translations/{locale}

// Bulk operations
POST /api/translations/bulk-update
POST /api/translations/import
GET /api/translations/export
```

## Best Practices

1. **Always Define Translatable Fields**: Explicitly set `$translatable` array in models
2. **Use Fallbacks**: The system automatically falls back to default locale
3. **Cache Wisely**: Translation caching is built-in but monitor cache hit rates
4. **Validate Locales**: Always check if locale is supported before operations
5. **Bulk Operations**: Use service methods for bulk updates rather than individual calls
6. **SEO Considerations**: Implement proper hreflang tags using translation data

## Example: Complete Product Translation Setup

```php
// 1. Model setup
class Product extends Model 
{
    use HasTranslations;
    
    protected array $translatable = [
        'name', 'description', 'short_description', 
        'meta_title', 'meta_description', 'meta_keywords'
    ];
}

// 2. Controller
class ProductController extends Controller 
{
    public function show(Product $product, $locale = null) 
    {
        if ($locale) {
            app()->setLocale($locale);
            $product = $product->translateTo($locale);
        }
        
        return view('products.show', compact('product'));
    }
}

// 3. Route
Route::get('/products/{product}/{locale?}', [ProductController::class, 'show'])
    ->name('products.show');

// 4. View
<h1>{{ $product->getTranslation('name') }}</h1>
<div>{!! $product->getTranslation('description') !!}</div>

@foreach(['en', 'es', 'fr'] as $locale)
    <a href="{{ route('products.show', [$product, $locale]) }}">
        {{ $locale }}
    </a>
@endforeach
```

This translation system provides a complete, scalable solution for multilingual content in your Laravel multi-vendor application!