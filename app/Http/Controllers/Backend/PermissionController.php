<?php

namespace App\Http\Controllers\Backend;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends BaseController
{
    protected string $resource = 'permission';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['groups'] = Permission::select('group')->distinct()->whereNotNull('group')->pluck('group');

        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.permissions.index', $data);
    }

    /**
     * Get data for DataTables Ajax
     */
    private function getDataTableData(Request $request)
    {
        $query = Permission::query();

        // Handle global search
        if ($request->has('search') && $request->search['value']) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%");
            });
        }

        // Handle column-specific filters
        if ($request->has('columns')) {
            foreach ($request->columns as $index => $column) {
                if (! empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];

                    switch ($index) {
                        case 3: // Status column
                            if ($searchValue === 'Active') {
                                $query->where('status', 1);
                            } elseif ($searchValue === 'Inactive') {
                                $query->where('status', 0);
                            }
                            break;

                        case 1: // Group column
                            $query->where('group', 'like', "%{$searchValue}%");
                            break;
                    }
                }
            }
        }

        // Handle column ordering
        if ($request->has('order')) {
            $columns = ['id', 'group', 'title', 'status', 'created_at'];
            $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
            $orderDirection = $request->order[0]['dir'] ?? 'desc';

            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->orderBy('group', 'asc')->orderBy('title', 'asc');
        }

        $totalRecords = Permission::count();
        $filteredRecords = $query->count();

        // Handle pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        $permissions = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($permissions as $permission) {
            $status = $permission->status
              ? '<span class="badge badge-success">Active</span>'
              : '<span class="badge badge-danger">Inactive</span>';

            $actions = '
        <div class="btn-group">
          <button class="btn btn-sm btn-info view-permission" data-id="'.$permission->id.'">
            <i class="fas fa-eye"></i>
          </button>
          <button class="btn btn-sm btn-warning edit-permission" data-id="'.$permission->id.'">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-danger delete-permission" data-id="'.$permission->id.'">
            <i class="fas fa-trash"></i>
          </button>
        </div>';

            $data[] = [
                'id' => $permission->id,
                'group' => $permission->group ? '<span class="badge badge-secondary">'.$permission->group.'</span>' : '<span class="text-muted">No Group</span>',
                'title' => '<strong>'.$permission->title.'</strong>',
                'status' => $status,
                'created_at' => $permission->created_at ? $permission->created_at->format('Y-m-d H:i') : '-',
                'actions' => $actions,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:permissions,title',
            'group' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $data = $request->only(['title', 'group', 'status']);
        $data['status'] = $request->has('status') ? 1 : 0;

        $permission = Permission::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🎉 Permission created successfully!',
                'title' => 'Success',
                'type' => 'success',
                'permission' => $permission,
            ]);
        }

        sweetalert()->success('Permission created successfully!');

        return redirect()->route('admin.permissions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission, Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'permission' => $permission,
            ]);
        }

        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission, Request $request)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'permission' => $permission,
            ]);
        }

        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:permissions,title,'.$permission->id,
            'group' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $data = $request->only(['title', 'group', 'status']);
        $data['status'] = $request->has('status') ? 1 : 0;

        $permission->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Permission updated successfully!',
                'title' => 'Updated',
                'type' => 'success',
                'permission' => $permission,
            ]);
        }

        sweetalert()->success('Permission updated successfully!');

        return redirect()->route('admin.permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission, Request $request)
    {
        $permission->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '🗑️ Permission deleted successfully!',
                'title' => 'Deleted',
                'type' => 'success',
            ]);
        }

        sweetalert()->success('Permission deleted successfully!');

        return redirect()->route('admin.permissions.index');
    }

    /**
     * Search permissions
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $permissions = Permission::where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('group', 'like', "%{$query}%");
        })->paginate(15);

        return view('admin.permissions.index', compact('permissions', 'query'));
    }

    /**
     * Get permissions by group
     */
    public function byGroup(Request $request)
    {
        $group = $request->get('group', 'all');

        $query = Permission::query();

        if ($group !== 'all' && ! empty($group)) {
            $query->where('group', $group);
        }

        $permissions = $query->orderBy('group', 'asc')->orderBy('title', 'asc')->paginate(15);

        return view('admin.permissions.index', compact('permissions', 'group'));
    }
}
