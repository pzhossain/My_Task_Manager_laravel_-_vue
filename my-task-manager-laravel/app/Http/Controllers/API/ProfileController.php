<?php

namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\User;

class ProfileController extends Controller
{

    // Get user profile method
    public function getProfile(Request $request): JsonResponse
    {
        try {
        $user = Auth::user();
        $cacheKey = "user_profile_{$user->id}";

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'profile_img_url' => $user->profile_img_url,
        ];

        Cache::put($cacheKey, $data, now()->addHour());

        return response()->json([
            'status' => 'success',
            'message' => 'User profile retrieved successfully',
            'data' => $data
        ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }
    
    
    //Update profile method
    public function updateProfile(UpdateProfileRequest $request):JsonResponse
    {
        try {
        $user = Auth::user();
        $data = $request->validated();

       
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        
        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = 'profile_' . $user->id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('public/profile_images', $imageName);
            $data['profile_img_url'] = 'storage/profile_images/' . $imageName;
        }
        // Update the user profile
        unset($data['profile_picture']);
        $user->update($data);
        
        $cacheKey = "user_profile_{$user->id}";
        Cache::put($cacheKey, [
            'name' => $user->name,
            'email' => $user->email,
            'profile_img_url' => $user->profile_img_url,
        ], now()->addHour());

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'profile_img_url' => $user->profile_img_url,
            ]
        ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Profile update failed',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }
}
