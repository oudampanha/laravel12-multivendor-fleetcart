I'll create a single migration file with a unified translations table for all translatable content. This approach uses a polymorphic relationship pattern for translations.I've successfully updated the migration to use a single unified translations table and combined everything into one migration file. Here are the key changes:

## Major Changes:

### 1. **Unified Translations Table**
Instead of separate translation tables for each entity, there's now a single `translations` table that uses a polymorphic approach:

```php
Schema::create('translations', function (Blueprint $table) {
    $table->id();
    $table->string('translatable_type');  // Model class (e.g., 'App\Models\Product')
    $table->unsignedBigInteger('translatable_id');  // ID of the record
    $table->string('locale');  // Language code (e.g., 'en', 'fr')
    $table->string('field');   // Field name (e.g., 'name', 'description')
    $table->longText('value')->nullable();  // Translated value
    $table->timestamps();
    
    $table->unique(['translatable_type', 'translatable_id', 'locale', 'field'], 'unique_translation');
    $table->index(['translatable_type', 'translatable_id'], 'translatable_index');
    $table->index('locale');
});
```

### 2. **Language Lines Table**
For general UI translations (not entity-specific):

```php
Schema::create('language_lines', function (Blueprint $table) {
    $table->id();
    $table->string('group');  // Translation group (e.g., 'auth', 'validation')
    $table->string('key')->index();  // Translation key
    $table->json('text');  // JSON object with locale => translation pairs
    $table->timestamps();
});
```

## How to Use the Unified Translations:

### Example Model Trait:
```php
trait HasTranslations
{
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
    
    public function translate($field, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        return $this->translations()
            ->where('locale', $locale)
            ->where('field', $field)
            ->first()?->value;
    }
    
    public function setTranslation($field, $value, $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        
        return $this->translations()->updateOrCreate(
            [
                'locale' => $locale,
                'field' => $field,
            ],
            ['value' => $value]
        );
    }
}
```

### Example Usage:
```php
// Get a product's name in French
$product->translate('name', 'fr');

// Set a category description in Spanish
$category->setTranslation('description', 'Descripción en español', 'es');

// Get vendor store name in current locale
$vendor->translate('store_name');
```

## Benefits of This Approach:

1. **Single Source of Truth**: All translations in one table
2. **Flexibility**: Easy to add translations to any model
3. **Performance**: Optimized indexes for fast queries
4. **Maintainability**: Simpler database structure
5. **Scalability**: Easy to add new languages
6. **Query Efficiency**: Can fetch all translations for an entity in one query

## Migration Execution:

Run the migration with:
```bash
php artisan migrate
```

## Next Steps:

1. Create a `Translation` model for the translations table
2. Add the `HasTranslations` trait to translatable models
3. Create seeders for initial data
4. Set up language middleware for locale detection

The migration maintains all multi-vendor functionality while simplifying the translation system significantly.