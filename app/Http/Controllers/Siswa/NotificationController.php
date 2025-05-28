<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\AssignmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notifications = AssignmentNotification::where('user_id', Auth::id())
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('siswa.notifications.index', compact('notifications'));
    }
    
    /**
     * Show a specific notification.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $notification = AssignmentNotification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        // Mark as read if not already
        if (!$notification->is_read) {
            $notification->markAsRead();
        }
        
        // If this is an assignment reminder, prepare to redirect to assignment
        if ($notification->type === 'assignment_reminder' && !empty($notification->data['assignment_id'])) {
            $assignmentId = $notification->data['assignment_id'];
            return redirect()->route('siswa.assignments.show', $assignmentId);
        }
        
        return view('siswa.notifications.show', compact('notification'));
    }
    
    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = AssignmentNotification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notifikasi ditandai sebagai telah dibaca.');
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        AssignmentNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            
        return redirect()->back()->with('success', 'Semua notifikasi ditandai sebagai telah dibaca.');
    }
    
    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = AssignmentNotification::where('user_id', Auth::id())
            ->findOrFail($id);
            
        $notification->delete();
        
        return redirect()->route('siswa.notifications.index')->with('success', 'Notifikasi berhasil dihapus.');
    }
}
