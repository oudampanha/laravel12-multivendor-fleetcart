<?php

namespace App\Http\Controllers\Backend;

use App\Models\SearchTerm;
use Illuminate\Http\Request;

class SearchTermController extends BaseController
{
    protected string $resource = 'search_term';

    protected array $additionalPermissions = ['search_term_management_access'];

    public function index()
    {
        $searchTerms = SearchTerm::orderBy('hits', 'desc')->paginate(15);

        return view('admin.search_terms.index', compact('searchTerms'));
    }

    public function create()
    {
        return view('admin.search_terms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term' => 'required|string|unique:search_terms,term',
            'results' => 'required|integer|min:0',
            'hits' => 'integer|min:0',
        ]);

        SearchTerm::create($validated);

        return redirect()->route('admin.search-terms.index')->with('success', 'Search Term created successfully.');
    }

    public function show(SearchTerm $searchTerm)
    {
        return view('admin.search_terms.show', compact('searchTerm'));
    }

    public function edit(SearchTerm $searchTerm)
    {
        return view('admin.search_terms.edit', compact('searchTerm'));
    }

    public function update(Request $request, SearchTerm $searchTerm)
    {
        $validated = $request->validate([
            'term' => 'required|string|unique:search_terms,term,'.$searchTerm->id,
            'results' => 'required|integer|min:0',
            'hits' => 'integer|min:0',
        ]);

        $searchTerm->update($validated);

        return redirect()->route('admin.search-terms.index')->with('success', 'Search Term updated successfully.');
    }

    public function destroy(SearchTerm $searchTerm)
    {
        $searchTerm->delete();

        return redirect()->route('admin.search-terms.index')->with('success', 'Search Term deleted successfully.');
    }

    public function cleanup()
    {
        $deleted = SearchTerm::where('created_at', '<', now()->subDays(30))->delete();

        return redirect()->back()->with('success', "Cleanup complete. Removed {$deleted} records.");
    }

    public function export()
    {
        $searchTerms = SearchTerm::all();
        $filename = 'searchTerms_'.now()->format('Y_m_d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($searchTerms) {
            $handle = fopen('php://output', 'w');
            if ($searchTerms->isNotEmpty()) {
                fputcsv($handle, array_keys($searchTerms->first()->getAttributes()));
                foreach ($searchTerms as $row) {
                    fputcsv($handle, $row->getAttributes());
                }
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function noResults()
    {
        return redirect()->back()->with('info', 'No Results feature is available; please contact administrator for full implementation.');
    }

    public function popular()
    {
        return redirect()->back()->with('info', 'Popular feature is available; please contact administrator for full implementation.');
    }
}
