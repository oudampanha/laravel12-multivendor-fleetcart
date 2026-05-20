<?php

namespace App\Http\Controllers\Backend;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends BaseController
{
    protected string $resource = 'warehouse';

    protected array $additionalPermissions = ['inventory_management_access'];

    public function index(Request $request)
    {
        $query = Warehouse::query()->withCount('stocks');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if (! is_null($request->input('is_active'))) {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        $warehouses = $query->orderBy('position')->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.warehouses.index', compact('warehouses'));
    }

    public function create()
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'vendor_id' => 'nullable|exists:vendors,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_default'] = $request->boolean('is_default', false);

        DB::transaction(function () use ($data) {
            if (! empty($data['is_default'])) {
                Warehouse::where('is_default', true)->update(['is_default' => false]);
            }
            Warehouse::create($data);
        });

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse created successfully.');
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['stocks.product', 'stocks.variant']);

        return view('admin.warehouses.show', compact('warehouse'));
    }

    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code,'.$warehouse->id,
            'name' => 'required|string|max:255',
            'vendor_id' => 'nullable|exists:vendors,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');

        DB::transaction(function () use ($data, $warehouse) {
            if (! empty($data['is_default'])) {
                Warehouse::where('id', '!=', $warehouse->id)->where('is_default', true)->update(['is_default' => false]);
            }
            $warehouse->update($data);
        });

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')->with('success', 'Warehouse deleted successfully.');
    }
}
