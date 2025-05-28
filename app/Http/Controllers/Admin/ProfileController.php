<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog; // Add this import statement

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $recentActivities = ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.profile.show', compact('user', 'recentActivities'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        // Validate the request data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // 2MB Max with specific formats
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'remove_avatar' => ['nullable', 'in:0,1'], // Can be either 0 or 1
        ]);
        
        try {
            // Update basic profile info
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            
            // Handle avatar removal request
            if ($request->has('remove_avatar') && $request->remove_avatar == '1') {
                // Delete avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                // Clear avatar field
                $user->avatar = null;
            } 
            // Handle avatar upload
            else if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                // Store the new avatar
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                
                if (!$avatarPath) {
                    throw new \Exception('Failed to upload avatar. Please try again.');
                }
                
                $user->avatar = $avatarPath;
            }
            
            // Update password if provided
            if ($request->filled('password')) {
                $user->password = Hash::make($validated['password']);
                
                // Log password change activity
                ActivityLogger::log(
                    'password_changed',
                    'Password berhasil diperbarui'
                );
            }
            
            // Save all changes
            $user->save();
            
            // Log profile update activity
            ActivityLogger::log(
                'profile_updated',
                'Profil berhasil diperbarui'
            );
            
            return redirect()->route('admin.profile.show')
                ->with('success', 'Profil berhasil diperbarui.');
                
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Profile update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage());
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
            
            // Log activity
            ActivityLogger::log(
                'password_changed',
                'Password berhasil diperbarui'
            );
        }
        
        $user->save();
        
        // Log activity
        ActivityLogger::log(
            'profile_updated',
            'Profil berhasil diperbarui'
        );
        
        return redirect()->route('admin.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
