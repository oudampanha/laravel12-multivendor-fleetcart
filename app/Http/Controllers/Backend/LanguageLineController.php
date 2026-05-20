<?php

namespace App\Http\Controllers\Backend;

use App\Models\LanguageLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LanguageLineController extends BaseController
{
    protected string $resource = 'language_line';

    protected array $additionalPermissions = ['translation_management_access'];

    public function index(Request $request)
    {
        $query = LanguageLine::orderBy('group')->orderBy('key');

        // Filter by group if provided
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        // Search by key or text
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('text', 'like', "%{$search}%");
            });
        }

        $languageLines = $query->paginate(15);
        $groups = LanguageLine::distinct('group')->pluck('group')->sort();

        return view('admin.language_lines.index', compact('languageLines', 'groups'));
    }

    public function create()
    {
        $groups = LanguageLine::distinct('group')->pluck('group')->sort();
        $languages = config('app.supported_locales', ['en', 'es', 'fr', 'de']);

        return view('admin.language_lines.create', compact('groups', 'languages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'text' => 'required|array',
            'text.*' => 'required|string',
        ]);

        // Check if the language line already exists
        $exists = LanguageLine::where('group', $request->group)
            ->where('key', $request->key)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Language line with this group and key already exists.');
        }

        LanguageLine::create([
            'group' => $request->group,
            'key' => $request->key,
            'text' => $request->text,
        ]);

        return redirect()->route('admin.language-lines.index')
            ->with('success', 'Language line created successfully.');
    }

    public function show(LanguageLine $languageLine)
    {
        return view('admin.language_lines.show', compact('languageLine'));
    }

    public function edit(LanguageLine $languageLine)
    {
        $groups = LanguageLine::distinct('group')->pluck('group')->sort();
        $languages = config('app.supported_locales', ['en', 'es', 'fr', 'de']);

        return view('admin.language_lines.edit', compact('languageLine', 'groups', 'languages'));
    }

    public function update(Request $request, LanguageLine $languageLine)
    {
        $request->validate([
            'group' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'text' => 'required|array',
            'text.*' => 'required|string',
        ]);

        // Check if the updated group/key combination already exists (excluding current record)
        $exists = LanguageLine::where('group', $request->group)
            ->where('key', $request->key)
            ->where('id', '!=', $languageLine->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Language line with this group and key already exists.');
        }

        $languageLine->update([
            'group' => $request->group,
            'key' => $request->key,
            'text' => $request->text,
        ]);

        return redirect()->route('admin.language-lines.index')
            ->with('success', 'Language line updated successfully.');
    }

    public function destroy(LanguageLine $languageLine)
    {
        $languageLine->delete();

        return redirect()->route('admin.language-lines.index')
            ->with('success', 'Language line deleted successfully.');
    }

    /**
     * Get language lines by group
     */
    public function byGroup(string $group)
    {
        $languageLines = LanguageLine::where('group', $group)
            ->orderBy('key')
            ->paginate(15);

        $groups = LanguageLine::distinct('group')->pluck('group')->sort();

        return view('admin.language_lines.index', compact('languageLines', 'groups', 'group'));
    }

    /**
     * Sync language lines from Laravel language files
     */
    public function syncFromFiles()
    {
        try {
            Artisan::call('translations:sync');
            $output = Artisan::output();

            return redirect()->route('admin.language-lines.index')
                ->with('success', 'Language lines synced from files successfully. '.$output);
        } catch (\Exception $e) {
            return redirect()->route('admin.language-lines.index')
                ->with('error', 'Failed to sync language lines: '.$e->getMessage());
        }
    }

    /**
     * Export language lines for a specific group
     */
    public function export(string $group)
    {
        $languageLines = LanguageLine::where('group', $group)->get();

        if ($languageLines->isEmpty()) {
            return redirect()->back()->with('error', 'No language lines found for this group.');
        }

        $data = [];
        foreach ($languageLines as $line) {
            $data[$line->key] = $line->text;
        }

        $filename = "language_lines_{$group}_".date('Y-m-d_H-i-s').'.json';
        $headers = [
            'Content-Type' => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->json($data, 200, $headers, JSON_PRETTY_PRINT);
    }

    /**
     * Import language lines from JSON file
     */
    public function import(Request $request)
    {
        $request->validate([
            'group' => 'required|string|max:255',
            'file' => 'required|file|mimes:json|max:2048',
            'overwrite' => 'boolean',
        ]);

        try {
            $content = File::get($request->file('file')->getRealPath());
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('error', 'Invalid JSON file format.');
            }

            $imported = 0;
            $skipped = 0;

            foreach ($data as $key => $text) {
                if (! is_array($text)) {
                    continue; // Skip non-array values
                }

                $exists = LanguageLine::where('group', $request->group)
                    ->where('key', $key)
                    ->exists();

                if ($exists && ! $request->overwrite) {
                    $skipped++;

                    continue;
                }

                LanguageLine::updateOrCreate(
                    [
                        'group' => $request->group,
                        'key' => $key,
                    ],
                    [
                        'text' => $text,
                    ]
                );

                $imported++;
            }

            $message = "Imported {$imported} language lines successfully.";
            if ($skipped > 0) {
                $message .= " Skipped {$skipped} existing lines.";
            }

            return redirect()->route('admin.language-lines.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to import language lines: '.$e->getMessage());
        }
    }

    /**
     * Get available language groups
     */
    public function getGroups()
    {
        $groups = LanguageLine::distinct('group')->pluck('group')->sort();

        return response()->json($groups);
    }
}
