<?php

namespace App\Http\Controllers\Backend;

use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\BaseController;

class OptionController extends BaseController
{
  protected string $resource = 'option';

  protected array $additionalPermissions = ['option_management_access'];

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    if ($request->ajax()) {
      return $this->getDataTableData($request);
    }
    return view('admin.options.index');
  }

  /**
   * Get data for DataTables Ajax
   */
  private function getDataTableData(Request $request)
  {
    $query = Option::withCount('values');

    // Handle global search
    if ($request->has('search') && $request->search['value']) {
      $search = $request->search['value'];
      $query->where(function ($q) use ($search) {
        $q->where('type', 'like', "%{$search}%")
          ->orWhere('position', 'like', "%{$search}%")
          ->orWhereHas('translations', function ($q) use ($search) {
            $q->where('value', 'like', "%{$search}%");
          });
      });
    }

    // Handle column-specific filters
    if ($request->has('columns')) {
      foreach ($request->columns as $index => $column) {
        if (!empty($column['search']['value'])) {
          $searchValue = $column['search']['value'];

          switch ($index) {
            case 2: // Type column
              $query->where('type', 'like', "%{$searchValue}%");
              break;

            case 3: // Required column
              if ($searchValue === 'Required') {
                $query->where('is_required', 1);
              } elseif ($searchValue === 'Optional') {
                $query->where('is_required', 0);
              }
              break;

            case 4: // Global column
              if ($searchValue === 'Yes') {
                $query->where('is_global', 1);
              } elseif ($searchValue === 'No') {
                $query->where('is_global', 0);
              }
              break;
          }
        }
      }
    }

    // Handle column ordering
    if ($request->has('order')) {
      $columns = ['id', 'name', 'type', 'is_required', 'is_global', 'position', 'values_count', 'created_at'];
      $orderColumn = $columns[$request->order[0]['column']] ?? 'id';
      $orderDirection = $request->order[0]['dir'] ?? 'desc';

      if (in_array($orderColumn, ['id', 'type', 'is_required', 'is_global', 'position', 'created_at'])) {
        $query->orderBy($orderColumn, $orderDirection);
      } else {
        $query->orderBy('created_at', 'desc'); // Default fallback
      }
    } else {
      $query->orderBy('created_at', 'desc');
    }

    $totalRecords = Option::count();
    $filteredRecords = $query->count();

    // Handle pagination
    $start = $request->start ?? 0;
    $length = $request->length ?? 10;
    $options = $query->skip($start)->take($length)->get();

    $data = [];
    foreach ($options as $option) {
      $requiredStatus = $option->is_required
        ? '<span class="badge badge-danger">Required</span>'
        : '<span class="badge badge-secondary">Optional</span>';

      $globalStatus = $option->is_global
        ? '<span class="badge badge-success">Yes</span>'
        : '<span class="badge badge-secondary">No</span>';

      $actions = '
        <div class="btn-group">
          <button class="btn btn-sm btn-info view-option" data-id="' . $option->id . '">
            <i class="fas fa-eye"></i>
          </button>
          <button class="btn btn-sm btn-warning edit-option" data-id="' . $option->id . '">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-danger delete-option" data-id="' . $option->id . '">
            <i class="fas fa-trash"></i>
          </button>
        </div>';

      $optionName = $option->getTranslation('name') ?? 'Untitled Option';

      $data[] = [
        'id' => $option->id,
        'name' => '<strong>' . $optionName . '</strong>',
        'type' => '<span class="badge badge-info">' . ucwords(str_replace('_', ' ', $option->type)) . '</span>',
        'is_required' => $requiredStatus,
        'is_global' => $globalStatus,
        'position' => $option->position ?? 'N/A',
        'values_count' => $option->values_count ?? 0,
        'created_at' => $option->created_at ? $option->created_at->format('Y-m-d H:i') : '-',
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
  public function create() {}

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    // Base validation rules
    $rules = [
      'name' => 'required|array',
      'name.*' => 'required|string|max:255',
      'type' => 'required|string|in:field,textarea,dropdown,checkbox,checkbox_custom,radio,radio_custom,multiple_select,date,date_time,time',
      'is_required' => 'boolean',
      'is_global' => 'boolean',
      'position' => 'nullable|integer',
    ];

    // Add validation rules based on type
    $tableTypes = ['dropdown', 'checkbox', 'checkbox_custom', 'radio', 'radio_custom', 'multiple_select'];

    if (in_array($request->type, $tableTypes)) {
      // For table types, validate values array
      $rules['values'] = 'sometimes|array';
      $rules['values.*.name'] = 'required|string|max:255';
      $rules['values.*.price'] = 'nullable|numeric|min:0';
      $rules['values.*.price_type'] = 'nullable|string|in:fixed,percent';
      $rules['values.*.position'] = 'nullable|integer';
    } else {
      // For simple types, validate price fields
      $rules['price'] = 'nullable|numeric|min:0';
      $rules['price_type'] = 'nullable|string|in:fixed,percent';
    }

    $request->validate($rules);

    $option = Option::create([
      'type' => $request->type,
      'is_required' => $request->boolean('is_required'),
      'is_global' => $request->boolean('is_global'),
      'position' => $request->position,
    ]);

    // Handle translations
    if ($request->has('name')) {
      foreach ($request->name as $locale => $name) {
        if (!empty($name)) {
          $option->setTranslation('name', $name, $locale);
        }
      }
    }

    // Handle option values
    $tableTypes = ['dropdown', 'checkbox', 'checkbox_custom', 'radio', 'radio_custom', 'multiple_select'];
    
    if (in_array($request->type, $tableTypes)) {
      // For table types, create multiple values from the values array
      if ($request->has('values')) {
        foreach ($request->values as $valueData) {
          if (!empty($valueData['name'])) {
            $optionValue = $option->values()->create([
              'position' => $valueData['position'] ?? 0,
              'price' => $valueData['price'] ?? null,
              'price_type' => $valueData['price_type'] ?? 'fixed',
            ]);

            // Set translation for value name
            $optionValue->setTranslation('name', $valueData['name'], 'en');
          }
        }
      }
    } else {
      // For simple field types, create a single option value to store price info
      $optionValue = $option->values()->create([
        'position' => 0,
        'price' => $request->price ?? null,
        'price_type' => $request->price_type ?? 'fixed',
      ]);
      
      // Set a default name for simple field types
      $optionValue->setTranslation('name', 'Default', 'en');
    }

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'message' => '🎉 Option created successfully!',
        'title' => 'Success',
        'type' => 'success',
        'option' => $option->load('values')
      ]);
    }

    sweetalert()->success('Option created successfully!');
    return redirect()->route('admin.options.index');
  }

  /**
   * Display the specified resource.
   */
  public function show(Option $option, Request $request)
  {
    $option->load('values');

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'option' => $option
      ]);
    }

    return view('admin.options.show', compact('option'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Option $option, Request $request)
  {
    $option->load('values');

    // Load translations for option and its values
    $option->name = $option->getTranslation('name');
    foreach ($option->values as $value) {
      $value->name = $value->getTranslation('name');
    }

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'option' => $option
      ]);
    }
    return view('admin.options.edit', compact('option'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Option $option)
  {
    // Base validation rules
    $rules = [
      'name' => 'required|array',
      'name.*' => 'required|string|max:255',
      'type' => 'required|string|in:field,textarea,dropdown,checkbox,checkbox_custom,radio,radio_custom,multiple_select,date,date_time,time',
      'is_required' => 'boolean',
      'is_global' => 'boolean',
      'position' => 'nullable|integer',
    ];

    // Add validation rules based on type
    $tableTypes = ['dropdown', 'checkbox', 'checkbox_custom', 'radio', 'radio_custom', 'multiple_select'];

    if (in_array($request->type, $tableTypes)) {
      // For table types, validate values array
      $rules['values'] = 'sometimes|array';
      $rules['values.*.name'] = 'required|string|max:255';
      $rules['values.*.price'] = 'nullable|numeric|min:0';
      $rules['values.*.price_type'] = 'nullable|string|in:fixed,percent';
      $rules['values.*.position'] = 'nullable|integer';
      $rules['values.*.id'] = 'nullable|integer|exists:option_values,id';
    } else {
      // For simple types, validate price fields
      $rules['price'] = 'nullable|numeric|min:0';
      $rules['price_type'] = 'nullable|string|in:fixed,percent';
    }

    $request->validate($rules);

    $option->update([
      'type' => $request->type,
      'is_required' => $request->boolean('is_required'),
      'is_global' => $request->boolean('is_global'),
      'position' => $request->position,
    ]);

    // Handle translations
    if ($request->has('name')) {
      foreach ($request->name as $locale => $name) {
        if (!empty($name)) {
          $option->setTranslation('name', $name, $locale);
        }
      }
    }

    // Handle option values
    $tableTypes = ['dropdown', 'checkbox', 'checkbox_custom', 'radio', 'radio_custom', 'multiple_select'];
    
    if (in_array($request->type, $tableTypes)) {
      // For table types, handle multiple values
      $existingValueIds = [];

      if ($request->has('values')) {
        foreach ($request->values as $valueData) {
          if (!empty($valueData['name'])) {
            if (!empty($valueData['id'])) {
              // Update existing value
              $optionValue = OptionValue::find($valueData['id']);
              if ($optionValue && $optionValue->option_id == $option->id) {
                $optionValue->update([
                  'position' => $valueData['position'] ?? 0,
                  'price' => $valueData['price'] ?? null,
                  'price_type' => $valueData['price_type'] ?? 'fixed',
                ]);
                $optionValue->setTranslation('name', $valueData['name'], 'en');
                $existingValueIds[] = $optionValue->id;
              }
            } else {
              // Create new value
              $optionValue = $option->values()->create([
                'position' => $valueData['position'] ?? 0,
                'price' => $valueData['price'] ?? null,
                'price_type' => $valueData['price_type'] ?? 'fixed',
              ]);
              $optionValue->setTranslation('name', $valueData['name'], 'en');
              $existingValueIds[] = $optionValue->id;
            }
          }
        }
      }

      // Delete values that are no longer present
      $option->values()->whereNotIn('id', $existingValueIds)->delete();
    } else {
      // For simple field types, handle single value for price info
      $existingValue = $option->values()->first();
      
      if ($existingValue) {
        // Update existing value
        $existingValue->update([
          'position' => 0,
          'price' => $request->price ?? null,
          'price_type' => $request->price_type ?? 'fixed',
        ]);
      } else {
        // Create new value
        $optionValue = $option->values()->create([
          'position' => 0,
          'price' => $request->price ?? null,
          'price_type' => $request->price_type ?? 'fixed',
        ]);
        
        // Set a default name for simple field types
        $optionValue->setTranslation('name', 'Default', 'en');
      }
    }

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'message' => '✅ Option updated successfully!',
        'title' => 'Updated',
        'type' => 'success',
        'option' => $option->load('values')
      ]);
    }

    sweetalert()->success('Option updated successfully!');
    return redirect()->route('admin.options.index');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Option $option, Request $request)
  {
    $option->delete();

    if ($request->ajax()) {
      return response()->json([
        'success' => true,
        'message' => '🗑️ Option deleted successfully!',
        'title' => 'Deleted',
        'type' => 'success'
      ]);
    }

    sweetalert()->success('Option deleted successfully!');
    return redirect()->route('admin.options.index');
  }
}
