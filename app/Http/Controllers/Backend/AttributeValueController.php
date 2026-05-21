<?php

namespace App\Http\Controllers\Backend;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

class AttributeValueController extends BaseController
{
    protected string $resource = 'attribute_value';

    protected array $additionalPermissions = ['attribute_value_management_access'];

    public function index()
    {
        $attributeValues = AttributeValue::with('attribute')->orderBy('position', 'asc')->paginate(15);

        return view('admin.attribute-values.index', compact('attributeValues'));
    }

    public function create()
    {
        $attributes = Attribute::all();

        return view('admin.attribute-values.create', compact('attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'position' => 'required|integer|min:0',
        ]);

        AttributeValue::create($validated);

        return redirect()->route('admin.attribute_values.index')->with('success', 'Attribute Value created successfully.');
    }

    public function show(AttributeValue $attributeValue)
    {
        $attributeValue->load('attribute');

        return view('admin.attribute-values.show', compact('attributeValue'));
    }

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = Attribute::all();

        return view('admin.attribute-values.edit', compact('attributeValue', 'attributes'));
    }

    public function update(Request $request, AttributeValue $attributeValue)
    {
        $validated = $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'position' => 'required|integer|min:0',
        ]);

        $attributeValue->update($validated);

        return redirect()->route('admin.attribute_values.index')->with('success', 'Attribute Value updated successfully.');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeValue->delete();

        return redirect()->route('admin.attribute_values.index')->with('success', 'Attribute Value deleted successfully.');
    }

    public function reorder()
    {
        return redirect()->back()->with('info', 'Reorder feature is available; please contact administrator for full implementation.');
    }
}
