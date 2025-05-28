<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Students can only view events for all or specifically for students
        $events = Event::where(function($query) {
            $query->where('audience', 'all')
                  ->orWhere('audience', 'students');
        })
        ->where('is_active', true)
        ->orderBy('start_date', 'desc')
        ->paginate(10);
        
        return view('siswa.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented for students
        return redirect()->route('siswa.events.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not implemented for students
        return redirect()->route('siswa.events.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        
        // Check if the student is allowed to view this event
        if (!($event->audience === 'all' || $event->audience === 'students') || !$event->is_active) {
            return redirect()->route('siswa.events.index')
                ->with('error', 'You do not have permission to view this event.');
        }
        
        return view('siswa.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented for students
        return redirect()->route('siswa.events.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not implemented for students
        return redirect()->route('siswa.events.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented for students
        return redirect()->route('siswa.events.index');
    }
}
