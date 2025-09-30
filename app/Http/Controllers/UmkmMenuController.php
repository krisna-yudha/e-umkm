<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use App\Models\UmkmMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UmkmMenuController extends Controller
{
    public function index($umkmId)
    {
        try {
            // Find UMKM dan pastikan user hanya bisa akses menu UMKM miliknya
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            $menus = $umkm->menus()->orderBy('nama_menu')->get();

            return Inertia::render('UmkmMenu/Index', [
                'umkm' => $umkm,
                'menus' => $menus
            ]);
        } catch (\Exception $e) {
            Log::error('UMKM Menu Index Error: ' . $e->getMessage());
            return redirect()->route('umkm.index')->with('error', 'Error loading UMKM menu: ' . $e->getMessage());
        }
    }

    public function create($umkmId)
    {
        try {
            // Find UMKM dan pastikan user hanya bisa tambah menu ke UMKM miliknya
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            return Inertia::render('UmkmMenu/Create', [
                'umkm' => $umkm
            ]);
        } catch (\Exception $e) {
            Log::error('UMKM Menu Create Error: ' . $e->getMessage());
            return redirect()->route('umkm.index')->with('error', 'Error loading menu creation form: ' . $e->getMessage());
        }
    }

    public function store(Request $request, $umkmId)
    {
        try {
            // Find UMKM dan pastikan user hanya bisa tambah menu ke UMKM miliknya
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            $validated = $request->validate([
                'nama_menu' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'kategori_menu' => 'nullable|string|max:255',
                'gambar_menu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tersedia' => 'nullable|boolean'
            ]);

            // Handle boolean field properly
            $validated['tersedia'] = $request->has('tersedia') ? (bool)$request->tersedia : true;

            if ($request->hasFile('gambar_menu')) {
                $validated['gambar_menu'] = $request->file('gambar_menu')->store('menu-images', 'public');
            }

            $validated['umkm_id'] = $umkm->id;

            UmkmMenu::create($validated);

            return redirect()->route('umkm.menu.index', $umkmId)->with('success', 'Menu berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('UMKM Menu Store Error: ' . $e->getMessage());
            return redirect()->route('umkm.menu.index', $umkmId)->with('error', 'Error creating menu: ' . $e->getMessage());
        }
    }

    public function show($umkmId, $menuId)
    {
        try {
            // Find UMKM dan menu dengan verifikasi ownership
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            $menu = UmkmMenu::where('id', $menuId)
                            ->where('umkm_id', $umkm->id)
                            ->first();
            
            if (!$menu) {
                abort(404, 'Menu not found');
            }

            return Inertia::render('UmkmMenu/Show', [
                'umkm' => $umkm,
                'menu' => $menu
            ]);
        } catch (\Exception $e) {
            Log::error('UMKM Menu Show Error: ' . $e->getMessage());
            return redirect()->route('umkm.menu.index', $umkmId)->with('error', 'Error loading menu details: ' . $e->getMessage());
        }
    }

    public function edit($umkmId, $menuId)
    {
        try {
            // Find UMKM dan menu dengan verifikasi ownership
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            $menu = UmkmMenu::where('id', $menuId)
                            ->where('umkm_id', $umkm->id)
                            ->first();
            
            if (!$menu) {
                abort(404, 'Menu not found');
            }

            return Inertia::render('UmkmMenu/Edit', [
                'umkm' => $umkm,
                'menu' => $menu
            ]);
        } catch (\Exception $e) {
            Log::error('UMKM Menu Edit Error: ' . $e->getMessage());
            return redirect()->route('umkm.menu.index', $umkmId)->with('error', 'Error loading menu edit form: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $umkmId, $menuId)
    {
        try {
            Log::info('Update Menu Request', [
                'umkmId' => $umkmId,
                'menuId' => $menuId,
                'data' => $request->all()
            ]);

            // Find UMKM dan menu dengan verifikasi ownership
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                Log::error('UMKM not found or unauthorized', ['umkmId' => $umkmId, 'userId' => Auth::id()]);
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            $menu = UmkmMenu::where('id', $menuId)
                            ->where('umkm_id', $umkm->id)
                            ->first();
            
            if (!$menu) {
                Log::error('Menu not found', ['menuId' => $menuId, 'umkmId' => $umkmId]);
                abort(404, 'Menu not found');
            }

            $validated = $request->validate([
                'nama_menu' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'kategori_menu' => 'nullable|string|max:255',
                'gambar_menu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'tersedia' => 'nullable|boolean'
            ]);

            // Handle boolean field properly
            $validated['tersedia'] = $request->has('tersedia') ? (bool)$request->tersedia : true;

            Log::info('Validation passed', ['validated' => $validated]);

            if ($request->hasFile('gambar_menu')) {
                // Hapus gambar lama jika ada
                if ($menu->gambar_menu && Storage::disk('public')->exists($menu->gambar_menu)) {
                    Storage::disk('public')->delete($menu->gambar_menu);
                }
                $validated['gambar_menu'] = $request->file('gambar_menu')->store('menu-images', 'public');
                Log::info('File uploaded', ['path' => $validated['gambar_menu']]);
            }

            $menu->update($validated);
            Log::info('Menu updated successfully', ['menuId' => $menuId]);

            return redirect()->route('umkm.menu.index', $umkmId)->with('success', 'Menu berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('UMKM Menu Update Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('umkm.menu.index', $umkmId)->with('error', 'Error updating menu: ' . $e->getMessage());
        }
    }

    public function destroy($umkmId, $menuId)
    {
        try {
            // Find UMKM dan menu dengan verifikasi ownership
            $umkm = Umkm::where('id', $umkmId)
                        ->where('user_id', Auth::id())
                        ->first();
            
            if (!$umkm) {
                abort(404, 'UMKM not found or you do not have permission to access it');
            }

            $menu = UmkmMenu::where('id', $menuId)
                            ->where('umkm_id', $umkm->id)
                            ->first();
            
            if (!$menu) {
                abort(404, 'Menu not found');
            }

            // Hapus gambar jika ada
            if ($menu->gambar_menu && Storage::disk('public')->exists($menu->gambar_menu)) {
                Storage::disk('public')->delete($menu->gambar_menu);
            }

            $menu->delete();

            return redirect()->route('umkm.menu.index', $umkmId)->with('success', 'Menu berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('UMKM Menu Destroy Error: ' . $e->getMessage());
            return redirect()->route('umkm.menu.index', $umkmId)->with('error', 'Error deleting menu: ' . $e->getMessage());
        }
    }
}
