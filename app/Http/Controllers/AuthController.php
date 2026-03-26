<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

public function login(Request $request): JsonResponse
{
    $request->validate([
        'msisdn' => 'required',
        'password' => 'required'
    ]);

    $credentials = $request->only('msisdn', 'password');

    // Try to login using JWTAuth
    if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid phone number or password',
            'error_code' => 'INVALID_CREDENTIALS'
        ], 401);
    }

    $user = JWTAuth::user();


    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60
        ]
    ]);
}



    public function me(): JsonResponse
    {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function logout(): JsonResponse
    {
        JWTAuth::parseToken()->invalidate();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = JWTAuth::parseToken()->refresh();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ]
        ]);
    }


    public function updateProfile(Request $request): JsonResponse
{
    $user = JWTAuth::parseToken()->authenticate();

    $data = $request->validate([
        'first_name' => 'nullable|string|max:255',
        'last_name'  => 'nullable|string|max:255',
        'email'      => 'nullable|email|unique:users,email,' . $user->id,
        'dob'        => 'nullable|date',
    ]);

    $user->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Profile updated successfully',
        'data' =>$user
]);
}


public function register(Request $request)
{
    $request->validate([
        'msisdn' => 'required|unique:users,msisdn',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'dob' => 'required|date',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'msisdn' => $request->msisdn,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'dob' => $request->dob,
        'password' => bcrypt($request->password),
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json([
        'success' => true,
        'message' => 'User registered successfully',
        'data' => [
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer'
        ]
    ], 201);
}


}


