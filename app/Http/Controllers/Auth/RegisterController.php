<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register'); // create view as needed
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname'  => $data['lastname'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'is_active' => true,
            'tenant_id' => null, // or set dynamically
        ]);

        // 🔥 Assign default role
        $user->assignRole('user'); // or 'admin', etc.

        Auth::login($user);

        // ✅ Trigger the verification email
        $user->sendEmailVerificationNotification();

         // ✅ Redirect to verification notice
        return redirect()->route('verification.notice');
        // return redirect()->route('dashboard');
    }
}
