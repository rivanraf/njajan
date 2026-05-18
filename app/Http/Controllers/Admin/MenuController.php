<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        // withTrashed() agar menu yang di-soft-delete tetap tampil di dashboard admin
        $menus = Menu::withTrashed()->with('category')->latest()->get();
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::all(); 
        return view('admin.menu.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input (Ubah 'type' menjadi nullable string)
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'type' => 'nullable|string', // SEKARANG BOLEH ISI TEKS BEBAS (CONTOH: Hot, Ice)
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status_stok' => 'nullable|string',
        ]);

        // 2. Logika Upload Gambar
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu-images', 'public');
        }

        // 3. Simpan ke Database
        Menu::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'type' => $request->type, 
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
            'is_available' => true,
            'status_stok' => $request->status_stok ?? 'tersedia',
        ]);

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $categories = Category::all();
        return view('admin.menu.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, $id)
    {
    $menu = Menu::findOrFail($id);

    // 1. Validasi
    $request->validate([
        'name'        => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'type'        => 'nullable|string', 
        'description' => 'nullable|string',
        'price'       => 'required|numeric|min:0',
        'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        'status_stok' => 'required|string',
    ]);

    // 2. Ambil semua data kecuali image dulu
    $data = $request->except('image');

    // 3. Logika Update Gambar
    if ($request->hasFile('image')) {
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        $data['image'] = $request->file('image')->store('menu-images', 'public');
    }

    // 4. Update Data (Termasuk status_stok)
    $menu->update($data);

    return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Soft Delete: JANGAN hapus gambar karena data masih ada di DB
        // Gambar baru dihapus saat forceDelete (jika diperlukan)
        $menu->delete();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dinonaktifkan (soft delete).');
    }

    /**
     * Restore a soft-deleted menu.
     */
    public function restore($id)
    {
        $menu = Menu::withTrashed()->findOrFail($id);
        $menu->restore();

        return redirect()->route('admin.menu.index')->with('success', 'Menu berhasil dipulihkan!');
    }
}