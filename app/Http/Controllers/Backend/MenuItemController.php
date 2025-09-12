<?php

namespace App\Http\Controllers\Backend;

use App\Models\MenuItem;
use App\Models\Menu;
use App\Models\Category;
use App\Models\Page;
use App\Http\Controllers\Backend\BaseController;
use Illuminate\Http\Request;

class MenuItemController extends BaseController
{
    protected string $resource = 'menu_item';
    
    protected array $additionalPermissions = ['menu_item_management_access'];

    public function index()
    {
        $menuItems = MenuItem::with(['menu', 'parent', 'category', 'page'])
                            ->orderBy('position', 'asc')
                            ->paginate(15);
        return view('admin.menu_items.index', compact('menuItems'));
    }

    public function create()
    {
        $menus = Menu::all();
        $menuItems = MenuItem::all();
        $categories = Category::all();
        $pages = Page::all();
        return view('admin.menu_items.create', compact('menus', 'menuItems', 'categories', 'pages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'category_id' => 'nullable|exists:categories,id',
            'page_id' => 'nullable|exists:pages,id',
            'type' => 'required|string',
            'url' => 'nullable|string',
            'icon' => 'nullable|string',
            'target' => 'required|string',
            'position' => 'nullable|integer|min:0',
            'is_root' => 'boolean',
            'is_fluid' => 'boolean',
            'is_active' => 'boolean'
        ]);

        MenuItem::create($validated);

        return redirect()->route('admin.menu_items.index')->with('success', 'Menu Item created successfully.');
    }

    public function show(MenuItem $menuItem)
    {
        $menuItem->load(['menu', 'parent', 'category', 'page']);
        return view('admin.menu_items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem)
    {
        $menus = Menu::all();
        $menuItems = MenuItem::where('id', '!=', $menuItem->id)->get();
        $categories = Category::all();
        $pages = Page::all();
        return view('admin.menu_items.edit', compact('menuItem', 'menus', 'menuItems', 'categories', 'pages'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'parent_id' => 'nullable|exists:menu_items,id',
            'category_id' => 'nullable|exists:categories,id',
            'page_id' => 'nullable|exists:pages,id',
            'type' => 'required|string',
            'url' => 'nullable|string',
            'icon' => 'nullable|string',
            'target' => 'required|string',
            'position' => 'nullable|integer|min:0',
            'is_root' => 'boolean',
            'is_fluid' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $menuItem->update($validated);

        return redirect()->route('admin.menu_items.index')->with('success', 'Menu Item updated successfully.');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('admin.menu_items.index')->with('success', 'Menu Item deleted successfully.');
    }
}