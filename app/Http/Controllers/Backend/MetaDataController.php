<?php

namespace App\Http\Controllers\Backend;

use App\Models\MetaData;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class MetaDataController extends BaseController
{
    protected string $resource = 'meta_data';
    
    protected array $additionalPermissions = ['meta_data_management_access'];

    public function index()
    {
        $metaData = MetaData::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.meta_data.index', compact('metaData'));
    }

    public function create()
    {
        return view('admin.meta_data.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer'
        ]);

        MetaData::create($validated);

        return redirect()->route('admin.meta_data.index')->with('success', 'Meta Data created successfully.');
    }

    public function show(MetaData $metaData)
    {
        return view('admin.meta_data.show', compact('metaData'));
    }

    public function edit(MetaData $metaData)
    {
        return view('admin.meta_data.edit', compact('metaData'));
    }

    public function update(Request $request, MetaData $metaData)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer'
        ]);

        $metaData->update($validated);

        return redirect()->route('admin.meta_data.index')->with('success', 'Meta Data updated successfully.');
    }

    public function destroy(MetaData $metaData)
    {
        $metaData->delete();

        return redirect()->route('admin.meta_data.index')->with('success', 'Meta Data deleted successfully.');
    }
}