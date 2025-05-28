<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = Student::with('class')->latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show form for creating a new student.
     */
    public function create()
    {
        $classes = SchoolClass::all();
        return view('students.create', compact('classes'));
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:students,nis',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'class_id' => 'required|exists:school_classes,id',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Student::create($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show($id)
    {
        $student = Student::with('class')->findOrFail($id);
        return view('students.show', compact('student'));
    }

    /**
     * Show form for editing student.
     */
    public function edit($id)
    {
        $student = Student::findOrFail($id);
        $classes = SchoolClass::all();
        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:students,nis,'.$id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'class_id' => 'required|exists:school_classes,id',
            'birth_date' => 'required|date',
            'address' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $student->update($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully');
    }

    /**
     * Remove the specified student.
     */
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully');
    }
}
