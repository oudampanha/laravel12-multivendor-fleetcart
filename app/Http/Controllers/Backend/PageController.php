<?php

namespace App\Http\Controllers\Backend;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends BaseController
{
    protected string $resource = 'page';

    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|unique:pages,slug',
            'is_active' => 'boolean',
        ]);

        Page::create($request->all());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page created successfully.');
    }

    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $request->validate([
            'slug' => 'required|string|unique:pages,slug,'.$page->id,
            'is_active' => 'boolean',
        ]);

        $page->update($request->all());

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Page deleted successfully.');
    }

    public function active()
    {
        $pages = Page::where('is_active', true)->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function duplicate(Page $page)
    {
        $copy = $page->replicate();
        $copy->save();

        return redirect()->back()->with('success', 'Page duplicated successfully.');
    }

    public function inactive()
    {
        $pages = Page::where('is_active', false)->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    public function toggleStatus(Page $page)
    {
        $page->update(['is_active' => ! $page->is_active]);

        return redirect()->back()->with('success', 'Page status updated successfully.');
    }
}
