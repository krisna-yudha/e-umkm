<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MapApiController extends Controller
{
    /**
     * Get all UMKM locations for map display.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllLocations(Request $request): JsonResponse
    {
        try {
            $query = Umkm::select([
                'id',
                'nama_umkm',
                'deskripsi', 
                'kategori',
                'alamat',
                'no_telepon',
                'email',
                'latitude',
                'longitude',
                'status',
                'gambar'
            ])->where('status', 'active')
              ->whereNotNull('latitude')
              ->whereNotNull('longitude');

            // Filter by category if provided
            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', $request->kategori);
            }

            // Filter by search term
            if ($request->has('search') && $request->search) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama_umkm', 'like', "%{$searchTerm}%")
                      ->orWhere('alamat', 'like', "%{$searchTerm}%")
                      ->orWhere('kategori', 'like', "%{$searchTerm}%");
                });
            }

            $umkms = $query->get();

            // Format for GeoJSON
            $features = $umkms->map(function ($umkm) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$umkm->longitude, (float)$umkm->latitude]
                    ],
                    'properties' => [
                        'id' => $umkm->id,
                        'nama_umkm' => $umkm->nama_umkm,
                        'deskripsi' => $umkm->deskripsi,
                        'kategori' => $umkm->kategori,
                        'alamat' => $umkm->alamat,
                        'no_telepon' => $umkm->no_telepon,
                        'email' => $umkm->email,
                        'gambar' => $umkm->gambar ? asset('storage/' . $umkm->gambar) : null,
                        'status' => $umkm->status
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Map locations retrieved successfully',
                'data' => [
                    'type' => 'FeatureCollection',
                    'features' => $features,
                    'total' => $features->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve map locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get UMKM locations within a specific radius.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLocationsByRadius(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:0.1|max:100', // radius in kilometers
                'kategori' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->radius ?? 10; // Default 10km

            // Using Haversine formula to calculate distance
            $query = Umkm::select([
                'id',
                'nama_umkm',
                'deskripsi',
                'kategori', 
                'alamat',
                'no_telepon',
                'email',
                'latitude',
                'longitude',
                'status',
                'gambar',
                DB::raw("
                    (6371 * acos(
                        cos(radians($latitude)) * 
                        cos(radians(latitude)) * 
                        cos(radians(longitude) - radians($longitude)) + 
                        sin(radians($latitude)) * 
                        sin(radians(latitude))
                    )) AS distance
                ")
            ])
            ->where('status', 'active')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radius)
            ->orderBy('distance');

            // Filter by category if provided
            if ($request->has('kategori') && $request->kategori) {
                $query->where('kategori', $request->kategori);
            }

            $umkms = $query->get();

            // Format for GeoJSON with distance
            $features = $umkms->map(function ($umkm) {
                return [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [(float)$umkm->longitude, (float)$umkm->latitude]
                    ],
                    'properties' => [
                        'id' => $umkm->id,
                        'nama_umkm' => $umkm->nama_umkm,
                        'deskripsi' => $umkm->deskripsi,
                        'kategori' => $umkm->kategori,
                        'alamat' => $umkm->alamat,
                        'no_telepon' => $umkm->no_telepon,
                        'email' => $umkm->email,
                        'gambar' => $umkm->gambar ? asset('storage/' . $umkm->gambar) : null,
                        'status' => $umkm->status,
                        'distance_km' => round($umkm->distance, 2)
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Nearby locations retrieved successfully',
                'data' => [
                    'type' => 'FeatureCollection',
                    'features' => $features,
                    'total' => $features->count(),
                    'search_params' => [
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'radius_km' => $radius
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve nearby locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available categories for map filtering.
     *
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = Umkm::select('kategori')
                ->where('status', 'active')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->groupBy('kategori')
                ->get()
                ->pluck('kategori')
                ->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get map statistics.
     *
     * @return JsonResponse
     */
    public function getMapStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_locations' => Umkm::where('status', 'active')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->count(),
                'by_category' => Umkm::select('kategori', DB::raw('count(*) as count'))
                    ->where('status', 'active')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->groupBy('kategori')
                    ->get(),
                'bounds' => [
                    'north' => (float)Umkm::where('status', 'active')->max('latitude'),
                    'south' => (float)Umkm::where('status', 'active')->min('latitude'),
                    'east' => (float)Umkm::where('status', 'active')->max('longitude'),
                    'west' => (float)Umkm::where('status', 'active')->min('longitude')
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Map statistics retrieved successfully',
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve map statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location details by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getLocationDetails(int $id): JsonResponse
    {
        try {
            $umkm = Umkm::with(['user', 'menus'])
                ->where('id', $id)
                ->where('status', 'active')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();

            if (!$umkm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Location not found'
                ], 404);
            }

            $locationData = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$umkm->longitude, (float)$umkm->latitude]
                ],
                'properties' => [
                    'id' => $umkm->id,
                    'nama_umkm' => $umkm->nama_umkm,
                    'deskripsi' => $umkm->deskripsi,
                    'kategori' => $umkm->kategori,
                    'alamat' => $umkm->alamat,
                    'no_telepon' => $umkm->no_telepon,
                    'email' => $umkm->email,
                    'gambar' => $umkm->gambar ? asset('storage/' . $umkm->gambar) : null,
                    'website' => $umkm->website,
                    'facebook' => $umkm->facebook,
                    'instagram' => $umkm->instagram,
                    'whatsapp' => $umkm->whatsapp,
                    'status' => $umkm->status,
                    'owner' => [
                        'name' => $umkm->user->name,
                        'email' => $umkm->user->email
                    ],
                    'menus' => $umkm->menus->map(function ($menu) {
                        return [
                            'id' => $menu->id,
                            'nama_menu' => $menu->nama_menu,
                            'deskripsi' => $menu->deskripsi,
                            'harga' => $menu->harga,
                            'gambar' => $menu->gambar ? asset('storage/' . $menu->gambar) : null,
                            'kategori_menu' => $menu->kategori_menu,
                            'is_available' => $menu->is_available
                        ];
                    })
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Location details retrieved successfully',
                'data' => $locationData
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve location details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}