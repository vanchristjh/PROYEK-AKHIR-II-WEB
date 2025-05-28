<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }
    
    /**
     * Mark notifications as read.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:notifications,id'
        ]);
        
        $user = Auth::user();
        $user->notifications()->whereIn('id', $request->ids)->update(['read_at' => now()]);
        
        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read'
        ]);
    }
    
    /**
     * Remove the specified notification from storage.
     *
     * @param  \App\Models\Notification  $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Notification $notification)
    {
        $user = Auth::user();
        
        // Check if the notification belongs to the user
        if ($notification->notifiable_id != $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
    
    /**
     * Store the FCM token for push notifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeFcmToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);
        
        $user = Auth::user();
        $user->fcm_token = $request->token;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'FCM token updated'
        ]);
    }
}
