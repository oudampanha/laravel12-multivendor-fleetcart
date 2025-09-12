<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogTagController extends Controller
{
    public function index()
    {
        $blogTags = BlogTag::withCount('posts')->paginate(15);
        return view('admin.blog-tags.index', compact('blogTags'));
    }

    public function create()
    {
        return view('admin.blog-tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|unique:blog_tags,slug'
        ]);

        BlogTag::create($request->all());

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Blog tag created successfully.');
    }

    public function show(BlogTag $blogTag)
    {
        $blogTag->load('posts');
        return view('admin.blog-tags.show', compact('blogTag'));
    }

    public function edit(BlogTag $blogTag)
    {
        return view('admin.blog-tags.edit', compact('blogTag'));
    }

    public function update(Request $request, BlogTag $blogTag)
    {
        $request->validate([
            'slug' => 'required|string|unique:blog_tags,slug,' . $blogTag->id
        ]);

        $blogTag->update($request->all());

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Blog tag updated successfully.');
    }

    public function destroy(BlogTag $blogTag)
    {
        $blogTag->delete();

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Blog tag deleted successfully.');
    }
}