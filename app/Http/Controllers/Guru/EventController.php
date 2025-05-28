<?php

namespace App\Http\Controllers\Guru;

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
        // Teachers can only view events for all or specifically for teachers
        $events = Event::where(function($query) {
            $query->where('audience', 'all')
                  ->orWhere('audience', 'teachers');
        })
        ->where('is_active', true)
        ->orderBy('start_date', 'desc')
        ->paginate(10);
        
        return view('guru.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented for teachers
        return redirect()->route('guru.events.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Not implemented for teachers
        return redirect()->route('guru.events.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        
        // Check if the teacher is allowed to view this event
        if (!($event->audience === 'all' || $event->audience === 'teachers') || !$event->is_active) {
            return redirect()->route('guru.events.index')
                ->with('error', 'You do not have permission to view this event.');
        }
        
        return view('guru.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented for teachers
        return redirect()->route('guru.events.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Not implemented for teachers
        return redirect()->route('guru.events.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented for teachers
        return redirect()->route('guru.events.index');
    }
}
