<?php

namespace App\Http\Controllers\Backend;

use App\Models\SearchTerm;
use App\Http\Controllers\Backend\BaseController;
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
            'hits' => 'integer|min:0'
        ]);

        SearchTerm::create($validated);

        return redirect()->route('admin.search_terms.index')->with('success', 'Search Term created successfully.');
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
            'term' => 'required|string|unique:search_terms,term,' . $searchTerm->id,
            'results' => 'required|integer|min:0',
            'hits' => 'integer|min:0'
        ]);

        $searchTerm->update($validated);

        return redirect()->route('admin.search_terms.index')->with('success', 'Search Term updated successfully.');
    }

    public function destroy(SearchTerm $searchTerm)
    {
        $searchTerm->delete();

        return redirect()->route('admin.search_terms.index')->with('success', 'Search Term deleted successfully.');
    }
}