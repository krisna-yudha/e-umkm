<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UmkmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $umkms = Umkm::with('user')->get();
        
        return Inertia::render('Umkm/Index', [
            'umkms' => $umkms
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Umkm/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('umkm-images', 'public');
        }

        Umkm::create($data);

        return redirect()->route('umkm.index')->with('success', 'UMKM berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Umkm $umkm)
    {
        return Inertia::render('Umkm/Show', [
            'umkm' => $umkm->load('user')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Umkm $umkm)
    {
        return Inertia::render('Umkm/Edit', [
            'umkm' => $umkm
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Umkm $umkm)
    {
        $request->validate([
            'nama_umkm' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar')) {
            if ($umkm->gambar) {
                Storage::disk('public')->delete($umkm->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('umkm-images', 'public');
        }

        $umkm->update($data);

        return redirect()->route('umkm.index')->with('success', 'UMKM berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Umkm $umkm)
    {
        if ($umkm->gambar) {
            Storage::disk('public')->delete($umkm->gambar);
        }
        
        $umkm->delete();

        return redirect()->route('umkm.index')->with('success', 'UMKM berhasil dihapus!');
    }
}
