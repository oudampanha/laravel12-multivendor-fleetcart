<?php

namespace App\Http\Controllers\Backend;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    protected string $resource = 'tag';

    protected array $additionalPermissions = ['tag_management_access'];

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
            return $this->getDataTableData($request);
        }

        return view('admin.tags.index');
    }

    /**
     * Get data for DataTables Ajax
     */
    private function getDataTableData(Request $request)
    {
        $query = Tag::withCount('products');

        // Handle global search
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                    ->orWhereHas('translations', function ($q) use ($search) {
                        $q->where('value', 'like', "%{$search}%");
                    });
            });
        }

        // Handle column-specific filters
        if ($request->has('columns')) {
            foreach ($request->columns as $index => $column) {
                if (! empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];

                    switch ($index) {
                        case 1: // Slug column
                            $query->where('slug', 'like', "%{$searchValue}%");
                            break;
                        case 3: // Products count filter
                            if ($searchValue === 'With Products') {
                                $query->has('products');
                            } elseif ($searchValue === 'No Products') {
                                $query->doesntHave('products');
                            }
                            break;
                    }
                }
            }
        }

        // Handle column ordering
        if ($request->has('order')) {
            $columns = ['id', 'name', 'slug', 'products_count', 'created_at'];
            $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
            $orderDirection = $request->order[0]['dir'] ?? 'desc';

            if (in_array($orderColumn, ['id', 'slug', 'created_at'])) {
                $query->orderBy($orderColumn, $orderDirection);
            } else {
                $query->orderBy('created_at', 'desc'); // Default fallback
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $totalRecords = Tag::count();
        $filteredRecords = $query->count();

        // Handle pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $tags = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($tags as $tag) {
            $actions = '
                <div class="btn-group">
                    <button class="btn btn-sm btn-info view-tag" data-id="'.$tag->id.'">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning edit-tag" data-id="'.$tag->id.'">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-tag" data-id="'.$tag->id.'">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';

            $tagName = $tag->getTranslation('name') ?? 'Untitled Tag';

            $data[] = [
                'id' => $tag->id,
                'name' => '<strong>'.$tagName.'</strong>',
                'slug' => '<span class="badge badge-secondary">'.$tag->slug.'</span>',
                'products_count' => $tag->products_count ?? 0,
                'created_at' => $tag->created_at ? $tag->created_at->format('Y-m-d H:i') : '-',
                'actions' => $actions,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'slug' => 'required|string|unique:tags,slug|max:255',
        ]);

        $tag = Tag::create([
            'slug' => $request->slug,
        ]);

        // Handle translations
        if ($request->has('name')) {
            foreach ($request->name as $locale => $name) {
                if (! empty($name)) {
                    $tag->setTranslation('name', $name, $locale);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🎉 Tag created successfully!',
                'title' => 'Success',
                'type' => 'success',
                'tag' => $tag,
            ]);
        }

        sweetalert()->success('Tag created successfully!');

        return redirect()->route('admin.tags.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag, Request $request)
    {
        $tag->load('products');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tag' => $tag,
            ]);
        }

        return view('admin.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag, Request $request)
    {
        // Load translations for the tag
        $tag->name = $tag->getTranslation('name');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tag' => $tag,
            ]);
        }

        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|array',
            'name.*' => 'required|string|max:255',
            'slug' => 'required|string|unique:tags,slug,'.$tag->id.'|max:255',
        ]);

        $tag->update([
            'slug' => $request->slug,
        ]);

        // Handle translations
        if ($request->has('name')) {
            foreach ($request->name as $locale => $name) {
                if (! empty($name)) {
                    $tag->setTranslation('name', $name, $locale);
                }
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Tag updated successfully!',
                'title' => 'Updated',
                'type' => 'success',
                'tag' => $tag,
            ]);
        }

        sweetalert()->success('Tag updated successfully!');

        return redirect()->route('admin.tags.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag, Request $request)
    {
        $tag->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ Tag deleted successfully!',
                'title' => 'Deleted',
                'type' => 'success',
            ]);
        }

        sweetalert()->success('Tag deleted successfully!');

        return redirect()->route('admin.tags.index');
    }
}
