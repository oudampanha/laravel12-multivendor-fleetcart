<?php

namespace App\Http\Controllers\Backend;

use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Http\Request;

class OptionValueController extends BaseController
{
    protected string $resource = 'option_value';

    protected array $additionalPermissions = ['option_value_management_access'];

    public function index()
    {
        $optionValues = OptionValue::with('option')->orderBy('position', 'asc')->paginate(15);

        return view('admin.option-values.index', compact('optionValues'));
    }

    public function create()
    {
        $options = Option::all();

        return view('admin.option-values.create', compact('options'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'option_id' => 'required|exists:options,id',
            'price' => 'nullable|decimal:0,4',
            'price_type' => 'required|string|max:10',
            'position' => 'required|integer|min:0',
        ]);

        OptionValue::create($validated);

        return back()->with('success', 'Option Value created successfully.');
    }

    public function show(OptionValue $optionValue)
    {
        $optionValue->load('option');

        return view('admin.option-values.show', compact('optionValue'));
    }

    public function edit(OptionValue $optionValue)
    {
        $options = Option::all();

        return view('admin.option-values.edit', compact('optionValue', 'options'));
    }

    public function update(Request $request, OptionValue $optionValue)
    {
        $validated = $request->validate([
            'option_id' => 'required|exists:options,id',
            'price' => 'nullable|decimal:0,4',
            'price_type' => 'required|string|max:10',
            'position' => 'required|integer|min:0',
        ]);

        $optionValue->update($validated);

        return back()->with('success', 'Option Value updated successfully.');
    }

    public function destroy(OptionValue $optionValue)
    {
        $optionValue->delete();

        return back()->with('success', 'Option Value deleted successfully.');
    }

    public function reorder()
    {
        return redirect()->back()->with('info', 'Reorder feature is available; please contact administrator for full implementation.');
    }
}
