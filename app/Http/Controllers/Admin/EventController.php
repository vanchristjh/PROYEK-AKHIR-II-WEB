<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::with('creator')->orderBy('start_date', 'desc')->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'audience' => 'required|in:all,teachers,students,admin',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $eventData = $request->except(['image']);
        $eventData['created_by'] = Auth::id();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'event_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/events', $filename);
            $eventData['image_path'] = str_replace('public/', '', $path);
        }

        $event = Event::create($eventData);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with('creator')->findOrFail($id);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = Event::findOrFail($id);
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'nullable|string|max:255',
            'audience' => 'required|in:all,teachers,students,admin',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $event = Event::findOrFail($id);
        $eventData = $request->except(['image']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($event->image_path && Storage::exists('public/' . $event->image_path)) {
                Storage::delete('public/' . $event->image_path);
            }
            
            $image = $request->file('image');
            $filename = 'event_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/events', $filename);
            $eventData['image_path'] = str_replace('public/', '', $path);
        }

        $event->update($eventData);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::findOrFail($id);
        
        // Delete the event image if it exists
        if ($event->image_path && Storage::exists('public/' . $event->image_path)) {
            Storage::delete('public/' . $event->image_path);
        }
        
        $event->delete();
        
        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
        //
    }
}
