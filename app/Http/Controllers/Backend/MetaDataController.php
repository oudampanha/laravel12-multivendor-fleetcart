<?php

namespace App\Http\Controllers\Backend;

use App\Models\MetaData;
use Illuminate\Http\Request;

class MetaDataController extends BaseController
{
    protected string $resource = 'meta_data';

    protected array $additionalPermissions = ['meta_data_management_access'];

    public function index()
    {
        $metaData = MetaData::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.meta-data.index', compact('metaData'));
    }

    public function create()
    {
        return view('admin.meta-data.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        MetaData::create($validated);

        return back()->with('success', 'Meta Data created successfully.');
    }

    public function show(MetaData $metaData)
    {
        return view('admin.meta-data.show', compact('metaData'));
    }

    public function edit(MetaData $metaData)
    {
        return view('admin.meta-data.edit', compact('metaData'));
    }

    public function update(Request $request, MetaData $metaData)
    {
        $validated = $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $metaData->update($validated);

        return back()->with('success', 'Meta Data updated successfully.');
    }

    public function destroy(MetaData $metaData)
    {
        $metaData->delete();

        return back()->with('success', 'Meta Data deleted successfully.');
    }
}
