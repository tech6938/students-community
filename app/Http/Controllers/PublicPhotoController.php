<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use Exception;
use App\Models\Profile;
use App\Models\PublicPhoto;

class PublicPhotoController extends Controller
{
    public function index($profileId)
    {
        try {
            $photos = PublicPhoto::where('profile_id', $profileId)->latest()->get();

            return ApiResponse::success(
                'Photos retrieved successfully',
                $photos
            );

        } catch (Exception $e) {
            return ApiResponse::error('Failed to fetch photos');
        }
    }

    public function store(Request $request)
    {
        try {
            $profile = auth()->user()->profile;

            if (!$profile) {
                return ApiResponse::error('Profile not found', 404);
            }

            if ($profile->photos()->count() >= 3) {
                return ApiResponse::error('Maximum 3 photos allowed', 400);
            }

            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
            ]);

            $path = $request->file('image')->store('public/photos');

            $photo = PublicPhoto::create([
                'profile_id' => $profile->id,
                'image' => $path
            ]);

            return ApiResponse::success(
                'Photo uploaded successfully',
                $photo,
                201
            );

        } catch (Exception $e) {
            return ApiResponse::error('Photo upload failed');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $photo = PublicPhoto::findOrFail($id);

            if ($photo->profile->user_id !== auth()->id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
            ]);

            if (\Storage::exists($photo->image)) {
                \Storage::delete($photo->image);
            }

            $path = $request->file('image')->store('public/photos');

            $photo->update([
                'image' => $path
            ]);

            return ApiResponse::success(
                'Photo updated successfully',
                $photo
            );

        } catch (Exception $e) {
            return ApiResponse::error('Photo update failed');
        }
    }

    public function destroy($id)
    {
        try {
            $photo = PublicPhoto::findOrFail($id);

            if ($photo->profile->user_id !== auth()->id()) {
                return ApiResponse::error('Unauthorized', 403);
            }

            if (\Storage::exists($photo->image)) {
                \Storage::delete($photo->image);
            }

            $photo->delete();

            return ApiResponse::success(
                'Photo deleted successfully',
                null
            );

        } catch (Exception $e) {
            return ApiResponse::error('Photo deletion failed');
        }
    }
}
