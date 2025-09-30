<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UmkmMenu;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class MenuApiController extends Controller
{
    /**
     * Display a listing of menus for a specific UMKM.
     *
     * @param Request $request
     * @param int $umkmId
     * @return JsonResponse
     */
    public function index(Request $request, int $umkmId): JsonResponse
    {
        try {
            $umkm = Umkm::find($umkmId);

            if (!$umkm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'UMKM not found'
                ], 404);
            }

            $query = UmkmMenu::where('umkm_id', $umkmId);

            // Filter by availability
            if ($request->has('tersedia')) {
                $query->where('tersedia', $request->boolean('tersedia'));
            }

            // Search by name
            if ($request->has('search')) {
                $query->where('nama_menu', 'like', '%' . $request->search . '%');
            }

            // Sort by price
            if ($request->has('sort_by_price')) {
                $direction = $request->get('sort_by_price') === 'desc' ? 'desc' : 'asc';
                $query->orderBy('harga', $direction);
            } else {
                $query->orderBy('nama_menu');
            }

            $menus = $query->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Menu data retrieved successfully',
                'data' => [
                    'umkm' => $umkm,
                    'menus' => $menus
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve menu data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified menu.
     *
     * @param int $umkmId
     * @param int $menuId
     * @return JsonResponse
     */
    public function show(int $umkmId, int $menuId): JsonResponse
    {
        try {
            $menu = UmkmMenu::where('umkm_id', $umkmId)
                ->where('id', $menuId)
                ->with('umkm')
                ->first();

            if (!$menu) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Menu data retrieved successfully',
                'data' => $menu
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve menu data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created menu.
     *
     * @param Request $request
     * @param int $umkmId
     * @return JsonResponse
     */
    public function store(Request $request, int $umkmId): JsonResponse
    {
        try {
            $umkm = Umkm::find($umkmId);

            if (!$umkm) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'UMKM not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_menu' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga' => 'required|numeric|min:0',
                'tersedia' => 'boolean',
                'foto_menu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['umkm_id'] = $umkmId;
            $data['tersedia'] = $data['tersedia'] ?? true;

            // Handle file upload
            if ($request->hasFile('foto_menu')) {
                $file = $request->file('foto_menu');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('menu', $filename, 'public');
                $data['foto_menu'] = $path;
            }

            $menu = UmkmMenu::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu created successfully',
                'data' => $menu->load('umkm')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified menu.
     *
     * @param Request $request
     * @param int $umkmId
     * @param int $menuId
     * @return JsonResponse
     */
    public function update(Request $request, int $umkmId, int $menuId): JsonResponse
    {
        try {
            $menu = UmkmMenu::where('umkm_id', $umkmId)
                ->where('id', $menuId)
                ->first();

            if (!$menu) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama_menu' => 'sometimes|required|string|max:255',
                'deskripsi' => 'nullable|string',
                'harga' => 'sometimes|required|numeric|min:0',
                'tersedia' => 'boolean',
                'foto_menu' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
            if ($request->hasFile('foto_menu')) {
                // Delete old photo
                if ($menu->foto_menu) {
                    Storage::disk('public')->delete($menu->foto_menu);
                }

                $file = $request->file('foto_menu');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('menu', $filename, 'public');
                $data['foto_menu'] = $path;
            }

            $menu->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu updated successfully',
                'data' => $menu->load('umkm')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified menu.
     *
     * @param int $umkmId
     * @param int $menuId
     * @return JsonResponse
     */
    public function destroy(int $umkmId, int $menuId): JsonResponse
    {
        try {
            $menu = UmkmMenu::where('umkm_id', $umkmId)
                ->where('id', $menuId)
                ->first();

            if (!$menu) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu not found'
                ], 404);
            }

            // Delete photo if exists
            if ($menu->foto_menu) {
                Storage::disk('public')->delete($menu->foto_menu);
            }

            $menu->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Menu deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all menus across all UMKM.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function allMenus(Request $request): JsonResponse
    {
        try {
            $query = UmkmMenu::with('umkm');

            // Filter by availability
            if ($request->has('tersedia')) {
                $query->where('tersedia', $request->boolean('tersedia'));
            }

            // Search by menu name
            if ($request->has('search')) {
                $query->where('nama_menu', 'like', '%' . $request->search . '%');
            }

            // Filter by price range
            if ($request->has('min_price')) {
                $query->where('harga', '>=', $request->min_price);
            }

            if ($request->has('max_price')) {
                $query->where('harga', '<=', $request->max_price);
            }

            // Sort by price
            if ($request->has('sort_by_price')) {
                $direction = $request->get('sort_by_price') === 'desc' ? 'desc' : 'asc';
                $query->orderBy('harga', $direction);
            } else {
                $query->orderBy('nama_menu');
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $menus = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'All menus retrieved successfully',
                'data' => $menus->items(),
                'pagination' => [
                    'current_page' => $menus->currentPage(),
                    'last_page' => $menus->lastPage(),
                    'per_page' => $menus->perPage(),
                    'total' => $menus->total(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve menus',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}