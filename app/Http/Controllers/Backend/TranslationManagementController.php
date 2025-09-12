<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TranslationManagementController extends BaseController
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        parent::__construct();
        $this->translationService = $translationService;
        
        // Apply permissions for translation management
        $this->applyMethodPermission('translation_access', ['index', 'show']);
        $this->applyMethodPermission('translation_create', ['create', 'store']);
        $this->applyMethodPermission('translation_edit', ['edit', 'update', 'bulkUpdate']);
        $this->applyMethodPermission('translation_delete', ['destroy', 'cleanup']);
        $this->applyMethodPermission('translation_export', ['export']);
        $this->applyMethodPermission('translation_import', ['import', 'processImport']);
    }

    /**
     * Display a listing of translations
     */
    public function index(Request $request)
    {
        $query = Translation::query();

        // Filter by locale
        if ($request->filled('locale')) {
            $query->where('locale', $request->locale);
        }

        // Filter by translatable type
        if ($request->filled('type')) {
            $query->where('translatable_type', $request->type);
        }

        // Filter by field
        if ($request->filled('field')) {
            $query->where('field', 'like', '%' . $request->field . '%');
        }

        // Search in values
        if ($request->filled('search')) {
            $query->where('value', 'like', '%' . $request->search . '%');
        }

        $translations = $query->with('translatable')
            ->orderBy('translatable_type')
            ->orderBy('translatable_id')
            ->orderBy('locale')
            ->orderBy('field')
            ->paginate(50);

        $stats = $this->translationService->getTranslationStats();
        $supportedLocales = $this->translationService->getSupportedLocales();

        // Get unique translatable types
        $translatableTypes = Translation::distinct('translatable_type')
            ->pluck('translatable_type')
            ->sort()
            ->values()
            ->toArray();

        return view('admin.translations.index', compact(
            'translations',
            'stats',
            'supportedLocales',
            'translatableTypes'
        ));
    }

    /**
     * Show the form for creating a new translation
     */
    public function create()
    {
        $supportedLocales = $this->translationService->getSupportedLocales();
        
        // Get translatable models (you might want to define this in config)
        $translatableModels = [
            'App\\Models\\Product' => 'Products',
            'App\\Models\\Category' => 'Categories',
            'App\\Models\\Brand' => 'Brands',
            'App\\Models\\Page' => 'Pages',
            'App\\Models\\BlogPost' => 'Blog Posts',
            'App\\Models\\Tag' => 'Tags',
        ];

        return view('admin.translations.create', compact('supportedLocales', 'translatableModels'));
    }

    /**
     * Store a newly created translation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'translatable_type' => 'required|string',
            'translatable_id' => 'required|integer',
            'locale' => 'required|string|size:2',
            'field' => 'required|string|max:255',
            'value' => 'required|string|max:65535',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate with translation service
        $errors = $this->translationService->validateTranslation(
            $request->locale,
            $request->field,
            $request->value
        );

        if (!empty($errors)) {
            return redirect()->back()
                ->withErrors($errors)
                ->withInput();
        }

        Translation::setTranslation(
            $request->translatable_type,
            $request->translatable_id,
            $request->locale,
            $request->field,
            $request->value
        );

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation created successfully.');
    }

    /**
     * Display the specified translation
     */
    public function show(Translation $translation)
    {
        $translation->load('translatable');
        
        // Get related translations (same model and field, different locales)
        $relatedTranslations = Translation::where('translatable_type', $translation->translatable_type)
            ->where('translatable_id', $translation->translatable_id)
            ->where('field', $translation->field)
            ->where('id', '!=', $translation->id)
            ->get();

        return view('admin.translations.show', compact('translation', 'relatedTranslations'));
    }

    /**
     * Show the form for editing the translation
     */
    public function edit(Translation $translation)
    {
        $supportedLocales = $this->translationService->getSupportedLocales();
        return view('admin.translations.edit', compact('translation', 'supportedLocales'));
    }

    /**
     * Update the specified translation
     */
    public function update(Request $request, Translation $translation)
    {
        $validator = Validator::make($request->all(), [
            'locale' => 'required|string|size:2',
            'field' => 'required|string|max:255',
            'value' => 'required|string|max:65535',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Validate with translation service
        $errors = $this->translationService->validateTranslation(
            $request->locale,
            $request->field,
            $request->value
        );

        if (!empty($errors)) {
            return redirect()->back()
                ->withErrors($errors)
                ->withInput();
        }

        $translation->update($request->all());

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation updated successfully.');
    }

    /**
     * Remove the specified translation
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Show translation statistics
     */
    public function stats()
    {
        $stats = $this->translationService->getTranslationStats();
        $supportedLocales = $this->translationService->getSupportedLocales();

        return view('admin.translations.stats', compact('stats', 'supportedLocales'));
    }

    /**
     * Show missing translations
     */
    public function missing(Request $request)
    {
        $modelType = $request->get('type', 'App\\Models\\Product');
        $locale = $request->get('locale', $this->translationService->getCurrentLocale());

        $missing = $this->translationService->findMissingTranslations($modelType, $locale);
        $supportedLocales = $this->translationService->getSupportedLocales();

        return view('admin.translations.missing', compact('missing', 'supportedLocales', 'modelType', 'locale'));
    }

    /**
     * Bulk update translations
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'translations' => 'required|array',
            'translations.*.id' => 'required|exists:translations,id',
            'translations.*.value' => 'required|string|max:65535',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $count = 0;
        foreach ($request->translations as $translationData) {
            $translation = Translation::find($translationData['id']);
            if ($translation) {
                $translation->update(['value' => $translationData['value']]);
                $count++;
            }
        }

        return redirect()->back()
            ->with('success', "Updated {$count} translations successfully.");
    }

    /**
     * Export translations
     */
    public function export(Request $request)
    {
        $modelType = $request->get('type');
        $locale = $request->get('locale');

        if (!$modelType || !$locale) {
            return redirect()->back()
                ->with('error', 'Model type and locale are required for export.');
        }

        $translations = $this->translationService->exportTranslations($modelType, $locale);

        $filename = 'translations_' . str_replace('\\', '_', $modelType) . '_' . $locale . '_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($translations)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Show import form
     */
    public function import()
    {
        $supportedLocales = $this->translationService->getSupportedLocales();
        
        $translatableModels = [
            'App\\Models\\Product' => 'Products',
            'App\\Models\\Category' => 'Categories',
            'App\\Models\\Brand' => 'Brands',
            'App\\Models\\Page' => 'Pages',
            'App\\Models\\BlogPost' => 'Blog Posts',
            'App\\Models\\Tag' => 'Tags',
        ];

        return view('admin.translations.import', compact('supportedLocales', 'translatableModels'));
    }

    /**
     * Process import
     */
    public function processImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json',
            'model_type' => 'required|string',
            'locale' => 'required|string|size:2',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $content = file_get_contents($request->file('file')->path());
            $translations = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format');
            }

            $count = $this->translationService->importTranslations(
                $request->model_type,
                $translations,
                $request->locale
            );

            return redirect()->route('admin.translations.index')
                ->with('success', "Imported {$count} translations successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup empty translations
     */
    public function cleanup()
    {
        $count = $this->translationService->cleanupTranslations();

        return redirect()->back()
            ->with('success', "Cleaned up {$count} empty translations.");
    }

    /**
     * Duplicate locale
     */
    public function duplicateLocale(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_locale' => 'required|string|size:2',
            'target_locale' => 'required|string|size:2|different:source_locale',
            'model_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $count = $this->translationService->duplicateLocale(
            $request->source_locale,
            $request->target_locale,
            $request->model_type
        );

        return redirect()->back()
            ->with('success', "Duplicated {$count} translations from {$request->source_locale} to {$request->target_locale}.");
    }
}