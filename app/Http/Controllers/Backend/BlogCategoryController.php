<?php

namespace App\Http\Controllers\Backend;

use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogCategoryController extends BaseController
{
    protected string $resource = 'blog_category';

    public function index()
    {
        $blogCategories = BlogCategory::withCount('posts')->paginate(15);

        return view('admin.blog-categories.index', compact('blogCategories'));
    }

    public function create()
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|unique:blog_categories,slug',
        ]);

        BlogCategory::create($request->all());

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category created successfully.');
    }

    public function show(BlogCategory $blogCategory)
    {
        $blogCategory->load('posts');

        return view('admin.blog_categories.show', compact('blogCategory'));
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog_categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $request->validate([
            'slug' => 'required|string|unique:blog_categories,slug,'.$blogCategory->id,
        ]);

        $blogCategory->update($request->all());

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category updated successfully.');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category deleted successfully.');
    }

    public function posts()
    {
        return redirect()->back()->with('info', 'Posts feature is available; please contact administrator for full implementation.');
    }
}
