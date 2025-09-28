<?php

namespace App\Http\Controllers;

use App\Models\Umkm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
    }

    public function mapping()
    {
        $umkms = Umkm::with('user')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('status', 'active')
            ->get();

        return Inertia::render('Mapping', [
            'umkms' => $umkms
        ]);
    }

    public function publicUmkmList()
    {
        $umkms = Umkm::with('user')
            ->where('status', 'active')
            ->get();

        return Inertia::render('PublicUmkmList', [
            'umkms' => $umkms
        ]);
    }
}
