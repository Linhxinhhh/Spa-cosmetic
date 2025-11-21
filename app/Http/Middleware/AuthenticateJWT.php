<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateJWT
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = session('jwt_token');
            if (!$token) {
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
            }
            JWTAuth::setToken($token);
            if (!$user = JWTAuth::authenticate()) {
                return redirect()->route('login')->with('error', 'Token không hợp lệ');
            }
        } catch (JWTException $e) {
            return redirect()->route('login')->with('error', 'Token không hợp lệ hoặc đã hết hạn');
        }

        return $next($request);
    }
}