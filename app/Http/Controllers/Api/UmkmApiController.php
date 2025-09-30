<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\UmkmMenu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UmkmApiController extends Controller
{
    /**
     * Display a listing of all UMKM.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Umkm::with(['user:id,name,email']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by category
            if ($request->has('kategori')) {
                $query->where('kategori', 'like', '%' . $request->kategori . '%');
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('nama_umkm', 'like', '%' . $request->search . '%');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $umkms = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'UMKM data retrieved successfully',
                'data' => $umkms->items(),
                'pagination' => [
                    'current_page' => $umkms->currentPage(),
                    'last_page' => $umkms->lastPage(),
                    'per_page' => $umkms->perPage(),
                    'total' => $umkms->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve UMKM data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified UMKM.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $umkm = Umkm::with(['user:id,name,email', 'menus'])->find($id);

            if (!$umkm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'UMKM not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'UMKM data retrieved successfully',
                'data' => $umkm
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve UMKM data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created UMKM.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_umkm' => 'required|string|max:255',
                'pemilik' => 'required|string|max:255',
                'alamat' => 'required|string',
                'telepon' => 'required|string|max:15',
                'email' => 'nullable|email|max:255',
                'kategori' => 'required|string|max:100',
                'deskripsi' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'status' => 'in:active,inactive',
                'jam_buka' => 'nullable|string|max:10',
                'jam_tutup' => 'nullable|string|max:10',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['status'] = $data['status'] ?? 'active';
            $data['user_id'] = Auth::id() ?? 1; // Default to admin if no auth

            // Handle file upload
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('umkm', $filename, 'public');
                $data['foto'] = $path;
            }

            $umkm = Umkm::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'UMKM created successfully',
                'data' => $umkm->load('user:id,name,email')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create UMKM',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified UMKM.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $umkm = Umkm::find($id);

            if (!$umkm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'UMKM not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_umkm' => 'sometimes|required|string|max:255',
                'pemilik' => 'sometimes|required|string|max:255',
                'alamat' => 'sometimes|required|string',
                'telepon' => 'sometimes|required|string|max:15',
                'email' => 'nullable|email|max:255',
                'kategori' => 'sometimes|required|string|max:100',
                'deskripsi' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'status' => 'in:active,inactive',
                'jam_buka' => 'nullable|string|max:10',
                'jam_tutup' => 'nullable|string|max:10',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Handle file upload
            if ($request->hasFile('foto')) {
                // Delete old photo
                if ($umkm->foto) {
                    Storage::disk('public')->delete($umkm->foto);
                }

                $file = $request->file('foto');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('umkm', $filename, 'public');
                $data['foto'] = $path;
            }

            $umkm->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'UMKM updated successfully',
                'data' => $umkm->load('user:id,name,email')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update UMKM',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified UMKM.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $umkm = Umkm::find($id);

            if (!$umkm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'UMKM not found'
                ], 404);
            }

            // Delete photo if exists
            if ($umkm->foto) {
                Storage::disk('public')->delete($umkm->foto);
            }

            $umkm->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'UMKM deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete UMKM',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get UMKM statistics.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalUmkm = Umkm::count();
            $activeUmkm = Umkm::where('status', 'active')->count();
            $inactiveUmkm = Umkm::where('status', 'inactive')->count();

            $categoryStats = Umkm::selectRaw('kategori, COUNT(*) as count')
                ->groupBy('kategori')
                ->get();

            $monthlyStats = Umkm::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Statistics retrieved successfully',
                'data' => [
                    'total_umkm' => $totalUmkm,
                    'active_umkm' => $activeUmkm,
                    'inactive_umkm' => $inactiveUmkm,
                    'category_stats' => $categoryStats,
                    'monthly_stats' => $monthlyStats
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get UMKM by location proximity.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function nearby(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'radius' => 'nullable|numeric|min:0.1|max:100', // in kilometers
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
            $radius = $request->get('radius', 10); // default 10km

            $umkms = Umkm::selectRaw("*, 
                ( 6371 * acos( cos( radians(?) ) * 
                cos( radians( latitude ) ) * 
                cos( radians( longitude ) - radians(?) ) + 
                sin( radians(?) ) * 
                sin( radians( latitude ) ) ) ) AS distance", 
                [$latitude, $longitude, $latitude])
                ->where('status', 'active')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('distance', '<', $radius)
                ->orderBy('distance')
                ->with('user:id,name,email')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Nearby UMKM retrieved successfully',
                'data' => $umkms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve nearby UMKM',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}