<?php

namespace App\Http\Controllers\Backend;

use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariationController extends BaseController
{
    protected string $resource = 'variation';

    protected array $additionalPermissions = ['variation_management_access'];

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
            return $this->getVariationsData($request);
        }

        return view('admin.variations.index');
    }

    /**
     * Get variations data formatted for display
     */
    private function getVariationsData(Request $request)
    {
        $variations = Variation::with('variationValues')
            ->orderBy('position')
            ->orderBy('created_at', 'desc')
            ->get();

        $formattedData = [];
        foreach ($variations as $variation) {
            $formattedData[] = [
                'id' => $variation->id,
                'name' => $variation->getTranslation('name') ?? 'Untitled',
                'uid' => $variation->uid,
                'type' => $variation->type,
                'is_global' => $variation->is_global,
                'position' => $variation->position,
                'values_count' => $variation->variationValues->count(),
                'created_at' => $variation->created_at,
                'updated_at' => $variation->updated_at,
            ];
        }

        return response()->json($formattedData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.variations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'uid' => 'required|string|unique:variations,uid',
            'type' => 'required|string|in:text,color,image',
            'is_global' => 'boolean',
            'position' => 'nullable|integer|min:0',
            'values' => 'nullable|array',
            'values.*.label' => 'required_with:values|string|max:255',
            'values.*.value' => 'nullable|string|max:255',
            'values.*.color' => 'nullable|string|max:7',
            'values.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['name', 'values']);

        // Handle boolean fields
        $data['is_global'] = $request->has('is_global') ? 1 : 0;

        // Set position if not provided
        if (! isset($data['position'])) {
            $maxPosition = Variation::max('position');
            $data['position'] = ($maxPosition ?? 0) + 1;
        }

        $variation = Variation::create($data);

        // Handle translations
        if ($request->filled('name')) {
            $variation->setTranslation('name', $request->input('name'));
        }

        // Handle variation values
        if ($request->has('values') && is_array($request->values)) {
            $this->saveVariationValues($variation, $request->values, $request);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🎉 Variation created successfully!',
                'title' => 'Success',
                'type' => 'success',
                'variation' => $variation->load('variationValues'),
            ]);
        }

        sweetalert()->success('Variation created successfully!');

        return redirect()->route('admin.variations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Variation $variation, Request $request)
    {
        $variation->load('variationValues');

        if ($request->ajax()) {
            // Load translations for the response
            $variationData = $variation->toArray();
            $variationData['name'] = $variation->getTranslation('name');

            return response()->json([
                'success' => true,
                'variation' => $variationData,
            ]);
        }

        return view('admin.variations.show', compact('variation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Variation $variation, Request $request)
    {
        $variation->load('variationValues');

        if ($request->ajax()) {
            // Load translations for the response
            $variationData = $variation->toArray();
            $variationData['name'] = $variation->getTranslation('name');

            // Format values for the frontend
            $variationData['values'] = $variation->variationValues->map(function ($value) {
                return [
                    'value' => $value->value,
                    'color' => $value->color_value ?? null,
                    'image' => $value->image_path ? asset('storage/'.$value->image_path) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'variation' => $variationData,
            ]);
        }

        return view('admin.variations.edit', compact('variation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Variation $variation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'uid' => 'required|string|unique:variations,uid,'.$variation->id,
            'type' => 'required|string|in:text,color,image',
            'is_global' => 'boolean',
            'position' => 'nullable|integer|min:0',
            'values' => 'nullable|array',
            'values.*.label' => 'required_with:values|string|max:255',
            'values.*.value' => 'nullable|string|max:255',
            'values.*.color' => 'nullable|string|max:7',
            'values.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['name', 'values']);

        // Handle boolean fields
        $data['is_global'] = $request->has('is_global') ? 1 : 0;

        $variation->update($data);

        // Handle translations
        if ($request->filled('name')) {
            $variation->setTranslation('name', $request->input('name'));
        }

        // Handle variation values
        if ($request->has('values') && is_array($request->values)) {
            // Delete existing values first
            $variation->variationValues()->delete();
            // Create new values
            $this->saveVariationValues($variation, $request->values, $request);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Variation updated successfully!',
                'title' => 'Updated',
                'type' => 'success',
                'variation' => $variation->load('variationValues'),
            ]);
        }

        sweetalert()->success('Variation updated successfully!');

        return redirect()->route('admin.variations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Variation $variation, Request $request)
    {
        // Check if variation has values
        if ($variation->variationValues()->count() > 0) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete variation that has values!',
                    'title' => 'Error',
                    'type' => 'error',
                ], 422);
            }

            sweetalert()->error('Cannot delete variation that has values!');

            return redirect()->route('admin.variations.index');
        }

        $variation->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ Variation deleted successfully!',
                'title' => 'Deleted',
                'type' => 'success',
            ]);
        }

        sweetalert()->success('Variation deleted successfully!');

        return redirect()->route('admin.variations.index');
    }

    /**
     * Search variations
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $variations = Variation::where(function ($q) use ($query) {
            $q->where('uid', 'like', "%{$query}%")
                ->orWhereHas('translations', function ($subQ) use ($query) {
                    $subQ->where('field', 'name')
                        ->where('value', 'like', "%{$query}%");
                });
        })->paginate(15);

        return view('admin.variations.index', compact('variations', 'query'));
    }

    /**
     * Toggle global status
     */
    public function toggleGlobal(Request $request, Variation $variation)
    {
        $variation->update([
            'is_global' => ! $variation->is_global,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Global status updated successfully!',
                'is_global' => $variation->is_global,
            ]);
        }

        sweetalert()->success('Global status updated successfully!');

        return redirect()->back();
    }

    /**
     * Get variation values
     */
    public function values(Variation $variation, Request $request)
    {
        $values = $variation->variationValues()->orderBy('position')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'values' => $values,
            ]);
        }

        return view('admin.variations.values', compact('variation', 'values'));
    }

    /**
     * Store variation value
     */
    public function storeValue(Request $request, Variation $variation)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'color' => 'required_if:type,color|string|max:7',
            'image_url' => 'required_if:type,image|string|max:255',
        ]);

        $data = $request->only(['label', 'color', 'image_url']);

        // Set position
        $maxPosition = $variation->variationValues()->max('position');
        $data['position'] = ($maxPosition ?? 0) + 1;

        $variationValue = $variation->variationValues()->create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Value added successfully!',
                'value' => $variationValue,
            ]);
        }

        sweetalert()->success('Value added successfully!');

        return redirect()->back();
    }

    /**
     * Delete variation value
     */
    public function destroyValue(Request $request, Variation $variation, $variationValueId)
    {
        $variationValue = $variation->variationValues()->findOrFail($variationValueId);
        $variationValue->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Value deleted successfully!',
            ]);
        }

        sweetalert()->success('Value deleted successfully!');

        return redirect()->back();
    }

    /**
     * Reorder variation values
     */
    public function reorderValues(Request $request)
    {
        $request->validate([
            'values' => 'required|array',
            'values.*.id' => 'required|exists:variation_values,id',
            'values.*.position' => 'required|integer|min:0',
        ]);

        foreach ($request->values as $valueData) {
            \App\Models\VariationValue::where('id', $valueData['id'])
                ->update(['position' => $valueData['position']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Values reordered successfully!',
        ]);
    }

    /**
     * Save variation values from the form
     */
    private function saveVariationValues(Variation $variation, array $values, Request $request)
    {
        foreach ($values as $index => $valueData) {
            if (empty($valueData['label'])) {
                continue; // Skip empty labels
            }

            $data = [
                'value' => $valueData['label'], // Use label as the main value
                'position' => $index + 1,
                'uid' => Str::random(10), // Generate UID for variation value
            ];

            // Handle different value types
            switch ($variation->type) {
                case 'color':
                    if (isset($valueData['color'])) {
                        $data['color_value'] = $valueData['color'];
                    }
                    break;

                case 'text':
                    if (isset($valueData['value'])) {
                        $data['text_value'] = $valueData['value'];
                    }
                    break;

                case 'image':
                    // Handle image upload
                    if (isset($valueData['image']) && $request->hasFile("values.{$index}.image")) {
                        $image = $request->file("values.{$index}.image");
                        $imageName = time().'_'.$index.'_'.$image->getClientOriginalName();
                        $imagePath = $image->storeAs('variation-values', $imageName, 'public');
                        $data['image_path'] = $imagePath;
                    }
                    break;
            }

            $variation->variationValues()->create($data);
        }
    }
}
