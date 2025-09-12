<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Backend\BaseController;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ImageUploadTrait;
use Illuminate\Support\Str;

class CategoryController extends BaseController
{
    use ImageUploadTrait;

    protected string $resource = 'category';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getJsTreeData($request);
        }

        return view('admin.categories.index');
    }

    /**
     * Get categories data formatted for jsTree
     */
    private function getJsTreeData(Request $request)
    {
        $categories = Category::with(['children' => function($query) {
            $query->orderBy('position');
        }])
        ->whereNull('parent_id')
        ->orderBy('position')
        ->get();

        $treeData = [];
        foreach ($categories as $category) {
            $treeData[] = $this->formatCategoryForTree($category);
        }

        return response()->json($treeData);
    }

    /**
     * Format category data for jsTree
     */
    private function formatCategoryForTree($category)
    {
        $categoryName = $category->getTranslation('name') ?? 'Untitled';
        $categoryDescription = $category->getTranslation('description') ?? '';
        
        $node = [
            'id' => 'category_' . $category->id,
            'text' => $categoryName,
            'data' => [
                'id' => $category->id,
                'name' => $categoryName,
                'slug' => $category->slug,
                'parent_id' => $category->parent_id,
                'position' => $category->position,
                'is_searchable' => $category->is_searchable,
                'is_active' => $category->is_active,
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
                'image' => $category->image,
                'description' => $categoryDescription,
            ],
            'state' => [
                'opened' => true
            ],
            'icon' => $category->is_active ? 'jstree-folder' : 'jstree-folder text-muted'
        ];

        // Add children if they exist
        if ($category->children && $category->children->count() > 0) {
            $node['children'] = [];
            foreach ($category->children as $child) {
                $node['children'][] = $this->formatCategoryForTree($child);
            }
        }

        return $node;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'position' => 'nullable|integer|min:0',
            'is_searchable' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $data = $request->except(['image', 'image_url', 'old_image', 'name', 'description']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->input('name'));
        }
        
        // Ensure slug is unique
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle boolean fields
        $data['is_searchable'] = $request->has('is_searchable') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request, 'image', 'uploads/categories', 'category_');
        } elseif ($request->filled('image_url')) {
            $imageUrl = $request->image_url;
            if (str_contains($imageUrl, '/storage/')) {
                $data['image'] = str_replace(url('/storage/'), '', $imageUrl);
            } else {
                $data['image'] = $imageUrl;
            }
        }

        // Set position if not provided
        if (!isset($data['position'])) {
            $maxPosition = Category::where('parent_id', $data['parent_id'])->max('position');
            $data['position'] = ($maxPosition ?? 0) + 1;
        }

        $category = Category::create($data);

        // Handle translations
        if ($request->filled('name')) {
            $category->setTranslation('name', $request->input('name'));
        }
        if ($request->filled('description')) {
            $category->setTranslation('description', $request->input('description'));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🎉 Category created successfully!',
                'title' => 'Success',
                'type' => 'success',
                'category' => $category
            ]);
        }

        sweetalert()->success('Category created successfully!');
        return redirect()->route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category, Request $request)
    {
        $category->load(['parent', 'children', 'products']);

        if ($request->ajax()) {
            // Load translations for the response
            $categoryData = $category->toArray();
            $categoryData['name'] = $category->getTranslation('name');
            $categoryData['description'] = $category->getTranslation('description');
            
            return response()->json([
                'success' => true,
                'category' => $categoryData
            ]);
        }

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category, Request $request)
    {
        $category->load(['parent', 'children']);

        if ($request->ajax()) {
            // Load translations for the response
            $categoryData = $category->toArray();
            $categoryData['name'] = $category->getTranslation('name');
            $categoryData['description'] = $category->getTranslation('description');
            
            return response()->json([
                'success' => true,
                'category' => $categoryData
            ]);
        }

        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'position' => 'nullable|integer|min:0',
            'is_searchable' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|url'
        ]);

        $data = $request->except(['image', 'image_url', 'old_image', 'name', 'description']);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($request->input('name'));
        }
        
        // Ensure slug is unique (excluding current category)
        $originalSlug = $data['slug'];
        $counter = 1;
        while (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle boolean fields
        $data['is_searchable'] = $request->has('is_searchable') ? 1 : 0;
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        // Handle image update
        if ($request->hasFile('image')) {
            $data['image'] = $this->updateImage($request, 'image', 'uploads/categories', 'category_', $category->image);
        } elseif ($request->filled('image_url')) {
            $imageUrl = $request->image_url;
            $relativePath = $imageUrl;

            if (str_contains($imageUrl, '/storage/')) {
                $relativePath = str_replace(url('/storage/'), '', $imageUrl);
            }

            if ($relativePath !== $category->image) {
                if ($category->image && !str_starts_with($category->image, 'http')) {
                    $this->deleteImage($category->image);
                }
                $data['image'] = $relativePath;
            }
        } elseif ($request->filled('old_image') && empty($request->image_url)) {
            if ($category->image && !str_starts_with($category->image, 'http')) {
                $this->deleteImage($category->image);
            }
            $data['image'] = null;
        }

        $category->update($data);

        // Handle translations
        if ($request->filled('name')) {
            $category->setTranslation('name', $request->input('name'));
        }
        if ($request->filled('description')) {
            $category->setTranslation('description', $request->input('description'));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Category updated successfully!',
                'title' => 'Updated',
                'type' => 'success',
                'category' => $category
            ]);
        }

        sweetalert()->success('Category updated successfully!');
        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category, Request $request)
    {
        // Check if category has children
        if ($category->children()->count() > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has subcategories!',
                    'title' => 'Error',
                    'type' => 'error'
                ], 422);
            }

            sweetalert()->error('Cannot delete category that has subcategories!');
            return redirect()->route('admin.categories.index');
        }

        // Check if category has products
        if ($category->products()->count() > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category that has products!',
                    'title' => 'Error',
                    'type' => 'error'
                ], 422);
            }

            sweetalert()->error('Cannot delete category that has products!');
            return redirect()->route('admin.categories.index');
        }

        // Delete image if exists
        if ($category->image && !str_starts_with($category->image, 'http')) {
            $this->deleteImage($category->image);
        }

        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ Category deleted successfully!',
                'title' => 'Deleted',
                'type' => 'success'
            ]);
        }

        sweetalert()->success('Category deleted successfully!');
        return redirect()->route('admin.categories.index');
    }

    /**
     * Update category position/order
     */
    public function updatePosition(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.position' => 'required|integer|min:0',
            'categories.*.parent_id' => 'nullable|exists:categories,id'
        ]);

        foreach ($request->categories as $categoryData) {
            Category::where('id', $categoryData['id'])
                ->update([
                    'position' => $categoryData['position'],
                    'parent_id' => $categoryData['parent_id']
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categories order updated successfully!',
            'title' => 'Success',
            'type' => 'success'
        ]);
    }

    /**
     * Search categories
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $categories = Category::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('slug', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })->with('parent')->paginate(15);

        return view('admin.categories.index', compact('categories', 'query'));
    }

    /**
     * Get categories tree for display
     */
    public function tree()
    {
        $categories = Category::with(['children' => function($query) {
            $query->orderBy('position')->orderBy('name');
        }])
        ->whereNull('parent_id')
        ->orderBy('position')
        ->orderBy('name')
        ->get();

        return view('admin.categories.tree', compact('categories'));
    }

    /**
     * Get parent categories for dropdowns
     */
    public function getParentCategories(Request $request)
    {
        $excludeId = $request->get('exclude_id');
        
        $categories = Category::whereNull('parent_id')
            ->when($excludeId, function($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}