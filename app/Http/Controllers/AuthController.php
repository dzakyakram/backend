<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!Auth::guard('web')->attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $user = Auth::guard('web')->user();

        if ($user->status === 'nonaktif') {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        if (!in_array($user->role, ['admin', 'pengelola'])) {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Akses ditolak. Gunakan aplikasi mobile.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(
            $user->role === 'admin'
                ? route('admin.dashboard')
                : route('pengelola.dashboard')
        );
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
