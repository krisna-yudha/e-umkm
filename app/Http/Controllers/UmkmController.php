<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class UmkmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admin bisa melihat semua UMKM untuk monitoring
        if (Auth::user()->role === 'admin') {
            $umkms = Umkm::with('menus', 'user')
                         ->orderBy('created_at', 'desc')
                         ->get();
        } else {
            // User hanya bisa melihat UMKM miliknya sendiri
            $umkms = Umkm::where('user_id', Auth::id())
                         ->with('menus')
                         ->orderBy('created_at', 'desc')
                         ->get();
        }
        
        return Inertia::render('Umkm/Index', [
            'umkms' => $umkms,
            'isAdmin' => Auth::user()->role === 'admin'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Double check: Admin tidak boleh mengakses halaman create
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak diizinkan untuk menambahkan UMKM. Anda hanya bertugas monitoring dan mengawasi.');
        }

        return Inertia::render('Umkm/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Double check: Admin tidak boleh menyimpan UMKM
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak diizinkan untuk menambahkan UMKM. Anda hanya bertugas monitoring dan mengawasi.');
        }

        $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'operating_hours' => 'nullable|array'
        ]);

        $data = $request->except(['_token']);
        
        // Automatically assign the authenticated user as owner
        $data['user_id'] = Auth::id();
        $data['status'] = 'active'; // Set default status

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('umkm-images', 'public');
        } else {
            unset($data['gambar']);
        }

        Umkm::create($data);

        return redirect()->route('umkm.index')->with('success', 'UMKM berhasil didaftarkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Umkm $umkm)
    {
        try {
            Log::info('UMKM Show Request', [
                'umkmId' => $umkm->id,
                'userId' => Auth::id(),
                'umkmOwnerId' => $umkm->user_id,
                'userRole' => Auth::user()->role
            ]);

            // Admin bisa melihat semua UMKM untuk monitoring
            if (Auth::user()->role === 'admin') {
                $umkm->load(['menus', 'user']);
                
                return Inertia::render('Umkm/Show', [
                    'umkm' => $umkm,
                    'isAdmin' => true
                ]);
            }

            // Verify ownership untuk user biasa
            if ($umkm->user_id !== Auth::id()) {
                Log::warning('Unauthorized UMKM show attempt', [
                    'umkmId' => $umkm->id, 
                    'userId' => Auth::id(),
                    'umkmOwnerId' => $umkm->user_id
                ]);
                return redirect()->route('umkm.index')->with('error', 'Anda tidak memiliki akses ke UMKM ini.');
            }
            
            // Load relationships
            $umkm->load(['menus' => function($query) {
                $query->orderBy('nama_menu');
            }, 'user']);

            Log::info('UMKM Show Data Loaded', [
                'umkmId' => $umkm->id,
                'menusCount' => $umkm->menus->count()
            ]);
            
            return Inertia::render('Umkm/Show', [
                'umkm' => $umkm,
                'isAdmin' => false
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('UMKM Not Found', ['umkmId' => request()->route('umkm')]);
            return redirect()->route('umkm.index')->with('error', 'UMKM tidak ditemukan.');
        } catch (\Exception $e) {
            Log::error('UMKM Show Error: ' . $e->getMessage(), [
                'umkmId' => $umkm->id ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('umkm.index')->with('error', 'Terjadi kesalahan saat memuat detail UMKM.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Umkm $umkm)
    {
        // Admin tidak boleh mengedit UMKM
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak diizinkan untuk mengedit UMKM. Anda hanya bertugas monitoring dan mengawasi.');
        }

        // Verify ownership
        if ($umkm->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        return Inertia::render('Umkm/Edit', [
            'umkm' => $umkm
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Umkm $umkm)
    {
        // Admin tidak boleh mengupdate UMKM
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak diizinkan untuk mengupdate UMKM. Anda hanya bertugas monitoring dan mengawasi.');
        }

        try {
            Log::info('Update UMKM Request', [
                'umkmId' => $umkm->id,
                'all_data' => $request->all(),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'has_file' => $request->hasFile('gambar')
            ]);

            // Verify ownership
            if ($umkm->user_id !== Auth::id()) {
                Log::error('Unauthorized UMKM update attempt', ['umkmId' => $umkm->id, 'userId' => Auth::id()]);
                abort(403, 'Unauthorized');
            }
            
            $request->validate([
                'nama_umkm' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'kategori' => 'required|string|max:255',
                'alamat' => 'required|string',
                'no_telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'facebook' => 'nullable|string|max:255',
                'instagram' => 'nullable|string|max:255',
                'twitter' => 'nullable|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'website' => 'nullable|url|max:255',
                'operating_hours' => 'nullable|array'
            ]);

            Log::info('UMKM validation passed');

            $data = $request->except(['_method', '_token']);
            
            // Ensure user_id cannot be changed
            unset($data['user_id']);

            if ($request->hasFile('gambar')) {
                Log::info('Processing file upload');
                if ($umkm->gambar) {
                    Storage::disk('public')->delete($umkm->gambar);
                }
                $data['gambar'] = $request->file('gambar')->store('umkm-images', 'public');
                Log::info('File uploaded', ['path' => $data['gambar']]);
            } else {
                // Don't update gambar field if no new file
                unset($data['gambar']);
            }

            $umkm->update($data);
            Log::info('UMKM updated successfully', ['umkmId' => $umkm->id]);

            return redirect()->route('umkm.show', $umkm)->with('success', 'UMKM berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('UMKM Validation Error', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('UMKM Update Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error updating UMKM: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Umkm $umkm)
    {
        // Prevent admin from deleting UMKM
        if (Auth::user()->role === 'admin') {
            return redirect()->route('dashboard')->with('error', 'Admin tidak diizinkan untuk mengelola UMKM. Anda hanya bertugas monitoring dan mengawasi.');
        }
        
        // Verify ownership
        if ($umkm->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Load menus for file cleanup
        $umkm->load('menus');
        
        // Delete associated files
        if ($umkm->gambar) {
            Storage::disk('public')->delete($umkm->gambar);
        }
        
        // Delete associated menus and their images
        foreach ($umkm->menus as $menu) {
            if ($menu->gambar_menu) {
                Storage::disk('public')->delete($menu->gambar_menu);
            }
        }
        
        $umkm->delete();

        return redirect()->route('umkm.index')->with('success', 'UMKM berhasil dihapus!');
    }

    /**
     * Toggle the status of the specified UMKM.
     */
    public function toggleStatus(Umkm $umkm)
    {
        // Verify ownership - only owner can toggle their UMKM status
        if ($umkm->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Toggle status
        $umkm->status = $umkm->status === 'active' ? 'inactive' : 'active';
        $umkm->save();
        
        $statusText = $umkm->status === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()->with('success', "UMKM berhasil {$statusText}!");
    }
}
