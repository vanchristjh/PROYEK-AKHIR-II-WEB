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
     */    public function index()
    {
        // Get student's class ID from auth user
        $user = Auth::user();
        $studentClass = $user->student->class_id ?? null;
          // Get materials for the student's class with subject relationship
        $materials = Material::with('subject')            ->when($studentClass, function ($query) use ($studentClass) {
                return $query->whereHas('classrooms', function ($q) use ($studentClass) {
                    $q->where('classrooms.id', $studentClass);
                });
            })
            ->paginate(10);
        
        return view('siswa.materials.index', compact('materials'));
    }

    /**
     * Display the specified material.
     */    public function show(Material $material)
    {
        // Load relationships without using 'user' directly on User model
        $material->load(['subject', 'teacher']);
        
        // Return view with the material
        return view('siswa.materials.show', compact('material'));
    }
}
