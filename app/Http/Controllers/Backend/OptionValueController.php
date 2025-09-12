<?php

namespace App\Http\Controllers\Backend;

use App\Models\OptionValue;
use App\Models\Option;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class OptionValueController extends BaseController
{
    protected string $resource = 'option_value';
    
    protected array $additionalPermissions = ['option_value_management_access'];

    public function index()
    {
        $optionValues = OptionValue::with('option')->orderBy('position', 'asc')->paginate(15);
        return view('admin.option_values.index', compact('optionValues'));
    }

    public function create()
    {
        $options = Option::all();
        return view('admin.option_values.create', compact('options'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'option_id' => 'required|exists:options,id',
            'price' => 'nullable|decimal:0,4',
            'price_type' => 'required|string|max:10',
            'position' => 'required|integer|min:0'
        ]);

        OptionValue::create($validated);

        return redirect()->route('admin.option_values.index')->with('success', 'Option Value created successfully.');
    }

    public function show(OptionValue $optionValue)
    {
        $optionValue->load('option');
        return view('admin.option_values.show', compact('optionValue'));
    }

    public function edit(OptionValue $optionValue)
    {
        $options = Option::all();
        return view('admin.option_values.edit', compact('optionValue', 'options'));
    }

    public function update(Request $request, OptionValue $optionValue)
    {
        $validated = $request->validate([
            'option_id' => 'required|exists:options,id',
            'price' => 'nullable|decimal:0,4',
            'price_type' => 'required|string|max:10',
            'position' => 'required|integer|min:0'
        ]);

        $optionValue->update($validated);

        return redirect()->route('admin.option_values.index')->with('success', 'Option Value updated successfully.');
    }

    public function destroy(OptionValue $optionValue)
    {
        $optionValue->delete();

        return redirect()->route('admin.option_values.index')->with('success', 'Option Value deleted successfully.');
    }
}