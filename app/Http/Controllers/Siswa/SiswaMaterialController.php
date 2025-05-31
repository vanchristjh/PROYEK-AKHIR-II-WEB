<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SiswaMaterialController extends Controller
{
    /**
     * Display a listing of the materials.
     */    
    public function index()
    {
        // Get student from auth user
        $user = Auth::user();
        $classroomIds = collect();

        // Add direct classroom_id if exists
        if ($user->classroom_id) {
            $classroomIds->push($user->classroom_id);
        }

        // Add classrooms from many-to-many relationship
        $manyToManyClassroomIds = $user->classrooms()->pluck('classrooms.id');
        if ($manyToManyClassroomIds->isNotEmpty()) {
            $classroomIds = $classroomIds->merge($manyToManyClassroomIds);
        }

        // Get materials for the student's classrooms with subject relationship
        $materials = Material::with(['subject', 'teacher'])
            ->where('is_active', true)
            ->where(function($query) use ($classroomIds) {
                return $query->whereHas('classrooms', function ($q) use ($classroomIds) {
                    $q->whereIn('classrooms.id', $classroomIds);
                });
            })
            ->latest()
            ->paginate(10);
        
        return view('siswa.materials.index', compact('materials'));
    }

    /**
     * Display the specified material.
     */    
    public function show(Material $material)
    {
        // Get student from auth user
        $user = Auth::user();
        $classroomIds = collect();

        // Add direct classroom_id if exists
        if ($user->classroom_id) {
            $classroomIds->push($user->classroom_id);
        }

        // Add classrooms from many-to-many relationship
        $manyToManyClassroomIds = $user->classrooms()->pluck('classrooms.id');
        if ($manyToManyClassroomIds->isNotEmpty()) {
            $classroomIds = $classroomIds->merge($manyToManyClassroomIds);
        }

        // Check if material is active and assigned to student's classroom
        if (!$material->is_active || !$classroomIds->count() || !$material->classrooms()->whereIn('classroom_id', $classroomIds)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke materi ini.');
        }
        
        // Check if material is within valid date range
        $now = now();
        if ($material->publish_date && $now->lt($material->publish_date)) {
            abort(403, 'Materi ini belum dapat diakses.');
        }
        if ($material->expiry_date && $now->gt($material->expiry_date)) {
            abort(403, 'Materi ini sudah tidak dapat diakses.');
        }

        // Load relationships
        $material->load(['subject', 'teacher']);
        
        return view('siswa.materials.show', compact('material'));
    }
}
