<?php

namespace App\Http\Controllers\Backend;

use App\Models\Translation;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TranslationController extends BaseController
{
    protected string $resource = 'translation';
    
    protected array $additionalPermissions = ['translation_management_access'];

    public function index(Request $request)
    {
        $query = Translation::orderBy('entity_type')->orderBy('entity_id')->orderBy('attribute');
        
        // Filter by entity type if provided
        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }
        
        // Filter by locale if provided
        if ($request->filled('locale')) {
            $query->where('locale', $request->locale);
        }
        
        // Search by value
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('value', 'like', "%{$search}%");
        }
        
        $translations = $query->paginate(15);
        $entityTypes = Translation::distinct('entity_type')->pluck('entity_type')->sort();
        $locales = Translation::distinct('locale')->pluck('locale')->sort();
        
        return view('admin.translations.index', compact('translations', 'entityTypes', 'locales'));
    }

    public function show(string $entityType, int $entityId)
    {
        $translations = Translation::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->orderBy('attribute')
            ->orderBy('locale')
            ->get()
            ->groupBy('attribute');
            
        return view('admin.translations.show', compact('translations', 'entityType', 'entityId'));
    }

    public function store(Request $request, string $entityType, int $entityId)
    {
        $request->validate([
            'attribute' => 'required|string|max:255',
            'locale' => 'required|string|max:10',
            'value' => 'required|string',
        ]);
        
        // Check if translation already exists
        $exists = Translation::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->where('attribute', $request->attribute)
            ->where('locale', $request->locale)
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Translation already exists for this entity, attribute, and locale.');
        }
        
        Translation::create([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'attribute' => $request->attribute,
            'locale' => $request->locale,
            'value' => $request->value,
        ]);
        
        return redirect()->route('admin.translations.show', [$entityType, $entityId])
            ->with('success', 'Translation created successfully.');
    }

    public function update(Request $request, Translation $translation)
    {
        $request->validate([
            'value' => 'required|string',
        ]);
        
        $translation->update([
            'value' => $request->value,
        ]);
        
        return redirect()->route('admin.translations.show', [$translation->entity_type, $translation->entity_id])
            ->with('success', 'Translation updated successfully.');
    }

    public function destroy(Translation $translation)
    {
        $entityType = $translation->entity_type;
        $entityId = $translation->entity_id;
        
        $translation->delete();
        
        return redirect()->route('admin.translations.show', [$entityType, $entityId])
            ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Get missing translations for a specific locale
     */
    public function missing(Request $request)
    {
        $locale = $request->get('locale', 'en');
        $entityType = $request->get('entity_type');
        
        // This is a complex query to find entities that don't have translations for specific attributes
        $query = "
            SELECT DISTINCT 
                t1.entity_type,
                t1.entity_id,
                t1.attribute
            FROM translations t1
            WHERE NOT EXISTS (
                SELECT 1 FROM translations t2 
                WHERE t2.entity_type = t1.entity_type 
                AND t2.entity_id = t1.entity_id 
                AND t2.attribute = t1.attribute 
                AND t2.locale = ?
            )
        ";
        
        $params = [$locale];
        
        if ($entityType) {
            $query .= " AND t1.entity_type = ?";
            $params[] = $entityType;
        }
        
        $query .= " ORDER BY t1.entity_type, t1.entity_id, t1.attribute";
        
        $missingTranslations = DB::select($query, $params);
        
        return view('admin.translations.missing', compact('missingTranslations', 'locale', 'entityType'));
    }

    /**
     * Sync translations from one locale to another
     */
    public function sync(Request $request)
    {
        $request->validate([
            'source_locale' => 'required|string|max:10',
            'target_locale' => 'required|string|max:10|different:source_locale',
            'entity_type' => 'nullable|string',
            'overwrite' => 'boolean',
        ]);
        
        $query = Translation::where('locale', $request->source_locale);
        
        if ($request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }
        
        $sourceTranslations = $query->get();
        $synced = 0;
        $skipped = 0;
        
        foreach ($sourceTranslations as $sourceTranslation) {
            $exists = Translation::where('entity_type', $sourceTranslation->entity_type)
                ->where('entity_id', $sourceTranslation->entity_id)
                ->where('attribute', $sourceTranslation->attribute)
                ->where('locale', $request->target_locale)
                ->exists();
                
            if ($exists && !$request->overwrite) {
                $skipped++;
                continue;
            }
            
            Translation::updateOrCreate(
                [
                    'entity_type' => $sourceTranslation->entity_type,
                    'entity_id' => $sourceTranslation->entity_id,
                    'attribute' => $sourceTranslation->attribute,
                    'locale' => $request->target_locale,
                ],
                [
                    'value' => $sourceTranslation->value, // In practice, you'd want to translate this
                ]
            );
            
            $synced++;
        }
        
        $message = "Synced {$synced} translations from {$request->source_locale} to {$request->target_locale}.";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} existing translations.";
        }
        
        return redirect()->route('admin.translations.index')
            ->with('success', $message);
    }

    /**
     * Export translations for a specific locale
     */
    public function export(Request $request)
    {
        $locale = $request->get('locale', 'en');
        $entityType = $request->get('entity_type');
        
        $query = Translation::where('locale', $locale);
        
        if ($entityType) {
            $query->where('entity_type', $entityType);
        }
        
        $translations = $query->get();
        
        if ($translations->isEmpty()) {
            return redirect()->back()->with('error', 'No translations found for export.');
        }
        
        $data = [];
        foreach ($translations as $translation) {
            $key = "{$translation->entity_type}.{$translation->entity_id}.{$translation->attribute}";
            $data[$key] = $translation->value;
        }
        
        $filename = "translations_{$locale}" . ($entityType ? "_{$entityType}" : '') . "_" . date('Y-m-d_H-i-s') . '.json';
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        return response()->json($data, 200, $headers, JSON_PRETTY_PRINT);
    }

    /**
     * Import translations from JSON file
     */
    public function import(Request $request)
    {
        $request->validate([
            'locale' => 'required|string|max:10',
            'file' => 'required|file|mimes:json|max:2048',
            'overwrite' => 'boolean',
        ]);
        
        try {
            $content = file_get_contents($request->file('file')->getRealPath());
            $data = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('error', 'Invalid JSON file format.');
            }
            
            $imported = 0;
            $skipped = 0;
            
            foreach ($data as $key => $value) {
                $parts = explode('.', $key);
                if (count($parts) < 3) {
                    continue; // Skip invalid keys
                }
                
                $entityType = $parts[0];
                $entityId = $parts[1];
                $attribute = implode('.', array_slice($parts, 2));
                
                $exists = Translation::where('entity_type', $entityType)
                    ->where('entity_id', $entityId)
                    ->where('attribute', $attribute)
                    ->where('locale', $request->locale)
                    ->exists();
                    
                if ($exists && !$request->overwrite) {
                    $skipped++;
                    continue;
                }
                
                Translation::updateOrCreate(
                    [
                        'entity_type' => $entityType,
                        'entity_id' => $entityId,
                        'attribute' => $attribute,
                        'locale' => $request->locale,
                    ],
                    [
                        'value' => $value,
                    ]
                );
                
                $imported++;
            }
            
            $message = "Imported {$imported} translations for locale {$request->locale}.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} existing translations.";
            }
            
            return redirect()->route('admin.translations.index')
                ->with('success', $message);
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to import translations: ' . $e->getMessage());
        }
    }

    /**
     * Get translation statistics
     */
    public function statistics()
    {
        $stats = [
            'total_translations' => Translation::count(),
            'entity_types' => Translation::distinct('entity_type')->count(),
            'locales' => Translation::distinct('locale')->count(),
            'entities_with_translations' => Translation::distinct('entity_type', 'entity_id')->count(),
        ];
        
        $localeStats = Translation::selectRaw('locale, COUNT(*) as count')
            ->groupBy('locale')
            ->orderBy('count', 'desc')
            ->get();
            
        $entityTypeStats = Translation::selectRaw('entity_type, COUNT(*) as count')
            ->groupBy('entity_type')
            ->orderBy('count', 'desc')
            ->get();
        
        return view('admin.translations.statistics', compact('stats', 'localeStats', 'entityTypeStats'));
    }
}