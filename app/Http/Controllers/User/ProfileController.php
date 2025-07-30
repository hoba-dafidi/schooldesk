<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        return view('user.profile', ['user' => Auth::user()]);
    }

    public function edit()
    {
        return view('user.profile-edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
{
    $user = Auth::user();

    $validated = $request->validate([
        'firstname'     => 'required|string|max:255',
        'middlename'    => 'required|string|max:255',
        'lastname'      => 'required|string|max:255',
        'phone'         => 'nullable|string|max:255',
        'avatar'        => 'nullable|image|max:2048',
        'timezone'      => 'nullable|string|max:255',
        'facebook'      => 'nullable|url',
        'twitter'       => 'nullable|url',
        'linkedin'      => 'nullable|url',
        'address_line1' => 'nullable|string|max:255',
        'address_line2' => 'nullable|string|max:255',
        'city'          => 'nullable|string|max:255',
        'state'         => 'nullable|string|max:255',
        'postal_code'   => 'nullable|string|max:20',
        'country'       => 'nullable|string|max:100',
        'password'      => 'nullable|string|confirmed|min:8', // set to nullable
    ]);

    // Fill basic fields except password
    $user->fill(collect($validated)->except('password')->toArray());

    if ($request->hasFile('avatar')) {
        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
    }

    if (!empty($validated['password'])) {
        $user->password = bcrypt($validated['password']);
    }

    $user->save();

    return redirect()->route('user.profile')->with('success', 'Profile updated successfully.');
}


}
