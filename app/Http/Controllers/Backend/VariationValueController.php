<?php

namespace App\Http\Controllers\Backend;

use App\Models\Variation;
use App\Models\VariationValue;
use Illuminate\Http\Request;

class VariationValueController extends BaseController
{
    protected string $resource = 'variation_value';

    protected array $additionalPermissions = ['variation_value_management_access'];

    public function index()
    {
        $variationValues = VariationValue::with('variation')->orderBy('position', 'asc')->paginate(15);

        return view('admin.variation_values.index', compact('variationValues'));
    }

    public function create()
    {
        $variations = Variation::all();

        return view('admin.variation_values.create', compact('variations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'uid' => 'required|string|unique:variation_values,uid',
            'variation_id' => 'required|exists:variations,id',
            'value' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
        ]);

        VariationValue::create($validated);

        return redirect()->route('admin.variation_values.index')->with('success', 'Variation Value created successfully.');
    }

    public function show(VariationValue $variationValue)
    {
        $variationValue->load('variation');

        return view('admin.variation_values.show', compact('variationValue'));
    }

    public function edit(VariationValue $variationValue)
    {
        $variations = Variation::all();

        return view('admin.variation_values.edit', compact('variationValue', 'variations'));
    }

    public function update(Request $request, VariationValue $variationValue)
    {
        $validated = $request->validate([
            'uid' => 'required|string|unique:variation_values,uid,'.$variationValue->id,
            'variation_id' => 'required|exists:variations,id',
            'value' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
        ]);

        $variationValue->update($validated);

        return redirect()->route('admin.variation_values.index')->with('success', 'Variation Value updated successfully.');
    }

    public function destroy(VariationValue $variationValue)
    {
        $variationValue->delete();

        return redirect()->route('admin.variation_values.index')->with('success', 'Variation Value deleted successfully.');
    }
}
