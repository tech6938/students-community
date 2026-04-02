<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use app\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    // public function index()
    // {
    //     try {
    //         $profiles = Profile::with('photos')->latest()->get();
    //         return ApiResponse::success(
    //             'Profiles retrieved successfully',
    //             $profiles
    //         );
    //     } catch (Exception $e) {
    //         return ApiResponse::error('Failed to fetch profiles');
    //     }
    // }

    // public function index()

            // return $user->id;
            // $profile = Profile::where('user_id', $user->id)->with('photos')->get();
            // return $profile;

    // {
    //     try {
    //         $profiles = Profile::with('photos')->latest()->get();

    //         return ApiResponse::success(
    //             'Profiles retrieved successfully',
    //             $profiles
    //         );

    //     } catch (Exception $e) {
    //         return ApiResponse::error('Failed to fetch profiles');
    //     }
    // }



    public function show()
    {
        try {
            $user = Auth::user();

            // return $user->id;
            $profile = Profile::where('user_id', $user->id)->with('photos')->get();
            // return $profile;

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

                // 👇 images validation
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            DB::beginTransaction();

            // ✅ Create profile
            $data['user_id'] = auth()->id();
            $profile = Profile::create($data);
            auth()->user()->update([
                'profile_status' => 1
            ]);

            // ✅ Store images to public/photos
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('photos'), $filename);
                    $profile->photos()->create([
                        'image' => url('photos/' . $filename)
                    ]);
                }
            }

            DB::commit();

            // ✅ Load photos relation
            $profile->load('photos');

            return ApiResponse::success(
                'Profile created successfully',
                $profile,
                201
            );
        } catch (Exception $e) {

            DB::rollBack();

            return ApiResponse::error('Profile creation failed');
        }
    }

    public function update(Request $request)
    {
        try {
            $profile = auth()->user()->profile;

            if (!$profile) {
                return ApiResponse::error('Profile not found', 404);
            }

            $data = $request->validate([
                'name' => 'sometimes|string|max:255',
                'home_city' => 'nullable|string|max:255',
                'languages' => 'nullable|array',
                'interests' => 'nullable|array',
            ]);

            unset($data['username']); // ❌ still protected

            $profile->update($data);

            return ApiResponse::success(
                'Profile updated successfully',
                $profile
            );
        } catch (Exception $e) {
            return ApiResponse::error('Profile update failed');
        }
    }

    public function destroy()
    {
        try {
            $profile = auth()->user()->profile;

            if (!$profile) {
                return ApiResponse::error('Profile not found', 404);
            }

            $profile->delete();

            return ApiResponse::success(
                'Profile deleted successfully'
            );
        } catch (Exception $e) {
            return ApiResponse::error('Profile deletion failed');
        }
    }
}
