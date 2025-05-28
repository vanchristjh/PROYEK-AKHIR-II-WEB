<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use Illuminate\Http\JsonResponse;

class MaterialController extends Controller
{
    /**
     * Get list of materials
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $materials = Material::with(['subject', 'teacher'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
                    
        return response()->json([
            'success' => true,
            'data' => $materials
        ]);
    }
    
    /**
     * Get a specific material
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        $material = Material::with(['subject', 'teacher'])->find($id);
        
        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $material
        ]);
    }
}
