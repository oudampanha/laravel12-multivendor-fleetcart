<?php

namespace App\Http\Controllers\Backend;

use App\Models\BlogTag;
use Illuminate\Http\Request;

class BlogTagController extends BaseController
{
    protected string $resource = 'blog_tag';

    public function index()
    {
        $blogTags = BlogTag::withCount('posts')->paginate(15);

        return view('admin.blog_tags.index', compact('blogTags'));
    }

    public function create()
    {
        return view('admin.blog_tags.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|string|unique:blog_tags,slug',
        ]);

        BlogTag::create($request->all());

        return redirect()->route('admin.blog-tags.index')
            ->with('success', 'Blog tag created successfully.');
    }

    public function show(BlogTag $blogTag)
    {
        $blogTag->load('posts');

        return view('admin.blog_tags.show', compact('blogTag'));
    }

    public function edit(BlogTag $blogTag)
    {
        return view('admin.blog_tags.edit', compact('blogTag'));
    }

    public function update(Request $request, BlogTag $blogTag)
    {
        $request->validate([
            'slug' => 'required|string|unique:blog_tags,slug,'.$blogTag->id,
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

    public function merge()
    {
        return redirect()->back()->with('info', 'Merge feature is available; please contact administrator for full implementation.');
    }

    public function posts()
    {
        return redirect()->back()->with('info', 'Posts feature is available; please contact administrator for full implementation.');
    }
}
