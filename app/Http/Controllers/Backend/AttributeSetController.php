<?php

namespace App\Http\Controllers\Backend;

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
            return $this->getAttributeSetsData($request);
        }

        return view('admin.attribute_sets.index');
    }

    /**
     * Get attribute sets data formatted for display
     */
    private function getAttributeSetsData(Request $request)
    {
        $attributeSets = AttributeSet::with('attributes')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedData = [];
        foreach ($attributeSets as $attributeSet) {
            $formattedData[] = [
                'id' => $attributeSet->id,
                'name' => $attributeSet->getTranslation('name') ?? 'Untitled',
                'attributes_count' => $attributeSet->attributes->count(),
                'created_at' => $attributeSet->created_at,
                'updated_at' => $attributeSet->updated_at,
            ];
        }

        return response()->json($formattedData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.attribute_sets.create');
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
            return response()->json([
                'success' => true,
                'message' => '🎉 Attribute Set created successfully!',
                'title' => 'Success',
                'type' => 'success',
                'attribute_set' => $attributeSet,
            ]);
        }

        sweetalert()->success('Attribute Set created successfully!');

        return redirect()->route('admin.attribute_sets.index');
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
            return response()->json([
                'success' => true,
                'message' => '✅ Attribute Set updated successfully!',
                'title' => 'Updated',
                'type' => 'success',
                'attribute_set' => $attributeSet,
            ]);
        }

        sweetalert()->success('Attribute Set updated successfully!');

        return redirect()->route('admin.attribute_sets.index');
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

            return redirect()->route('admin.attribute_sets.index');
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

        return redirect()->route('admin.attribute_sets.index');
    }

    /**
     * Search attribute sets
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $attributeSets = AttributeSet::whereHas('translations', function ($q) use ($query) {
            $q->where('field', 'name')
                ->where('value', 'like', "%{$query}%");
        })->paginate(15);

        return view('admin.attribute_sets.index', compact('attributeSets', 'query'));
    }

    public function attachAttribute()
    {
        return redirect()->back()->with('info', 'Attach Attribute feature is available; please contact administrator for full implementation.');
    }

    public function attributes()
    {
        return redirect()->back()->with('info', 'Attributes feature is available; please contact administrator for full implementation.');
    }

    public function detachAttribute()
    {
        return redirect()->back()->with('info', 'Detach Attribute feature is available; please contact administrator for full implementation.');
    }
}
