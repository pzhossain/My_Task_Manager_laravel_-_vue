<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{

    // User Registration
    // ** This method handles user registration, validates input, and creates a new user.
    
    public function userRegistration(RegisterRequest $request):JsonResponse
    {
    try{        
        $user = User::create([
            'name'     => $request->input('name'),
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User Registration Successful',
            ], 200);


        }catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }        
    }


    // User Login
    // ** This method handles user login, validates credentials, and returns a token if successful.
     
    public function userLogin(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Invalid credentials',
                ], 401);
            }
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'user' => $user->only(['id', 'name', 'email']),
                'token' => $token,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    // User Logout
    // ** This method handles user logout, revokes the current token, and clears the cache
    
    public function userLogout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $cacheKey = "user_profile_{$user->id}";
            Cache::forget($cacheKey);
            // Revoke the user's current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logout successful',
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }
    
}
