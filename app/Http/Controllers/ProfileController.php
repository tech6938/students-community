<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
    use App\Helpers\ApiResponse;
    use Exception;
use App\Models\Profile;
use App\Models\PublicPhoto;
class ProfileController extends Controller
{


public function index()
{
    try {
        $profiles = Profile::with('photos')->latest()->get();

        return ApiResponse::success(
            'Profiles retrieved successfully',
            $profiles
        );

    } catch (Exception $e) {
        return ApiResponse::error('Failed to fetch profiles');
    }
}

public function show($id)
{
    try {
        $profile = Profile::with('photos')->findOrFail($id);

        return ApiResponse::success(
            'Profile retrieved successfully',
            $profile
        );

    } catch (Exception $e) {
        return ApiResponse::error('Profile not found', 404);
    }
}

public function store(Request $request)
{
    try {
        if (auth()->user()->profile) {
            return ApiResponse::error('Profile already exists', 400);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:profiles,username',
            'home_school' => 'nullable|string|max:255',
            'abroad_school' => 'nullable|string|max:255',
            'home_city' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:255',
            'languages' => 'nullable|array',
            'interests' => 'nullable|array',
        ]);

        $data['user_id'] = auth()->id();

        $profile = Profile::create($data);

        return ApiResponse::success(
            'Profile created successfully',
            $profile,
            201
        );

    } catch (Exception $e) {
        return ApiResponse::error('Profile creation failed');
    }
}

public function update(Request $request, $id)
{
    try {
        $profile = Profile::findOrFail($id);

        if ($profile->user_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'home_school' => 'nullable|string|max:255',
            'abroad_school' => 'nullable|string|max:255',
            'home_city' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:255',
            'languages' => 'nullable|array',
            'interests' => 'nullable|array',
        ]);

        unset($data['username']); // ❌ prevent update

        $profile->update($data);

        return ApiResponse::success(
            'Profile updated successfully',
            $profile
        );

    } catch (Exception $e) {
        return ApiResponse::error('Profile update failed');
    }
}

public function destroy($id)
{
    try {
        $profile = Profile::findOrFail($id);

        if ($profile->user_id !== auth()->id()) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $profile->delete();

        return ApiResponse::success(
            'Profile deleted successfully',
            null
        );

    } catch (Exception $e) {
        return ApiResponse::error('Profile deletion failed');
    }
}
}
