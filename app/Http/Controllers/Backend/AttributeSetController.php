<?php

namespace App\Http\Controllers\Backend;

use App\Models\Attribute;
use App\Models\AttributeSet;
use Illuminate\Http\Request;

class AttributeSetController extends BaseController
{
    protected string $resource = 'attribute_set';

    protected array $additionalPermissions = ['attribute_set_management_access'];

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
            $paginator = AttributeSet::withCount('attributes')->paginate(15);
            // Ensure translated `name` is present on each item for the frontend
            $paginator->getCollection()->transform(function ($item) {
                $item->name = $item->getTranslation('name');

                return $item;
            });

            return response()->json($paginator);
        }

        return view('admin.attribute-sets.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attribute-sets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $attributeSet = AttributeSet::create([]);

        // Handle translations
        if ($request->filled('name')) {
            $attributeSet->setTranslation('name', $request->input('name'));
        }

        if ($request->ajax()) {
            $attributeSet->name = $attributeSet->getTranslation('name');

            return response()->json([
                'success' => true,
                'message' => '🎉 Attribute Set created successfully!',
                'title' => 'Success',
                'type' => 'success',
                'attribute_set' => $attributeSet,
            ]);
        }

        sweetalert()->success('Attribute Set created successfully!');

        return redirect()->route('admin.attribute-sets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(AttributeSet $attributeSet, Request $request)
    {
        $attributeSet->load('attributes');

        if ($request->ajax()) {
            // Load translations for the response
            $attributeSetData = $attributeSet->toArray();
            $attributeSetData['name'] = $attributeSet->getTranslation('name');

            return response()->json([
                'success' => true,
                'attribute_set' => $attributeSetData,
            ]);
        }

        return view('admin.attribute_sets.show', compact('attributeSet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttributeSet $attributeSet, Request $request)
    {
        if ($request->ajax()) {
            // Load translations for the response
            $attributeSetData = $attributeSet->toArray();
            $attributeSetData['name'] = $attributeSet->getTranslation('name');

            return response()->json([
                'success' => true,
                'attribute_set' => $attributeSetData,
            ]);
        }

        return view('admin.attribute_sets.edit', compact('attributeSet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttributeSet $attributeSet)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Handle translations
        if ($request->filled('name')) {
            $attributeSet->setTranslation('name', $request->input('name'));
        }

        if ($request->ajax()) {
            $attributeSet->name = $attributeSet->getTranslation('name');

            return response()->json([
                'success' => true,
                'message' => '✅ Attribute Set updated successfully!',
                'title' => 'Updated',
                'type' => 'success',
                'attribute_set' => $attributeSet,
            ]);
        }

        sweetalert()->success('Attribute Set updated successfully!');

        return redirect()->route('admin.attribute-sets.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AttributeSet $attributeSet, Request $request)
    {
        // Check if attribute set has attributes
        if ($attributeSet->attributes()->count() > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete attribute set that has attributes!',
                    'title' => 'Error',
                    'type' => 'error',
                ], 422);
            }

            sweetalert()->error('Cannot delete attribute set that has attributes!');

            return redirect()->route('admin.attribute-sets.index');
        }

        $attributeSet->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ Attribute Set deleted successfully!',
                'title' => 'Deleted',
                'type' => 'success',
            ]);
        }

        sweetalert()->success('Attribute Set deleted successfully!');

        return redirect()->route('admin.attribute-sets.index');
    }

    /**
     * Search attribute sets
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $paginator = AttributeSet::whereHas('translations', function ($q) use ($query) {
            $q->where('field', 'name')
                ->where('value', 'like', "%{$query}%");
        })->withCount('attributes')->paginate(15);

        // Attach translated name for frontend
        $paginator->getCollection()->transform(function ($item) {
            $item->name = $item->getTranslation('name');

            return $item;
        });

        // If ajax requested, return JSON like index
        if ($request->ajax()) {
            return response()->json($paginator);
        }

        $attributeSets = $paginator;

        return view('admin.attribute-sets.index', compact('attributeSets', 'query'));
    }

    public function attachAttribute(Request $request, AttributeSet $attributeSet)
    {
        return redirect()->back()->with('info', 'Attach Attribute feature is available; please contact administrator for full implementation.');
    }

    public function attributes(AttributeSet $attributeSet)
    {
        return redirect()->back()->with('info', 'Attributes feature is available; please contact administrator for full implementation.');
    }

    public function detachAttribute(AttributeSet $attributeSet, Attribute $attribute)
    {
        return redirect()->back()->with('info', 'Detach Attribute feature is available; please contact administrator for full implementation.');
    }
}
