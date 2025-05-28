<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SchoolClass; // Make sure you have a model for classes

class ClassController extends Controller
{
    /**
     * Display a listing of the classes.
     */
    public function index()
    {
        $classes = SchoolClass::all();
        return view('admin.class.index', compact('classes'));
    }

    /**
     * Show the form for creating a new class.
     */
    public function create()
    {
        return view('admin.class.create');
    }

    /**
     * Store a newly created class in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Add other validation rules as needed
        ]);

        SchoolClass::create($validated);

        return redirect()->route('admin.class')->with('success', 'Kelas berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit($id)
    {
        $class = SchoolClass::findOrFail($id);
        return view('admin.class.edit', compact('class'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Add other validation rules as needed
        ]);

        $class->update($validated);

        return redirect()->route('admin.class')->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        $class->delete();

        return redirect()->route('admin.class')->with('success', 'Kelas berhasil dihapus.');
    }
}
