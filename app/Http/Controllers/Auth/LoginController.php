<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // create a Blade view if using web
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
          $request->session()->regenerate();

          $user = Auth::user();

           // ✅ Role-based redirect
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('user')) {
            return redirect()->route('dashboard');
        }

        // Default fallback
        return redirect()->intended('/dashboard');

        //   if ($user->role === 'admin') {
        //       return redirect()->intended('/admin/dashboard');
        //   }

        //   return redirect()->intended('/user/dashboard');
        }

        // if (Auth::attempt($credentials, $request->remember)) {
        //     $request->session()->regenerate();
        //     return redirect()->intended('user/dashboard');
        // }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }
}
