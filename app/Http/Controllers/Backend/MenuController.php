<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::withCount('items')->paginate(15);
        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menus.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'is_active' => 'boolean'
        ]);

        Menu::create($request->all());

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu created successfully.');
    }

    public function show(Menu $menu)
    {
        $menu->load('items.category', 'items.page', 'items.children');
        return view('admin.menus.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'is_active' => 'boolean'
        ]);

        $menu->update($request->all());

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }

    public function createItem(Menu $menu)
    {
        $categories = Category::where('is_active', true)->get();
        $pages = Page::where('is_active', true)->get();
        $parentItems = $menu->items()->whereNull('parent_id')->get();
        
        return view('admin.menu-items.create', compact('menu', 'categories', 'pages', 'parentItems'));
    }

    public function storeItem(Request $request, Menu $menu)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:menu_items,id',
            'category_id' => 'nullable|exists:categories,id',
            'page_id' => 'nullable|exists:pages,id',
            'type' => 'required|string|in:category,page,custom,url',
            'url' => 'nullable|string',
            'icon' => 'nullable|string',
            'target' => 'required|string|in:_self,_blank',
            'position' => 'nullable|integer',
            'is_root' => 'boolean',
            'is_fluid' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $menu->items()->create($request->all());

        return redirect()->route('admin.menus.show', $menu)
            ->with('success', 'Menu item created successfully.');
    }

    public function editItem(Menu $menu, MenuItem $menuItem)
    {
        $categories = Category::where('is_active', true)->get();
        $pages = Page::where('is_active', true)->get();
        $parentItems = $menu->items()->whereNull('parent_id')->where('id', '!=', $menuItem->id)->get();
        
        return view('admin.menu-items.edit', compact('menu', 'menuItem', 'categories', 'pages', 'parentItems'));
    }

    public function updateItem(Request $request, Menu $menu, MenuItem $menuItem)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:menu_items,id',
            'category_id' => 'nullable|exists:categories,id',
            'page_id' => 'nullable|exists:pages,id',
            'type' => 'required|string|in:category,page,custom,url',
            'url' => 'nullable|string',
            'icon' => 'nullable|string',
            'target' => 'required|string|in:_self,_blank',
            'position' => 'nullable|integer',
            'is_root' => 'boolean',
            'is_fluid' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $menuItem->update($request->all());

        return redirect()->route('admin.menus.show', $menu)
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroyItem(Menu $menu, MenuItem $menuItem)
    {
        $menuItem->delete();

        return redirect()->route('admin.menus.show', $menu)
            ->with('success', 'Menu item deleted successfully.');
    }
}