<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavItem;
use Illuminate\Http\Request;

class NavItemController extends Controller
{
    public function index()
    {
        $navItems = NavItem::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with('children')
            ->get();

        $parents = NavItem::whereNull('parent_id')->orderBy('sort_order')->get();

        return view('nav-items.index', compact('navItems', 'parents'));
    }
    public function Modules()
    {
        return view('nav-items.modules');
    }
    public function store(Request $request)
    {
        $data = $this->validated($request);

        NavItem::create($data);

        return back()->with('success', 'Menu item created.');
    }

    public function update(Request $request, NavItem $navItem)
    {
        $data = $this->validated($request, $navItem->id);

        $navItem->update($data);

        return back()->with('success', 'Menu item updated.');
    }

    public function destroy(NavItem $navItem)
    {
        $navItem->delete();

        return back()->with('success', 'Menu item deleted.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'parent_id'  => 'nullable|exists:nav_items,id',
            'module_key' => 'required|string|max:60|unique:nav_items,module_key' . ($ignoreId ? ",$ignoreId" : ''),
            'label'      => 'required|string|max:100',
            'icon'       => 'nullable|string|max:60',
            'route'      => 'nullable|string|max:150',
            'sort_order' => 'nullable|integer',
            'status'     => 'nullable|boolean',
        ]);
    }
}
