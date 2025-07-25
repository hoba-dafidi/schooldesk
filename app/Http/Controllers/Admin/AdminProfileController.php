<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    /**
     * Show the admin profile page.
     */
    public function edit()
    {
        return view('admin.profile');
    }

    /**
     * Update the admin's profile info.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            $validated = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname'  => 'required|string|max:255',
                'phone'     => 'nullable|string|max:20',
                'bio'       => 'nullable|string|max:1000',
                'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            $user->firstname = $validated['firstname'];
            $user->lastname  = $validated['lastname'];
            $user->phone     = $validated['phone'] ?? null;
            $user->bio       = $validated['bio'] ?? null;

            if ($request->hasFile('avatar')) {
                // Delete old avatar if stored locally
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store new avatar
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->save();

            return redirect()->back()->with('toast_success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return redirect()->back()->with('toast_error', 'Something went wrong while updating profile.');
        }
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'current_password' => 'required',
                'password'         => 'required|string|min:8|confirmed',
            ]);

            // dd($request);
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withInput()->with('toast_error', 'Current password is incorrect.');
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->back()->with('toast_success', 'Password updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->with('toast_error', 'Validation failed. Please check your inputs.');
        } catch (\Exception $e) {
            return back()->with('toast_error', 'An unexpected error occurred. Please try again.');
        }
    }

}
