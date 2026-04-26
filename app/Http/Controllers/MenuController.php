<?php
// app/Http/Controllers/MenuController.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $query = MenuItem::with('category')->where('is_available', true);

        if ($request->category) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->orderBy('is_featured', 'desc')->orderBy('name')->get();
        $featured  = MenuItem::where('is_featured', true)->where('is_available', true)->take(4)->get();

        return view('pages.home', compact('categories', 'menuItems', 'featured'));
    }

    // Admin: manage menus
    public function adminIndex()
    {
        $items = MenuItem::with('category')->orderBy('category_id')->orderBy('name')->get();
        return view('pages.admin.menu-index', compact('items'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('pages.admin.menu-form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'is_available' => 'boolean',
            'is_featured'  => 'boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $data['is_available'] = $request->boolean('is_available', true);
        $data['is_featured']  = $request->boolean('is_featured');

        MenuItem::create($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(MenuItem $menuItem)
    {
        $categories = Category::where('is_active', true)->get();
        return view('pages.admin.menu-form', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'is_available' => 'boolean',
            'is_featured'  => 'boolean',
            'image'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        $data['is_available'] = $request->boolean('is_available');
        $data['is_featured']  = $request->boolean('is_featured');

        $menuItem->update($data);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(MenuItem $menuItem)
    {
        $menuItem->delete();
        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}
