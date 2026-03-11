<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Logic chống đăng nhập nhiều nơi
            $user = Auth::user();
            $token = \Illuminate\Support\Str::random(60);
            $user->session_token = $token;
            $user->save(); // Lưu token mới vào DB

            session(['session_token' => $token]); // Lưu token vào session hiện tại

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'error' => 'Tên đăng nhập hoặc mật khẩu không đúng.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
