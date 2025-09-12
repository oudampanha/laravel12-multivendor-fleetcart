<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index()
    {
        $blogPosts = BlogPost::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        return view('admin.blog-posts.index', compact('blogPosts'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        $users = User::all();
        
        return view('admin.blog-posts.create', compact('categories', 'tags', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'slug' => 'required|string|unique:blog_posts,slug',
            'publish_status' => 'required|string|in:draft,published,archived'
        ]);

        $blogPost = BlogPost::create($request->all());

        if ($request->has('tags')) {
            $blogPost->tags()->attach($request->tags);
        }

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post created successfully.');
    }

    public function show(BlogPost $blogPost)
    {
        $blogPost->load(['user', 'category', 'tags']);
        return view('admin.blog-posts.show', compact('blogPost'));
    }

    public function edit(BlogPost $blogPost)
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        $users = User::all();
        
        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'tags', 'users'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'slug' => 'required|string|unique:blog_posts,slug,' . $blogPost->id,
            'publish_status' => 'required|string|in:draft,published,archived'
        ]);

        $blogPost->update($request->all());

        if ($request->has('tags')) {
            $blogPost->tags()->sync($request->tags);
        }

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Blog post deleted successfully.');
    }

    public function publish(BlogPost $blogPost)
    {
        $blogPost->update(['publish_status' => 'published']);

        return redirect()->back()
            ->with('success', 'Blog post published successfully.');
    }

    public function unpublish(BlogPost $blogPost)
    {
        $blogPost->update(['publish_status' => 'draft']);

        return redirect()->back()
            ->with('success', 'Blog post unpublished successfully.');
    }
}