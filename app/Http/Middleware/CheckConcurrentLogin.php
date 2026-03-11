<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckConcurrentLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // So sánh token trong DB với token trong Session
            if ($user->session_token !== session('session_token')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Trả về kèm thông báo lỗi
                return redirect('/login')->withErrors([
                    'error' => 'Tài khoản của bạn đã được đăng nhập ở một thiết bị/trình duyệt khác!'
                ]);
            }
        }

        return $next($request);
    }
}