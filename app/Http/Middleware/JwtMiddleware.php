<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Invalid',
                    'error_code' => 'TOKEN_INVALID'
                ], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token is Expired',
                    'error_code' => 'TOKEN_EXPIRED'
                ], 401);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization Token not found',
                    'error_code' => 'TOKEN_NOT_FOUND'
                ], 401);
            }
        }

        return $next($request);
    }
}
