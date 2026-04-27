<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{


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

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:profiles,username',
                'home_school' => 'nullable|string|max:255',
                'abroad_school' => 'nullable|string|max:255',
                'home_city' => 'nullable|string|max:255',
                'current_city' => 'nullable|string|max:255',
                'languages' => 'nullable|array',
                'interests' => 'nullable|array',
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            if ($validator->fails()) {
                return ApiResponse::error($validator->errors(), 422);
            }

            $data = $validator->validated(); // ✅ FIX

            DB::beginTransaction();

            // ✅ Profile image
            if ($request->hasFile('profile_img')) {
                $file = $request->file('profile_img');
                $filename = time() . '_profile_' . $file->getClientOriginalName();
                $file->move(public_path('photos'), $filename);

                $data['profile_img'] = 'photos/' . $filename;
            }

            // ✅ Create profile
            $data['user_id'] = auth()->id();
            $profile = Profile::create($data);

            auth()->user()->update([
                'profile_status' => 1
            ]);

            // ✅ Multiple images
            if ($request->hasFile('images')) {
                $imageCount = 0;

                foreach ($request->file('images') as $image) {

                    if ($imageCount >= 3) break;

                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('photos'), $filename);

                    $profile->photos()->create([
                        'image' => 'photos/' . $filename
                    ]);

                    $imageCount++;
                }
            }

            DB::commit();

            $profile->load('photos');

            return ApiResponse::success(
                'Profile created successfully',
                $profile,
                201
            );
        } catch (Exception $e) {

            DB::rollBack();

            return ApiResponse::error($e->getMessage(), 500);
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
                'home_school' => 'nullable|string|max:255',
                'abroad_school' => 'nullable|string|max:255',
                'languages' => 'nullable|array',
                'interests' => 'nullable|array',
                'current_city' => 'nullable',
                'profile_visibility' => 'sometimes|in:friends,public,private',
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'profile_img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            unset($data['username']);

            if ($request->has('profile_visibility')) {
                $data['profile_visibility'] = $request->input('profile_visibility');
            }

            // ✅ Languages (replace completely)
            if ($request->has('languages')) {
                $data['languages'] = $request->input('languages', []);
            }

            // ✅ Interests (replace completely)
            if ($request->has('interests')) {
                $data['interests'] = $request->input('interests', []);
            }


            $profile->update($data);

            // ✅ Store new images to public/photos
            if ($request->hasFile('images')) {

                $existingPhotos = $profile->photos()->get();
                $existingCount = $existingPhotos->count();

                foreach ($request->file('images') as $index => $image) {

                    if (!$image)
                        continue;

                    // ✅ Block if updating a new index that would exceed 3
                    if (!isset($existingPhotos[$index]) && $existingCount >= 3) {
                        continue; // skip adding new ones beyond limit
                    }

                    $filename = time() . '_' . $image->getClientOriginalName();
                    $image->move(public_path('photos'), $filename);

                    if (isset($existingPhotos[$index])) {
                        // ✅ update existing image (index 0, 1, 2 only)
                        $existingPhotos[$index]->update([
                            'image' => 'photos/' . $filename
                        ]);
                    } else {
                        // ✅ create new only if under limit
                        $existingCount++;
                        $profile->photos()->create([
                            'image' => 'photos/' . $filename
                        ]);
                    }
                }
            }

            // ✅ Load photos relation
            $profile->load('photos');

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


    public function getProfileByVisibility($id)
    {
        try {
            $user = auth()->user();

            $userProfile = User::with('profile')->find($id);
            $profile = $userProfile->profile;

            if (!$profile) {
                return ApiResponse::error('Profile not found');
            }

            // ✅ PUBLIC
            if ($profile->profile_visibility === 'public') {
                return ApiResponse::success(
                    'Profile fetched successfully',
                    $profile
                );
            }

            // ✅ FRIENDS
            if ($profile->profile_visibility === 'friends') {

                $friend = \DB::table('friends')
                    ->where(function ($query) use ($user, $profile) {
                        $query->where('sender_id', $user->id)
                            ->where('receiver_id', $profile->user_id);
                    })
                    ->orWhere(function ($query) use ($user, $profile) {
                        $query->where('sender_id', $profile->user_id)
                            ->where('receiver_id', $user->id);
                    })
                    ->first();

                if (!$friend) {
                    return ApiResponse::success(
                        'You are not friends with this user'
                    );
                }

                // ✅ If request accepted
                if ($friend->accepted == 1) {
                    return ApiResponse::success(
                        'Profile fetched successfully',
                        $profile
                    );
                }

                // ✅ If request pending (accepted = 0)
                return ApiResponse::success(
                    'Friend request not accepted yet'
                );
            }

            // ✅ PRIVATE
            if ($profile->profile_visibility === 'private') {
                return ApiResponse::success(
                    'Profile is private'
                );
            }

            return ApiResponse::error('Invalid visibility type');

        } catch (Exception $e) {
            return ApiResponse::error('Something went wrong');
        }
    }
}
